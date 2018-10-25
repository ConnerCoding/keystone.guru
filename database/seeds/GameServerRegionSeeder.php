<?php

use Illuminate\Database\Seeder;
use App\Models\GameServerRegion;

class GameServerRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_rollback();
        $this->command->info('Adding known game server regions');

        $gameServerRegions = [
            // https://us.battle.net/forums/en/wow/topic/20745655899?page=1#post-1
            new GameServerRegion(['short' => 'na', 'name' => 'Americas', 'reset_day_offset' => 2, 'reset_time_offset_utc' => '15:00:00']),
            // http://wowreset.com/
            new GameServerRegion(['short' => 'eu', 'name' => 'Europe', 'reset_day_offset' => 3, 'reset_time_offset_utc' => '07:00:00']),
            // Copy paste from America, I couldn't find info for these regions
            new GameServerRegion(['short' => 'cn', 'name' => 'China', 'reset_day_offset' => 2, 'reset_time_offset_utc' => '15:00:00']),
            new GameServerRegion(['short' => 'tw', 'name' => 'Taiwan', 'reset_day_offset' => 2, 'reset_time_offset_utc' => '15:00:00']),
            new GameServerRegion(['short' => 'kr', 'name' => 'Korea', 'reset_day_offset' => 2, 'reset_time_offset_utc' => '15:00:00']),
        ];

        foreach ($gameServerRegions as $gameServerRegion) {
            /** @var $gameServerRegion \Illuminate\Database\Eloquent\Model */
            $gameServerRegion->save();
        }
    }

    private function _rollback()
    {
        DB::table('game_server_regions')->truncate();
    }
}