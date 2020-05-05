class KillZoneEnemySelection extends EnemySelection {
    constructor(map, sourceMapObject) {
        super(map, sourceMapObject);

        this.register('enemyselection:enemyselected', this, this._enemySelected.bind(this));
    }

    _enemySelected(enemySelectedEvent){
        console.assert(this instanceof KillZoneEnemySelection, 'this is not a KillZoneEnemySelection', this);

        /** @type KillZone sourceMapObject */
        this.sourceMapObject.enemySelected(enemySelectedEvent.data.enemy);
    }

    /**
     * Filters an enemy if it should be selected or not.
     * @param source MapObject
     * @param enemyCandidate Enemy
     * @returns {boolean}
     * @protected
     */
    _filter(source, enemyCandidate){
        console.assert(this instanceof KillZoneEnemySelection, 'this is not a KillZoneEnemySelection', this);
        console.assert(source instanceof KillZone, 'source is not a KillZone', source);
        console.assert(enemyCandidate instanceof Enemy, 'enemyCandidate is not an Enemy', enemyCandidate);
        return true; //enemyCandidate.getKillZone() === null || enemyCandidate.getKillZone().id === source.id;
    }

    /**
     * The way the icon looks when an enemy may be selected.
     * @protected
     */
    _getLayerIcon(){
        console.assert(this instanceof KillZoneEnemySelection, 'this is not a KillZoneEnemySelection', this);
        return LeafletKillZoneIconEditMode;
    }
}