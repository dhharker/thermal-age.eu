
// hat, cloak etc.
var wc = {
    local: {
        map: {}
        
    },
    loadGmapsAsync: function (callback) {
    return false; // debug
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=" + callback;
        document.body.appendChild(script);
        wc.loadGmapsAsync = function (callback) { return false; };
        return true;
    },
    lockHeightToParent: function (ele) {                    // <-- not sure if this method works at all *shrug*
        $('window').resize (ele, function (ele) {
            $me = $(ele);
            console.log ($me.parent().height());
            $me.height ($me.parent().height());
        }).resize();
    },
    init: function (ele) {
        ele = ele || '#SiteForm';
        var $me = $(ele);
        wc.lockHeightToParent ($('#wizardDetailColumn', $me));
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
        
        var map = new google.maps.Map(document.getElementById (mcid), myOptions);
        
        var marker = new google.maps.Marker({
            position: latlng, 
            map: map,
            draggable: true,
            title:""
        });
        
        wc.local.map = {
            mapContainer: $mc,
            map: map,
            marker: marker,
            resized: function () { wc.local.map.map.checkResize(); }
        };
        
    },
    initSiteForm: function (ele) {
        wc.loadGmapsAsync ("wc.initMap");
        wc.initLocationLookupButton ();
    },
    initLocationLookupButton: function () {
        $me = $("#FindLatLonBySiteNameButton");
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
}

$(wc.init());




