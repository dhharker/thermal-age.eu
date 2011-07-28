String.prototype.startsWith = function(str){
    return (this.indexOf(str) === 0);
}


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
        $('input[name$="_name\]"]').keyup (function () {
            $('input#ReactionMoleculeName').toTitleCase();
            $('input#ReactionReactionName').toTitleCase();
        }).trigger ('keyup');
        
        $('label[for="ReactionName"]').once ('widgetInited', function () { $(this).before ( 
            $('<div></div>')
                .addClass ('fg-buttonset fg-buttonset-single')
                .css ({
                        'float': 'right',
                        'margin': '0.2em'
                    })
                .html (
                $('<a>&dArr; Copy Above</a>')
                        .addClass ('fg-button ui-state-default ui-priority-secondary ui-corner-all')
                        .click (function () {
                            var combinedName = $('input#ReactionMoleculeName').val () + ' ' + $('input#ReactionReactionName').val ();
                            combinedName = jQuery.trim (combinedName);
                            $('input#ReactionName').val (combinedName);
                        }
                    )
                )
            )
        });
        
        $('select#ReactionSelect').once ('widgetInited', function () {
            $(this).change (function () {
                if ($(this).val () == '-1') {
                    $('#ReactionDetails').show ('slideDown');
                }
                else {
                    $('#ReactionDetails').hide ('slideUp');
                }
            })
            .trigger ('change');
        });
    }
}











