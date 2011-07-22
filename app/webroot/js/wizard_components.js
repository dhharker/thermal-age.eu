// hat, cloak etc.
var wc = {
    maps: {},
    
    init: function (sel) {
        var context = $(sel || '#wizardControlBox');
    },
    initMap: function (ele) {
        ele = ele || '#mapContainer';
        $mc = $(ele);
        latlng = new google.maps.LatLng(20, 0);
        myOptions = {
            zoom: 2,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var ni = $('.mapContainer').length + 1;
        var mcid = 'mapContainer_' + (ni);
        $mc.attr ('id', mcid).addClass ('mapContainer');
        wc.maps[ni] = $mc;
        var map = new google.maps.Map(document.getElementById (mcid), myOptions);
        
        var marker = new google.maps.Marker({
            position: latlng, 
            map: map,
            draggable: true,
            title:""
        });
    }
}

$(document).ready (function () {
    wc.init ();
});
