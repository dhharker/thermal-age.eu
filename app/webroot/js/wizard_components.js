var ADAPT_CALLBACK_2;
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
    damocles: function (ele) {
        wc.pullUp (ele);
        if ($('body').hasClass ('mobile-layout')) {
            wc.pullOut (ele);
        }
    },
    pullUp: function (ele) {
        $(ele).css ({
            'margin-top': (($(ele).height() * -1) - 2) + 'px'
        });
    },
    pullOut: function (ele) {
        $(ele).css ({
            'width': $(ele).parent().width() + 'px',
            'float': 'none'
        });
    },
    initProgressColumn: function (url) {
        var ele = '#wizardDetailColumn';
        // @todo when in mobile layout, the col does not resize with the window unless the progress bar is clicked
        $(ele)
            .resize (function () {wc.damocles ($('#wizardDetailColumn'));})
            .trigger('resize')
            .load (url, {}, function () { $('#wizardDetailColumn').resize(); })
            .hide()
        ;
        

    },
    initWizardProgressBar: function (ele, progress) {
        ele = ele || 'a#wizardProgressBar';
        progress = progress || 42;
        $(ele).once ('widgetInited', function () {
            $(this)
                .click (function () {
                    wc.damocles ($('#wizardDetailColumn'));
                    $('#wizardDetailColumn')
                        .slideToggle(250, function () {
                            $(this).resize();
                            wc.damocles ($(this));
                        })
                        .mouseleave (function (e) {
                            $('#wizardDetailColumn').not(':animated').hide('fade', 250);
                        })
                    ;
                    $(this).blur();
                    return false;
                })
                .find ('#wpbContainer').progressbar ({
                    value: progress,
                })
            ;
        });

    },
    init: function (ele) {
        ele = ele || '#wizardContainer';
        var $me = $(ele);
        

        
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
        var mapResizeHandler = function (i, width) {
            setTimeout ("google.maps.event.trigger(wc.local.map.map, 'resize');", 500);
        };
        $('body').data ('resizeHandler', mapResizeHandler);
        
        $mc.resize (mapResizeHandler);

        
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


($(document).ready(function () {
wc.init();
}));


