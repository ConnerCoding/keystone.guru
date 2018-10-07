<?php


class DungeonRouteMapCommentsRelationParser implements RelationParser
{
    /**
     * @param $modelClassName string
     * @return mixed
     */
    public function canParseModel($modelClassName)
    {
        return $modelClassName === '\App\Models\DungeonRoute';
    }

    /**
     * @param $name string
     * @param $value array
     * @return mixed
     */
    public function canParseRelation($name, $value)
    {
        return $name === 'mapcomments' && is_array($value);
    }

    /**
     * @param $modelClassName string
     * @param $modelData array
     * @param $name string
     * @param $value array
     * @return array
     */
    public function parseRelation($modelClassName, $modelData, $name, $value)
    {
        foreach ($value as $mapCommentData) {
            if ($mapCommentData['always_visible'] === 1) {
                // Not tied to a dungeon route!
                $mapCommentData['dungeon_route_id'] = -1;
            } else {
                // We now know the dungeon route ID, set it back to the map comment
                $mapCommentData['dungeon_route_id'] = $modelData['id'];
            }

            \App\Models\MapComment::insert($mapCommentData);
        }

        // Didn't really change anything so just return the value.
        return $modelData;
    }

}