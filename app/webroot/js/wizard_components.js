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
        //wc.loadGmapsAsync = function (callback) { return false; };
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
        progress = (progress == undefined) ? 0 : progress;
        $(ele).once ('widgetInited', function () {
            $(this)
                .click (function () { return false; })
                .mousedown (function () {
                    wc.damocles ($('#wizardDetailColumn'));
                    $('#wizardDetailColumn')
                        .slideToggle(250, function () {
                            $(this).resize();
                            wc.damocles ($(this));
                        })
                        .mouseleave (function (e) {
                            $('#wizardDetailColumn').not(':animated').hide('fade', 500);
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
        var ajaxTarget = '#wizardAjaxTarget';
        var afOpts = {
            beforeSubmit: function () {
                //$(ajaxTarget).not(':animated').hide ('fast');
                return true;
            },
            complete: function (a, b) {
                $(ajaxTarget).show ();
                initialiseTAUI (ajaxTarget);
                wc.init (ajaxTarget);
            },
            target: ajaxTarget
        };
        $('form:first', $me).not('.axfInited').ajaxForm (afOpts).submit (function () {
            return false;
        }).addClass ('axfInited');
    },
    initBurialForm: function (ele) {
        
    },
    initStorageForm: function (ele) {
        ele = ele || '#wizardContainer';
        var $me = $(ele);
    },
    initMapLoadButton: function () {
        $('#FindLatLonByMapButton')
            .attr ('href','')
            .click (function () {
                wc.loadGmapsAsync ("wc.initMap");
                $(this).hide ('fade', function () {
                    $(this).remove();
                }, 250);
                return false;
            })
        ;
    },
    initMap: function (ele) {
        $('#gMapGridBox').show ('blind', function () {
            $.smoothScroll ({
                scrollElement: $('#bg2'),
                scrollTarget: $('#gMapGridBox'),
                offset: -85
            });
            ele = ele || '#gMapContainer';
            var $mc = $(ele);
            var latlng = new google.maps.LatLng(parseFloat ($('#SiteLatDec').val()) || 0, parseFloat ($('#SiteLonDec').val()) || 0);
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
                title:"Site Location",
                animation: google.maps.Animation.DROP
            });
            
            var infoWindow = new google.maps.InfoWindow ({
                maxWidth: 250, // iPhone
            });
              
            wc.local.map = {
                mapContainer: $mc,
                map: gmap,
                marker: marker,
                info: infoWindow,
                showPlace: function (place) {
                    //console.log ("showPlace", this, place);
                    var latlng = new google.maps.LatLng(parseFloat (place.lat) || 0, parseFloat (place.lng) || 0);
                    var iwi = "rgsMapInfoBox-"+place.placeIndex;
                    
                    this.info.setPosition (latlng);
                    this.info.setContent ('<div id="'+iwi+'" style="height: 7em;">Loading...</div>');
                    
                    google.maps.event.addListener (this.info, 'domready', function () {
                        console.log ("domready", this, iwi);
                        ($('#'+iwi).text('')
                            .append ($('#rgsUseButton-'+place.placeIndex).clone(true)))
                            .append ($('<span class="placeTitle">'+place.placeTitle+'</span>'))
                            .append ($('<div style="float: right;" class="help"><span class="geo"><span class="latitude">'+place.hrLat+'</span> <span class="longitude">'+place.hrLng+'</span></span> <span class="elevation">'+place.elevation+'</span><div>'));
                    });
                    
                    this.info.open (this.map);
                    
                    return true;
                },
            };
            
            if (typeof wc.local.mapqueue == 'function') {
                wc.local.mapqueue ();
                wc.local.mapqueue = null;
            }
            
            var udm = function (event) {
                marker.setPosition (event.latLng);
                $('#SiteLatDec').val(event.latLng.lat())
                $('#SiteLonDec').val(event.latLng.lng())
                gmap.panTo (event.latLng);
            };

            google.maps.event.addListener (marker, 'dragend', udm);
            google.maps.event.addListener (gmap, 'click', udm);
            
            google.maps.event.addListener (gmap, 'resize', function () {
                gmap.setCenter (marker.getPosition());
            });

            var mapResizeHandler = function (i, width) {
                setTimeout ("google.maps.event.trigger(wc.local.map.map, 'resize');", 150);
            };
            $('body').data ('resizeHandler', mapResizeHandler);
            
            $mc.resize (mapResizeHandler);
        }, 150);
        
    },
    initSiteForm: function (ele) {
        wc.initMapLoadButton ();
        wc.initLocationLookupButton ();
    },
    initSiteChoiceButtons: function (scope) {
        if (!$(scope).attr('id') == 'reverseGeocodeResults') {
            return false;
        }
        var container = $(scope);
        initialiseTAUI (container);
            
        var eff = {
            effect: 'blind',
            duration: 300,
        };
        $('.rgsClearResultsButton', scope).click (function () {
            container.hide(eff, function () {
                $(this).html('');
            });
            return false;
        });
        
        var placeMap = function (event) {
            //console.log (event.data);
            
            // get or load map
            if (!wc.local.map.map) {
                wc.local.mapqueue = function () {
                    wc.local.map.showPlace (event.data);
                };
                $('#FindLatLonByMapButton').click();
            }
            else {
                wc.local.map.showPlace (event.data);
            }
            // scroll to place on map
            // open info window with name, coords and "use" button
            
            return false;
        };
        var placeUse = function (place, scope) {
            return false;
        };
        
        $('.placeSearchResultRow').each (function () {
            var place = {};
            var $row = $(this);
            //var $m = $('.rgsMapButton', scope);
            //var $u = $('.rgsUseButton', scope);
            var index = $('span.placeIndex', this).text();
            place = {
                placeIndex: index,
                placeTitle: $('span.placeTitle', this).text(),
                lat: $('span.geo span.latitude', this).attr('title'),
                lng: $('span.geo span.longitude', this).attr('title'),
                hrLat: $('span.geo span.latitude', this).text(),
                hrLng: $('span.geo span.longitude', this).text(),
                elevation: $('span.elevation', this).text(),
            };
            $row.data ('place', place);
            
            $('.rgsMapButton', $row).click (place, placeMap);
            $('.rgsUseButton', $row).click (place, placeUse);
            
            
            
        });
        
        
        
        

        return $e;
    },
    initLocationLookupButton: function () {
        $("#FindLatLonBySiteNameButton").click (function () {
            var res = $('#reverseGeocodeResults');
            var inp = $('input#SiteName');
            var minLength = 4; // Characters before a search will be made
            if (res.data ('lookupStatus') != 'loading' && inp.val().length >= minLength) {
                res
                    .loadingAnim()
                    .data ('lookupStatus', 'loading')
                    .load ('/wiz/place_search/', {place: inp.val()},
                        function () {
                            $(this)
                                .hide()
                                .data ('lookupStatus', 'ok')
                                .show ({effect: 'blind', duration: 300});
                            wc.initSiteChoiceButtons (this);
                    });
                    
            }
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
                    $('input#ReactionShowname').val ($('input#ReactionName').val());
            }).trigger ('keyup');
            
        $('select#ReactionSelect').once ('widgetInited', function () {
            $(this).change (function () {
                var delay = 500;
                if (!$(this).hasClass ('notOnLoad')) {
                    delay = 0;
                    $(this).addClass ('notOnLoad');
                }
                
                if ($(this).val () == '-1') {
                    if (delay == 0)
                        $('#ReactionDetails:hidden').show ();
                    else
                        $('#ReactionDetails:hidden').show ('blind', {
                            direction: 'vertical'
                        }, delay);
                }
                else {
                    $('#ReactionDetails:visible').hide ('blind', {
                        direction: 'vertical'
                    }, delay);
                    $('#ReactionShowname').val ($('option:selected', this).text());
                }
            }).change();

            if ($(this).val () == '-1') {
                $('#ReactionDetails:hidden').show ();
            }
        });
    }
};




