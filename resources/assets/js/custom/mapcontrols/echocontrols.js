class EchoControls extends MapControl {
    constructor(map) {
        super(map);
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);
        console.assert(map instanceof DungeonMap, 'map is not DungeonMap', map);

        let self = this;

        this._mapControl = null;

        this.map.echo.register('status:changed', this, this._onStatusChanged.bind(this));
        this.map.echo.register('user:add', this, this._onUserAdd.bind(this));
        this.map.echo.register('user:remove', this, this._onUserRemove.bind(this));
        this.map.echo.register('user:colorchanged', this, this._onUserColorChanged.bind(this));

        this.map.register('map:mapobjectgroupsfetchsuccess', this, this._onMapObjectGroupsFetchSuccess.bind(this));


        this.mapControlOptions = {
            onAdd: function (leafletMap) {
                let template = Handlebars.templates['map_controls_route_echo_template'];

                let data = getHandlebarsDefaultVariables();

                return $(template(data))[0];
            }
        };
    }

    _onStatusChanged(statusChangedEvent) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        this._setStatus(statusChangedEvent.data.newStatus);
    }

    _onUserAdd(userAddEvent) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        this._addUser(userAddEvent.data.user);
    }

    _onUserRemove(userRemoveEvent) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        this._removeUser(userRemoveEvent.data.user);
    }

    _onUserColorChanged(userRemoveEvent) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        this._applyUserColor(userRemoveEvent.data.user);
    }

    _onMapObjectGroupsFetchSuccess(fetchSuccessEvent) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        // Initial status while we wait for status changes
        this._setStatus(this.map.echo.getStatus());

        // We can only add existing users at this point because that's when our control is fully built.
        let existingUsers = this.map.echo.getUsers();
        for (let i = 0; i < existingUsers.length; i++) {
            this._addUser(existingUsers[i]);
        }
    }

    /**
     * Sets the status of the controls.
     * @param status string Either 'connecting' or 'connected'.
     * @private
     */
    _setStatus(status) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        let $connecting = $('.connecting');
        let $connected = $('.connected');
        switch (status) {
            case ECHO_STATUS_DISCONNECTED:
                $connecting.show();
                $connected.hide();
                break;
            case ECHO_STATUS_CONNECTED:
                $connecting.hide();
                $connected.show();
                break;
            default:
                console.error('Invalid echo state found!');
                break;
        }
    }

    /**
     * Adds a user to the status bar.
     * @param user Object
     * @private
     */
    _addUser(user) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);
        let template = Handlebars.templates['map_controls_route_echo_member_template'];

        // May be unset when not our own user, but this confuses handlebars
        user.self = user.name === this.map.options.username;

        let result = template($.extend({}, getHandlebarsDefaultVariables(), user));
        $('#edit_route_echo_members_container').append(
            result
        );

        // Update the color
        this._applyUserColor(user);

        refreshTooltips();
    }

    /**
     * Removes a user from the status bar.
     * @param user Object
     * @private
     */
    _removeUser(user) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);
        // Remove element
        $('.echo_user_' + user.name).remove();
    }

    /**
     * Sets the display color of a user.
     * @param user {object}
     * @private
     */
    _applyUserColor(user) {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        let styleID = 'style_color_' + user.name;
        // Delete any previous styles
        $('#' + styleID).remove();

        // Gets funky here, create a new CSS class with the user's color so we can direct some elements to use this class
        $("<style id='" + styleID + "'>")
            .prop('type', 'text/css')
            .html("\
            .user_color_" + user.name + " {\
                background-color: " + user.color + " !important\
            }")
            .appendTo('head');

        // Update the text color depending on the luminance
        let $user = $('.echo_user_' + user.name);
        if (isColorDark(user.color)) {
            $user.addClass('text-white');
            $user.removeClass('text-dark');
        } else {
            $user.addClass('text-dark');
            $user.removeClass('text-white');
        }
    }

    /**
     * Adds the Control to the current LeafletMap
     */
    addControl() {
        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);

        // Code for the statusbar
        L.Control.Statusbar = L.Control.extend(this.mapControlOptions);

        L.control.statusbar = function (opts) {
            return new L.Control.Statusbar(opts);
        };

        this._mapControl = L.control.statusbar({position: 'tophorizontalcenter'}).addTo(this.map.leafletMap);

        // Add the leaflet draw control to the sidebar
        let container = this._mapControl.getContainer();
        $(container).removeClass('leaflet-control');
        let $targetContainer = $('#route_echo_container');
        $targetContainer.append(container);
    }

    cleanup() {
        super.cleanup();

        this.map.echo.unregister('status:changed', this);
        this.map.echo.unregister('user:add', this);
        this.map.echo.unregister('user:remove', this);
        this.map.echo.unregister('user:colorchanged', this);

        this.map.unregister('map:mapobjectgroupsfetchsuccess', this);

        console.assert(this instanceof EchoControls, 'this is not EchoControls', this);
    }
}