<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $npc_id
 * @property int $whitelist_npc_id
 *
 * @property Npc $npc
 * @property Npc $whitelistnpc
 *
 * @mixin \Eloquent
 */
class NpcBolsteringWhitelist extends Model
{
    public $timestamps = false;

    protected $fillable = ['id', 'npc_id', 'whitelist_npc_id'];

    public $with = ['whitelistnpc'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    function npc()
    {
        return $this->belongsTo('App\Models\Npc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    function whitelistnpc()
    {
        // Without to prevent infinite recursion
        return $this->belongsTo('App\Models\Npc', 'whitelist_npc_id')->without('npcbolsteringwhitelists');
    }
}
