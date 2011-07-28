
// hat, cloak etc.
var wc = {
    local: {
        maps: {}
        
    },
    lockHeightToParent: function (ele) {
        $('window').resize (ele, function () {
            $me = $(ele);
            $me.css ('height', $me.parent().css ('height'));
        });
    },
    init: function (ele) {
        ele = ele || '#SiteForm';
        var $me = $(ele);
        wc.lockHeightToParent ($('#wizardDetailColumn', $me));
    },
    initMap: function (ele) {
        ele = ele || '#mapContainer';
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
        wc.local.maps[ni] = $mc;
        var map = new google.maps.Map(document.getElementById (mcid), myOptions);
        
        var marker = new google.maps.Marker({
            position: latlng, 
            map: map,
            draggable: true,
            title:""
        });
    },
    initSiteForm: function (ele) {
        wc.initMap ('#gMapContainer');
        
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

wc.init();




