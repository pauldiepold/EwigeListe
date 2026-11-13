#!/bin/bash
# Wartet auf die CI des Commits, startet bei Erfolg „Deploy Staging“ (mit Prod-Dump) und wartet aufs Ende.
# Aufruf: scripts/ci-deploy.sh [sha] [branch] – Standard: HEAD und aktueller Branch. Am besten im Hintergrund.
# Ausgabe: „CI ok … / DEPLOY ok …“ oder die letzten Logzeilen des Fehlschlags.
set -u
SHA=${1:-$(git rev-parse HEAD)}; BR=${2:-$(git branch --show-current)}
REPO=$(gh repo view --json nameWithOwner --jq .nameWithOwner)
for _ in $(seq 1 60); do
  ID=$(gh run list --branch "$BR" --workflow ci.yml --json databaseId,headSha --jq ".[] | select(.headSha|startswith(\"$SHA\")) | .databaseId" | head -1)
  [ -n "$ID" ] && break; sleep 10
done
[ -z "${ID:-}" ] && { echo "CI-Run für $SHA nicht gefunden (CI läuft nur mit offenem PR nach master)"; exit 1; }
gh run watch "$ID" --exit-status >/dev/null 2>&1 || { echo "CI FAILED ($ID)"; gh run view "$ID" --log-failed | tail -30; exit 1; }
echo "CI ok ($ID)"
gh api "repos/$REPO/actions/workflows/deploy-staging.yml/dispatches" -f ref="$BR" -f "inputs[sync_db]=yes"
sleep 15
DID=$(gh run list --workflow deploy-staging.yml --branch "$BR" --limit 1 --json databaseId --jq '.[0].databaseId')
gh run watch "$DID" --exit-status >/dev/null 2>&1 && echo "DEPLOY ok ($DID)" || { echo "DEPLOY FAILED ($DID)"; gh run view "$DID" --log-failed | tail -30; exit 1; }
