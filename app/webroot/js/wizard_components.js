
// hat, cloak etc.
var wc = {
    local: {
        map: {}
        
    },
    loadGmapsAsync: function (callback) {
//    return false; // debug
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=" + callback;
        document.body.appendChild(script);
        wc.loadGmapsAsync = function (callback) { return false; };
        return true;
    },
    initProgressColumn: function (ele) {                    // <-- not sure if this method works at all *shrug*
        var $me = $(ele);
        $me.click (function () {
            $(this).height ($(this).parent().height());
        });
    },
    init: function (ele) {
        ele = ele || '#wizardContainer';
        var $me = $(ele);
        wc.initProgressColumn ($('#wizardDetailColumn', $me));
    },
    initMap: function (ele) {
        ele = ele || '#gMapContainer';
        var $mc = $(ele);
        latlng = new google.maps.LatLng(20, 0);
        myOptions = {
            zoom: 2,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var ni = $('.mapContainer').length + 1;
        var mcid = 'mapContainer_' + (ni);
        $mc.attr ('id', mcid).addClass ('mapContainer');
        
        var gmap = new google.maps.Map(document.getElementById (mcid), myOptions);
        
        var marker = new google.maps.Marker({
            position: latlng, 
            map: gmap,
            draggable: true,
            title:""
        });
        
        wc.local.map = {
            mapContainer: $mc,
            map: gmap,
            marker: marker,
        };
        
        google.maps.event.addListener (marker, 'dragend', function (event) {
            marker.setPosition (event.latLng);
            gmap.panTo (event.latLng);
        });
        google.maps.event.addListener (gmap, 'resize', function () {
            gmap.setCenter (marker.getPosition());
        });
        var mapResizeHandler = function () {
            google.maps.event.trigger(gmap, 'resize');
        };

        $mc.resize (mapResizeHandler);
        $(window).resize (mapResizeHandler);
        
    },
    initSiteForm: function (ele) {
        wc.loadGmapsAsync ("wc.initMap");
        wc.initLocationLookupButton ();
    },
    initLocationLookupButton: function () {
        $("#FindLatLonBySiteNameButton").click (function () {
            console.log ("this button doesn't work yet, make it work.");
            return false;
        });
    },
    initReactionForm: function (ele) {

        $('input[name$="_name\]"]')
            .keyup (function () {
                $(this).toTitleCase();
            })
            .trigger ('keyup')
            //.filter ('#ReactionReactionName, #ReactionSubstrateName')
            .keyup(function () {
                    var combinedName = $('input#ReactionMoleculeName').val () + ' ' + $('input#ReactionReactionName').val ();
                    combinedName = jQuery.trim (combinedName);
                    var subs = jQuery.trim ($('input#ReactionSubstrateName').val ());
                    if (subs.length > 0)
                        combinedName += ' (' + subs + ')';

                    // takes care of editing not working on reload
                    if (!$('input#ReactionName').data ('lastSet') && $('input#ReactionName').val ().indexOf (combinedName) != -1) {
                        $('input#ReactionName').data ('lastSet', combinedName);
                    }
                    
                    // try and overwrite/replace/populate with a degree of sensitivity
                    if (combinedName.length > 0 && (
                        jQuery.trim ($('input#ReactionName').val ()).length == 0
                        || $('input#ReactionName').data ('lastSet') == $('input#ReactionName').val ()
                    )) {
                        $('input#ReactionName')
                            .val (combinedName)
                            .data ('lastSet', combinedName);
                    }
                    else if (
                        $('input#ReactionName').data ('lastSet') && 
                        $('input#ReactionName').data ('lastSet').length > 0
                    ) {
                        $('input#ReactionName')
                            .val ($('input#ReactionName').val ().replace ($('input#ReactionName').data ('lastSet'), combinedName))
                            .data ('lastSet', combinedName);
                    }
            }).trigger ('keyup');
            
        $('select#ReactionSelect').once ('widgetInited', function () {
            $(this).change (function () {
                if ($(this).val () == '-1') {
                    $('#ReactionDetails:hidden').show ('blind', {
                        direction: 'vertical'
                    }, 1000);
                }
                else {
                    $('#ReactionDetails:visible').hide ('blind', {
                        direction: 'vertical'
                    }, 1000);
                }
            })

            if ($(this).val () == '-1') {
                $('#ReactionDetails:hidden').show ();
            }
        });
    }
};


wc.init();


