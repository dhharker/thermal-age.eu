// hat, cloak etc.
var wc = {
    init: function (sel) {
        var context = $(sel || '#wizardControlBox');
    },
    initMap (ele) {
        latlng = new google.maps.LatLng(20, 0);
        myOptions = {
            zoom: 2,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var mcid = 'mapContainer_' + ($('.mapContainer').length + 1);
        $mc.attr ('id', mcid).addClass ('mapContainer');
        
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
