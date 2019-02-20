class FactionDisplayControls extends MapControl {
    constructor(map) {
        super(map);

        let self = this;

        this.map = map;

        this.mapControlOptions = {
            onAdd: function (leafletMap) {
                let source = $('#map_faction_display_controls_template').html();
                let template = Handlebars.compile(source);

                let data = {};

                // Build the status bar from the template
                self.domElement = $(template(data));

                self.domElement.find('.map_faction_display_control').bind('click', function (e) {
                    let root = $(e.currentTarget);
                    let allRadios = $('#map_faction_display_controls .radiobutton');
                    let checkbox = root.find('.radiobutton');
                    let checked = checkbox.hasClass('fas');

                    allRadios.removeClass('fas');
                    allRadios.addClass('far');
                    if (!checked) {
                        checkbox.removeClass('far');
                        checkbox.addClass('fas');
                    }

                    // Disable everything
                    $.each(allRadios, function (index, el) {
                        self._visibilityToggled($($(el).parent()).data('faction'), false);
                    });

                    // Re-enable the thing we're supposed to enable
                    self._visibilityToggled(root.data('faction'), true);

                    e.preventDefault();
                    return false;
                });

                self.domElement = self.domElement[0];

                return self.domElement;
            }
        };

        this.map.register('map:mapobjectgroupsfetchsuccess', this, function () {
            // Start as Horde visible
            self._visibilityToggled('horde', true);
            self._visibilityToggled('alliance', false);
        });
    }

    /**
     * Called whenever the visibility has been toggled to display another faction.
     * @param faction
     * @param visible
     * @private
     */
    _visibilityToggled(faction, visible) {
        console.assert(this instanceof FactionDisplayControls, this, 'this is not FactionDisplayControls');

        let enemyMapObjectGroups = [
            this.map.mapObjectGroupManager.getByName(MAP_OBJECT_GROUP_ENEMY),
            this.map.mapObjectGroupManager.getByName(MAP_OBJECT_GROUP_ENEMY_PACK),
            this.map.mapObjectGroupManager.getByName(MAP_OBJECT_GROUP_ENEMY_PATROL)
        ];

        // For each group
        $.each(enemyMapObjectGroups, function (i, enemyMapObjectGroup) {
            // For each object in the group
            $.each(enemyMapObjectGroup.objects, function (index, mapObject) {
                // One or either or any faction
                if (mapObject.faction === faction && mapObject.faction !== 'any') {
                    enemyMapObjectGroup.setMapObjectVisibility(mapObject, visible);
                }
            });
        });
    }

    /**
     * Adds the Control to the current LeafletMap
     */
    addControl() {
        console.assert(this instanceof FactionDisplayControls, this, 'this is not FactionDisplayControls');

        // Code for the domElement
        L.Control.domElement = L.Control.extend(this.mapControlOptions);

        L.control.domElement = function (opts) {
            return new L.Control.domElement(opts);
        };

        this._mapControl = L.control.domElement({position: 'topright'}).addTo(this.map.leafletMap);
    }

    cleanup() {
        super.cleanup();

        console.assert(this instanceof FactionDisplayControls, this, 'this is not FactionDisplayControls');

        this.map.unregister('map:mapobjectgroupsfetchsuccess', this);
    }
}
