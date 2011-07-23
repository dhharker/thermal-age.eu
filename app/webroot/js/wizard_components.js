// hat, cloak etc.
var wc = {
    local: {
        maps: {}
        
    },
    init: function (sel) {
        var context = $(sel || '#wizardControlBox');
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
            $('input#ReactionName').val (
                $('input#ReactionMoleculeName').val () + 
                    ' ' + 
                    $('input#ReactionReactionName').val ()
            );
        }).trigger ('keyup');
        
    }
}

$(document).ready (function () {
    wc.init ();
});
