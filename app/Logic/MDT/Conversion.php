<?php
/**
 * Created by PhpStorm.
 * User: wouterk
 * Date: 15-1-2019
 * Time: 16:34
 */

namespace App\Logic\MDT;

use App\Models\AffixGroup;
use App\Service\Season\SeasonService;

class Conversion
{
    private static $dungeonNameMapping = [
        'Atal\'Dazar' => 'AtalDazar',
        'Freehold' => 'Freehold',
        'Kings\' Rest' => 'KingsRest',
        'Shrine of the Storm' => 'ShrineoftheStorm',
        'Siege of Boralus' => 'SiegeofBoralus',
        'Temple of Sethraliss' => 'TempleofSethraliss',
        'The MOTHERLODE!!' => 'TheMotherlode',
        'The Underrot' => 'TheUnderrot',
        'Tol Dagor' => 'TolDagor',
        'Waycrest Manor' => 'WaycrestManor',
        'Mechagon: Junkyard' => 'MechagonIsland',
        'Mechagon: Workshop' => 'MechagonCity',
    ];

    /**
     * Rounds a number to the nearest two decimals.
     * @param $nr
     * @return float|int
     */
    private static function _round($nr)
    {
        return (int)($nr * 100) / 100;
    }

    /**
     * @param $dungeonName string
     * @return mixed Gets the MDT version of a dungeon name.
     */
    public static function hasMDTDungeonName($dungeonName)
    {
        return isset(self::$dungeonNameMapping[$dungeonName]);
    }

    /**
     * @param $dungeonName string
     * @return mixed Gets the MDT version of a dungeon name.
     */
    public static function getMDTDungeonName($dungeonName)
    {
        return self::$dungeonNameMapping[$dungeonName];
    }

    /**
     * Converts a MDT Dungeon ID to a Keystone.guru ID.
     * @param $mdtDungeonId int
     * @return int
     * @throws \Exception An exception if the found dungeon ID was incorrect/not supported.
     */
    public static function convertMDTDungeonID($mdtDungeonId)
    {
        // May be a double, convert it first
        $mdtDungeonId = (int)$mdtDungeonId;
        // BFA, there's 10 valid dungeons
        if ($mdtDungeonId >= 15 && $mdtDungeonId <= 26) {
            return $mdtDungeonId - 1;
        } else {
            throw new \Exception('Unsupported dungeon found.');
        }
    }

    /**
     * Converts an array with x/y keys set to an array with lat/lng set, converted to our own coordinate system.
     * @param $xy array
     * @return array
     */
    public static function convertMDTCoordinateToLatLng($xy)
    {
        // This seems to match my coordinate system for about 99%. Needs some more refinement but it should be very minor.
        // Yes I know about php's round() function but it gives floating point rounding errors.
        return ['lat' => self::_round($xy['y'] / 2.185), 'lng' => self::_round($xy['x'] / 2.185)];
    }

    /**
     * Converts an array with lat/lng keys set to an array with x/y set, converted to MDT coordinate system.
     * @param $latLng array
     * @return array
     */
    public static function convertLatLngToMDTCoordinate($latLng)
    {
        return ['y' => $latLng['lat'] * 2.185, 'x' => $latLng['lng'] * 2.185];
    }

    /**
     * Convert a MDT week to a matching affix group
     * @param SeasonService $seasonService
     * @param $mdtWeek int
     * @return AffixGroup
     */
    public static function convertWeekToAffixGroup(SeasonService $seasonService, int $mdtWeek)
    {
        // You can do this in a mathy way but tbh I can't be bothered right now.
        $weekMapping = [
            1 => 12,
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 4,
            6 => 5,
            7 => 6,
            8 => 7,
            9 => 8,
            10 => 9,
            11 => 10,
            12 => 11
        ];
        return AffixGroup::find($weekMapping[$mdtWeek] + (($seasonService->getSeasons()->count() - 1) * config('keystoneguru.season_iteration_affix_group_count')));
    }
}