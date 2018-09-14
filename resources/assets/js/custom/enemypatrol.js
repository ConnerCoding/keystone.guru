$(function () {
    L.Draw.EnemyPatrol = L.Draw.Polyline.extend({
        statics: {
            TYPE: 'enemypatrol'
        },
        initialize: function (map, options) {
            // Save the type so super can fire, need to do this as cannot do this.TYPE :(
            this.type = L.Draw.EnemyPatrol.TYPE;
            L.Draw.Feature.prototype.initialize.call(this, map, options);
        }
    });
});

class EnemyPatrol extends MapObject {
    constructor(map, layer) {
        super(map, layer);

        let self = this;

        this.label = 'EnemyPatrol';
        this.color = null;
        this.decorator = null;
        this.faction = 'any'; // sensible default
        // console.log(rand);
        // let hex = "#" + color.values[0].toString(16) + color.values[1].toString(16) + color.values[2].toString(16);

        this.setColor(c.map.enemypatrol.defaultColor);
        this.setSynced(true);

        this.register('synced', this, function () {
            self._rebuildDecorator();
        });
        this.register('object:deleted', this, function () {
            self._cleanDecorator();
        });
        this.map.register('map:beforerefresh', this, function () {
            self._cleanDecorator();
        });
    }

    /**
     * Cleans up the decorator of this route, removing it from the map.
     * @private
     */
    _cleanDecorator() {
        console.assert(this instanceof EnemyPatrol, this, 'this is not an EnemyPatrol');

        if (this.decorator !== null) {
            this.map.leafletMap.removeLayer(this.decorator);
        }
    }

    /**
     * Rebuild the decorators for this route (directional arrows etc).
     * @private
     */
    _rebuildDecorator() {
        console.assert(this instanceof EnemyPatrol, this, 'this is not an EnemyPatrol');

        this._cleanDecorator();

        this.decorator = L.polylineDecorator(this.layer, {
            patterns: [
                {
                    offset: 12,
                    repeat: 25,
                    symbol: L.Symbol.dash({
                        pixelSize: 10,
                        pathOptions: {color: 'darkred', weight: 2}
                    })
                },
                {
                    offset: 25,
                    repeat: 50,
                    symbol: L.Symbol.arrowHead({
                        pixelSize: 12,
                        pathOptions: {fillOpacity: 1, weight: 0, color: this.color}
                    })
                }
            ]
        });
        this.decorator.addTo(this.map.leafletMap);
    }

    setColor(color) {
        this.color = color;
        this.setColors({
            unsavedBorder: color,
            unsaved: color,

            editedBorder: color,
            edited: color,

            savedBorder: color,
            saved: color
        });
    }

    // To be overridden by any implementing classes
    onLayerInit() {
        console.assert(this instanceof EnemyPatrol, this, 'this is not an EnemyPatrol');
        super.onLayerInit();

        // Show a permanent tooltip for the pack's name
        // this.layer.bindTooltip(this.label, {permanent: true, offset: [0, 0]}).openTooltip();
    }

    getVertices() {
        let coordinates = this.layer.toGeoJSON().geometry.coordinates;
        let result = [];
        for (let i = 0; i < coordinates.length; i++) {
            result.push({lat: coordinates[i][0], lng: coordinates[i][1]});
        }
        return result;
    }
}