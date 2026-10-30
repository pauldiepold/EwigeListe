<?php

namespace Tests\Feature;

use App\Game;
use App\Group;
use App\Player;
use App\Profile;
use App\Round;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GroupStatisticsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_record_names_every_holder_with_a_link_to_their_profile(): void
    {
        $anna = $this->memberWith('Anna', ['games' => 12]);
        $ben = $this->memberWith('Ben', ['games' => 12]);
        $this->memberWith('Carla', ['games' => 3]);

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.records.0', [
                    'label' => 'Meiste Spiele:',
                    'value' => '12',
                    'holders' => [
                        ['name' => 'Anna', 'path' => "/profil/{$anna->id}/1"],
                        ['name' => 'Ben', 'path' => "/profil/{$ben->id}/1"],
                    ],
                ])
            );
    }

    #[Test]
    public function records_only_consider_profiles_above_their_minimum(): void
    {
        $this->memberWith('Anna', ['games' => 50, 'winrate' => 90]);
        $this->memberWith('Ben', ['games' => 51, 'winrate' => 52.5]);

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.records', fn ($records) => collect($records)
                    ->firstWhere('label', 'Höchste Gewinnrate:')['value'] === '52.5%')
            );
    }

    #[Test]
    public function a_record_without_qualifying_profiles_is_left_out(): void
    {
        $this->memberWith('Anna', ['games' => 5, 'soli' => 10, 'soloWinrate' => 100]);

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.records', fn ($records) => collect($records)
                    ->doesntContain('label', 'Höchste Solo-Gewinnrate:'))
            );
    }

    #[Test]
    public function guests_only_get_links_to_visible_profiles(): void
    {
        $this->memberWith('Anna', ['games' => 12]);
        $hidden = $this->memberWith('Ben', ['games' => 12]);
        $hidden->forceFill(['hide' => true])->save();

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.records.0.holders.0.path', fn (?string $path) => $path !== null)
                ->where('group.records.0.holders.1.path', null)
            );
    }

    #[Test]
    public function calculating_a_group_stores_its_statistics(): void
    {
        $group = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $this->roundWithGames($group, [4, 6]);
        $this->roundWithGames($group, [5]);
        $this->roundWithGames($group, []);

        $group->calculate();

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('group.stats', [
                    ['label' => 'Spiele insgesamt:', 'value' => '3'],
                    ['label' => '∅ Punktzahl pro Spiel:', 'value' => '5'],
                    ['label' => '∅ Anzahl Spiele pro Runde:', 'value' => '1'],
                ])
            );
    }

    /**
     * @param  array<string, int|float>  $statistics
     */
    private function memberWith(string $surname, array $statistics): Player
    {
        $player = Player::factory()->create(['surname' => $surname]);
        Group::findOrFail(Group::EWIGE_LISTE_ID)->addPlayer($player);

        Profile::where('player_id', $player->id)->update($statistics);

        return $player;
    }

    /**
     * @param  list<int>  $points
     */
    private function roundWithGames(Group $group, array $points): void
    {
        $round = Round::factory()->create();
        $round->groups()->attach($group);

        foreach ($points as $gamePoints) {
            Game::factory()->create(['round_id' => $round->id, 'points' => $gamePoints]);
        }
    }
}
