<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveWaycrestManorEnemiesToDifferentFloor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Exception
     */
    public function up()
    {
        // These IDs moved to a different floor altogether (because MDT). So we have to migrate all killzones that use
        // this pack (all of them) to the different floor to prevent breaking them all.
        $ids = [2076, 2077];

        // Select all killzones that need updating
        $killZones = DB::table('kill_zone_enemies')
            ->whereIn('enemy_id', $ids)
            ->groupBy('kill_zone_id')
            ->select('kill_zone_id')->get();

        foreach ($killZones as $killZoneData) {
            /** @var \App\Models\KillZone $killZone */
            $killZone = \App\Models\KillZone::find($killZoneData->kill_zone_id);

            // Check if the killzone has other enemies selected, if so, don't move it and just remove the moved enemies
            $hasOtherEnemies = false;
            foreach ($killZone->enemies as $kzEnemy) {
                /** @var $kzEnemy \App\Models\KillZoneEnemy */
                if (!in_array($kzEnemy->id, $ids)) {
                    $hasOtherEnemies = true;
                }
            }

            if ($hasOtherEnemies) {
                // Remove the enemies that were moved
                foreach ($killZone->enemies as $kzEnemy) {
                    /** @var $kzEnemy \App\Models\KillZoneEnemy */
                    if (in_array($kzEnemy->id, $ids)) {
                        $kzEnemy->delete();
                    }
                }
            } else {
                // Move the killzone to the next floor and set a decent position for it
                $killZone->floor_id = 61;
                $killZone->lat = -60;
                $killZone->lng = 122.38;
                $killZone->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
