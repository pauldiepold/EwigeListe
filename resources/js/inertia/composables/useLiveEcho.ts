import { onUnmounted, ref, watch, type Ref } from 'vue';
import { useEchoPresence } from '@laravel/echo-vue';
import { useEchoConnection, usePresenceMembers } from '@/composables/echoConnection';
import type { LiveGamePush } from '@/types/live';

/** Wer online ist, wie `routes/channels.php` ihn für `round.{id}` beschreibt (`id` ist die Spieler-ID). */
export type LiveEchoMember = {
  id: number;
  name: string;
  avatar_path: string | null;
};

export type UseLiveEchoOptions = {
  roundId: number;
  liveRoundId: number;
  /** Spieler-ID des Betrachters; nur für Mitspieler des Spiels ist sie die ID im Channel. */
  playerId: number;
  /**
   * Spielt der Betrachter im aktuellen Spiel mit? Dann hört er auf seinen privaten Channel
   * (`LiveGameDataBroadcasted` mit Hand), sonst auf den der Zuschauer (`...Inaktiv`). Wechselt der
   * Wert zwischen zwei Spielen, wechselt auch der Channel.
   */
  isActivePlayer: Readonly<Ref<boolean>>;
  /** Ein Push des Spiels; geht direkt in den lokalen Zustand. */
  onPush: (push: LiveGamePush) => void;
  /** `RoundUpdated`: Ein Spiel wurde gestartet oder beendet, die Props müssen neu geladen werden. */
  onRoundUpdated: () => void;
  /** Ein Mitspieler hat „bereit“ geflüstert. */
  onReady: (playerId: number) => void;
  /** Jemand hat den Presence-Channel verlassen; der Bereit-Check fängt von vorn an. */
  onMemberLeft: () => void;
};

export type UseLiveEchoReturn = {
  /** Wer gerade auf der Live-Seite dieser Runde ist (Spieler und Zuschauer). */
  onlineMembers: Ref<LiveEchoMember[]>;
  /** Flüstert den Mitspielern, dass ich bereit bin. */
  sendReady: (playerId: number) => void;
};

/**
 * Die Echo-Anbindung der Live-Seite und die Test-Seam für Pushes: Tests ersetzen dieses Modul
 * (`vi.mock('@/composables/useLiveEcho')`) und rufen die übergebenen Callbacks selbst auf.
 *
 * - Presence-Channel `round.{id}`: Online-Liste, `RoundUpdated`, Whisper `ready`.
 * - Private-Channel `liveRound.{id}.{player}` (Mitspieler) bzw. `liveRound.{id}` (Zuschauer).
 *
 * Alle Channels sind nur offen, solange die Seite offen ist.
 */
export function useLiveEcho(options: UseLiveEchoOptions): UseLiveEchoReturn {
  const connection = useEchoConnection();

  if (connection === null) {
    return { onlineMembers: ref<LiveEchoMember[]>([]), sendReady: () => undefined };
  }

  const presence = useEchoPresence<unknown, 'pusher'>(
    `round.${options.roundId}`,
    'RoundUpdated',
    options.onRoundUpdated,
  ).channel();

  const onlineMembers = usePresenceMembers<LiveEchoMember>(presence, () => options.onMemberLeft());

  // Auf dem geteilten Channel (Runde und Live-Seite) bleiben Listener der alten Seite sonst hängen.
  const onReady = (message: { id: number }): void => options.onReady(message.id);
  presence.listenForWhisper('ready', onReady);
  onUnmounted(() => presence.stopListeningForWhisper('ready', onReady));

  watch(
    options.isActivePlayer,
    (isActivePlayer, _previous, onCleanup) => {
      const name = isActivePlayer
        ? `liveRound.${options.liveRoundId}.${options.playerId}`
        : `liveRound.${options.liveRoundId}`;
      const event = isActivePlayer ? 'LiveGameDataBroadcasted' : 'LiveGameDataBroadcastedInaktiv';

      connection.private(name).listen(event, options.onPush);
      onCleanup(() => connection.leaveChannel(`private-${name}`));
    },
    { immediate: true },
  );

  return {
    onlineMembers,
    sendReady: (playerId) => {
      presence.whisper('ready', { id: playerId });
    },
  };
}
