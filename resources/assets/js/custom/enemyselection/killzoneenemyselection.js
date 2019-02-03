class KillZoneEnemySelection extends EnemySelection {
    constructor(map, sourceMapObject) {
        super(map, sourceMapObject);
    }

    /**
     * Filters an enemy if it should be selected or not.
     * @param source MapObject
     * @param enemyCandidate Enemy
     * @returns {boolean}
     * @protected
     */
    _filter(source, enemyCandidate){
        console.assert(source instanceof KillZone, source, 'source is not a KillZone');
        console.assert(enemyCandidate instanceof Enemy, enemyCandidate, 'enemyCandidate is not an Enemy');
        return enemyCandidate.kill_zone_id <= 0 || enemyCandidate.kill_zone_id === source.id;
    }

    /**
     * The way the icon looks when an enemy may be selected.
     * @protected
     */
    _getLayerIcon(){
        return LeafletKillZoneIconSelected;
    }
}