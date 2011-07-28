
// hat, cloak etc.
var wc = {
    local: {
        maps: {}
        
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
        ele = ele || 'form#ReactionForm';
        var $form = $(ele);
        $('input[name$="_name\]"]')
            .keyup (function () {
                $(this).toTitleCase();
            })
            .trigger ('keyup')
            //.filter ('#ReactionReactionName, #ReactionSubstrateName')
            .blur(function () {
                    var combinedName = $('input#ReactionMoleculeName').val () + ' ' + $('input#ReactionReactionName').val ();
                    combinedName = jQuery.trim (combinedName);
                    var subs = jQuery.trim ($('input#ReactionSubstrateName').val ());
                    if (subs.length > 0)
                        combinedName += ' (' + subs + ')';
                    
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
                }
            );
        
        /*$('label[for="ReactionName"]').once ('widgetInited', function () { $(this).before ( 
            $('<div></div>')
                .addClass ('fg-buttonset fg-buttonset-single')
                .css ({
                        'float': 'right',
                        'margin': '0.2em'
                })
                .html (
                $('<a>&dArr; Copy Above</a>')
                    .attr ('id', 'copyNamesDownButton')
                    .addClass ('fg-button ui-state-default ui-priority-secondary ui-corner-all')
                    .click (function () {
                        var combinedName = $('input#ReactionMoleculeName').val () + ' ' + $('input#ReactionReactionName').val ();
                        combinedName = jQuery.trim (combinedName);
                        var subs = jQuery.trim ($('input#ReactionSubstrateName').val ());
                        if (subs.length > 0)
                            combinedName += ' (' + subs + ')';

                        if (combinedName.length > 0 && (
                            jQuery.trim ($('input#ReactionName').val ()).length == 0
                            || $('input#ReactionName').data ('lastSet') == $('input#ReactionName').val ()
                        )) {
                                                console.log (1);
                            $('input#ReactionName')
                                .val (combinedName)
                                .data ('lastSet', combinedName);
                        }
                        else if (
                            $('input#ReactionName').data ('lastSet') && $('input#ReactionName').data ('lastSet').length > 0
                        ) {
                                                console.log (2);
                            $('input#ReactionName')
                                .val ($('input#ReactionName').val ().replace ($('input#ReactionName').data ('lastSet'), combinedName))
                                .data ('lastSet', combinedName);
                        }
                        else {
                                                console.log (3);
                            $('input#ReactionName')
                                .val (combinedName)
                                .data ('lastSet', combinedName);
                        }
                        
                        
                    })
                )
            )
        });*/
        
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











