<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $floor_id
 * @property int $enemy_id
 * @property int $polyline_id
 * @property string $faction
 * @property \App\Models\Floor $floor
 * @property \App\Models\Enemy $enemy
 * @property \App\Models\Polyline $polyline
 */
class EnemyPatrol extends Model
{
    public $visible = ['id', 'faction', 'polyline'];
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function floor()
    {
        return $this->belongsTo('App\Models\Floor');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function enemy()
    {
        return $this->belongsTo('App\Models\Enemy');
    }

    /**
     * Get the dungeon route that this brushline is attached to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    function polyline()
    {
        return $this->hasOne('App\Models\Polyline', 'model_id')->where('model_class', get_class($this));
    }
}
