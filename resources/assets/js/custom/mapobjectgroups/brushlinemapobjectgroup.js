class BrushlineMapObjectGroup extends MapObjectGroup {
    constructor(manager, editable) {
        super(manager, MAP_OBJECT_GROUP_BRUSHLINE, 'brushline', editable);

        let self = this;

        this.title = 'Hide/show brushlines';
        this.fa_class = 'fa-paint-brush';

        if (this.manager.map.options.echo) {
            window.Echo.join(this.manager.map.options.appType + '-route-edit.' + getState().getDungeonRoute().publicKey)
                .listen('.brushline-changed', (e) => {
                    if (e.brushline.floor_id === getState().getCurrentFloor().id) {
                        self._restoreObject(e.brushline, e.user);
                    }
                })
                .listen('.brushline-deleted', (e) => {
                    let mapObject = self.findMapObjectById(e.id);
                    if (mapObject !== null) {
                        mapObject.localDelete();
                        self._showDeletedFromEcho(mapObject, e.user);
                    }
                });
        }
    }

    _createObject(layer) {
        console.assert(this instanceof BrushlineMapObjectGroup, 'this is not an BrushlineMapObjectGroup', this);

        return new Brushline(this.manager.map, layer);
    }

    /**
     * @inheritDoc
     */
    _restoreObject(remoteMapObject, username = null) {
        console.assert(this instanceof BrushlineMapObjectGroup, 'this is not an BrushlineMapObjectGroup', this);

        // Fetch the existing path if it exists
        let brushline = this.findMapObjectById(remoteMapObject.id);

        // Create the polyline first
        let polyline = remoteMapObject.polyline;
        let vertices = JSON.parse(polyline.vertices_json);

        let points = [];
        for (let j = 0; j < vertices.length; j++) {
            let vertex = vertices[j];
            points.push([vertex.lat, vertex.lng]);
        }

        // Only create a new one if it's new for us
        if (brushline === null) {
            let layer = L.polyline(points);
            brushline = this.createNew(layer);
        } else {
            // Update latlngs
            brushline.layer.setLatLngs(points);
        }

        brushline.loadRemoteMapObject(remoteMapObject);
        brushline.loadRemoteMapObject(remoteMapObject.polyline);

        // We just downloaded the brushline, make it synced
        brushline.setSynced(true);

        // Show echo notification or not
        this._showReceivedFromEcho(brushline, username);

        return brushline;
    }
}