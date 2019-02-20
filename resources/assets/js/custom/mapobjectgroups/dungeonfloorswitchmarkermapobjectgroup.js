class DungeonFloorSwitchMarkerMapObjectGroup extends MapObjectGroup {
    constructor(manager, name, editable) {
        super(manager, name, editable);

        this.title = 'Hide/show floor switch markers';
        this.fa_class = 'fa-door-open';
    }

    _createObject(layer) {
        console.assert(this instanceof DungeonFloorSwitchMarkerMapObjectGroup, 'this is not an DungeonFloorSwitchMarkerMapObjectGroup');

        if (isAdmin) {
            return new AdminDungeonFloorSwitchMarker(this.manager.map, layer);
        } else {
            return new DungeonFloorSwitchMarker(this.manager.map, layer);
        }
    }

    _fetchSuccess(response) {
        super._fetchSuccess(response);
        // no super call required
        console.assert(this instanceof DungeonFloorSwitchMarkerMapObjectGroup, this, 'this is not a DungeonFloorSwitchMarkerMapObjectGroup');

        let floorSwitchMarkers = response.dungeonfloorswitchmarker;

        // Now draw the enemies on the map
        for (let index in floorSwitchMarkers) {
            if (floorSwitchMarkers.hasOwnProperty(index)) {
                let remoteDungeonFloorSwitchMarker = floorSwitchMarkers[index];

                let layer = new LeafletDungeonFloorSwitchMarker();
                layer.setLatLng(L.latLng(remoteDungeonFloorSwitchMarker.lat, remoteDungeonFloorSwitchMarker.lng));

                let dungeonFloorSwitchMarker = this.createNew(layer);
                dungeonFloorSwitchMarker.id = remoteDungeonFloorSwitchMarker.id;
                dungeonFloorSwitchMarker.floor_id = remoteDungeonFloorSwitchMarker.floor_id;
                dungeonFloorSwitchMarker.target_floor_id = remoteDungeonFloorSwitchMarker.target_floor_id;

                // We just downloaded the floor switch marker, it's synced alright!
                dungeonFloorSwitchMarker.setSynced(true);
            }
        }
    }
}