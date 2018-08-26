class EnemyPatrolMapObjectGroup extends MapObjectGroup {
    constructor(map, name, classname) {
        super(map, name);

        this.classname = classname;
        this.title = 'Hide/show enemy patrol routes';
        this.fa_class = 'fa-exchange-alt';
    }

    _createObject(layer){
        console.assert(this instanceof EnemyPatrolMapObjectGroup, 'this is not an EnemyPatrolMapObjectGroup');

        switch (this.classname) {
            case "AdminEnemyPatrol":
                return new AdminEnemyPatrol(this.map, layer);
            default:
                return new EnemyPatrol(this.map, layer);
        }
    }


    fetchFromServer(floor) {
        // no super call required
        console.assert(this instanceof EnemyPatrolMapObjectGroup, this, 'this is not a EnemyPatrolMapObjectGroup');

        let self = this;

        $.ajax({
            type: 'GET',
            url: '/api/v1/enemypatrols',
            dataType: 'json',
            data: {
                floor_id: floor.id
            },
            success: function (json) {
                // Remove any layers that were added before
                self._removeObjectsFromLayer.call(self);

                // Now draw the patrols on the map
                for (let index in json) {
                    if (json.hasOwnProperty(index)) {
                        let points = [];
                        let remoteEnemyPatrol = json[index];

                        for (let j = 0; j < remoteEnemyPatrol.vertices.length; j++) {
                            let vertex = remoteEnemyPatrol.vertices[j];
                            points.push([vertex.lng, vertex.lat]); // dunno why it must be lng/lat
                        }

                        let layer = L.polyline(points);

                        let enemyPatrol = self.createNew(layer);
                        enemyPatrol.id = remoteEnemyPatrol.id;
                        enemyPatrol.enemy_id = remoteEnemyPatrol.enemy_id;
                        // We just downloaded the enemy pack, it's synced alright!
                        enemyPatrol.setSynced(true);
                    }
                }
            }
        });
    }
}