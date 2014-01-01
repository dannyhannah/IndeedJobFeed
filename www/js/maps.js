    /**
     * A distance widget that will display a circle that can be resized and will
     * provide the radius in km.
     *
     * @param {google.maps.Map} map The map to attach to.
     *
     * @constructor
     */
    function DistanceWidget(map) {
        this.set('map', map);
        this.set('position', map.getCenter());
        var marker = new google.maps.Marker({
            draggable: true,
            title: 'Move me!'
        });
        // Bind the marker map property to the DistanceWidget map property
        marker.bindTo('map', this);
        // Bind the marker position property to the DistanceWidget position
        // property
        marker.bindTo('position', this);
        // Create a new radius widget
        var radiusWidget = new RadiusWidget();
        // Bind the radiusWidget map to the DistanceWidget map
        radiusWidget.bindTo('map', this);
        // Bind the radiusWidget center to the DistanceWidget position
        radiusWidget.bindTo('center', this, 'position');
        // Bind to the radiusWidgets' distance property
        this.bindTo('distance', radiusWidget);
        // Bind to the radiusWidgets' bounds property
        this.bindTo('bounds', radiusWidget);

        google.maps.event.addListener(marker, 'dragend', function(event) {
            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(event.latLng.lat(), event.latLng.lng());
            geocoder.geocode({
                'latLng': latlng
            }, function(results, status) {

                var arrAddress = results[0].address_components;
                // iterate through address_component array
                $.each(arrAddress, function (i, address_component) {

                    if (address_component.types[0] == "postal_town") // locality type
                        document.getElementById("locationName").value = address_component.long_name;
                        $('.longlat input#locationName').trigger('change');
                    });
                delete window.results;

            });

            document.getElementById("latbox").value = event.latLng.lat();
            document.getElementById("lngbox").value = event.latLng.lng();

            $('.longlat input#latbox').trigger('change');
        });
    }
    DistanceWidget.prototype = new google.maps.MVCObject();
    
    /**
     * A radius widget that add a circle to a map and centers on a marker.
     *
     * @constructor
     */
    function RadiusWidget() {
        var circle = new google.maps.Circle({
            strokeWeight: 1,
            fillColor: "#A2DFDF"
        });
        // Set the distance property value, default to 50km.
        this.set('distance', userrad );
        // Bind the RadiusWidget bounds property to the circle bounds property.
        this.bindTo('bounds', circle);
        // Bind the circle center to the RadiusWidget center property
        circle.bindTo('center', this);
        // Bind the circle map to the RadiusWidget map
        circle.bindTo('map', this);
        // Bind the circle radius property to the RadiusWidget radius property
        circle.bindTo('radius', this);
        // Add the sizer marker
        this.addSizer_();
    }
    RadiusWidget.prototype = new google.maps.MVCObject();
    /**
     * Update the radius when the distance has changed.
     */
    RadiusWidget.prototype.distance_changed = function() {
        this.set('radius', this.get('distance') * 1000);
    };
    /**
     * Add the sizer marker to the map.
     *
     * @private
     */
    RadiusWidget.prototype.addSizer_ = function() {
        var sizer = new google.maps.Marker({
            draggable: true,
            title: 'Drag me!'
        });
        sizer.bindTo('map', this);
        sizer.bindTo('position', this, 'sizer_position');
        var me = this;
        google.maps.event.addListener(sizer, 'drag', function() {
            // Set the circle distance (radius)
            me.setDistance();
        });
        google.maps.event.addListener(sizer, 'dragend', function() {
            document.getElementById("rad").value = me.distance;
            $('.longlat input#rad').trigger('change');
        });
    };
    /**
     * Update the center of the circle and position the sizer back on the line.
     *
     * Position is bound to the DistanceWidget so this is expected to change when
     * the position of the distance widget is changed.
     */
    RadiusWidget.prototype.center_changed = function() {
        var bounds = this.get('bounds');
        // Bounds might not always be set so check that it exists first.
        if (bounds) {
            var lng = bounds.getNorthEast().lng();
            // Put the sizer at center, right on the circle.
            var position = new google.maps.LatLng(this.get('center').lat(), lng);
            this.set('sizer_position', position);
        }
    };
    /**
     * Calculates the distance between two latlng points in km.
     * @see http://www.movable-type.co.uk/scripts/latlong.html
     *
     * @param {google.maps.LatLng} p1 The first lat lng point.
     * @param {google.maps.LatLng} p2 The second lat lng point.
     * @return {number} The distance between the two points in km.
     * @private
     */
    RadiusWidget.prototype.distanceBetweenPoints_ = function(p1, p2) {
        if (!p1 || !p2) {
            return 0;
        }
        var R = 6371; // Radius of the Earth in km
        var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
        var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return d;
    };
    /**
     * Set the distance of the circle based on the position of the sizer.
     */
    RadiusWidget.prototype.setDistance = function() {
        // As the sizer is being dragged, its position changes.  Because the
        // RadiusWidget's sizer_position is bound to the sizer's position, it will
        // change as well.
        var pos = this.get('sizer_position');
        var center = this.get('center');
        var distance = this.distanceBetweenPoints_(center, pos);
        // Set the distance property for any objects that are bound to it
        this.set('distance', distance);
    };

    function init() {
        var mapDiv = document.getElementById('map-canvas');
        var map = new google.maps.Map(mapDiv, {
            center: new google.maps.LatLng( userlat , userlong ),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        //console.log(map);
        var distanceWidget = new DistanceWidget(map);
    }
    google.maps.event.addDomListener(window, 'load', init);

$(window).bind("load", function() {

    $("form.longlat, form.col1").change(function(event) {
        $(document).ajaxStart(function() {
            $('.loading').show();
        });

        $.ajax({
            url: "/user/updateMap",
            context: document.body,
            type: "post",
            data: $(this).find("input[type='text'],input:checked, select")
        }).done(function(e) {
            console.log(e);
            var body = $(e).find(".users-listing");
            $(".users-listing").replaceWith(body);
            $('.loading').hide();
        });
        return false;
    });
});