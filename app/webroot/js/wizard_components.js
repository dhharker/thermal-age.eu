var ADAPT_CALLBACK_2;
// hat, cloak etc.
var wc = {
    local: {
        map: {},
        burial: {
            nextIndex: 1,
        }
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
        
        $('textarea[id$=Description]', $me).not ('.inited')
            .each (function () {
                $(this).parentsUntil ('div.input').parent().wrapInner ('<div class="hidden_description"></div>');
            }).addClass ('inited');
        
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
        $('form:first', $me).not('.noAjax').not('.axfInited').ajaxForm (afOpts).submit (function () {
            return false;
        }).addClass ('axfInited');
        $('select.make-chosen').chosen ({
            //style: 'margin-right: .85em'
        })
        .removeClass('make-chosen')
        .addClass('made-chosen');
    },
    initLayerDeleteButtons: function (scope) {
        scope = scope || '#wizardContainer';
        $('.deleteLayerButton', scope).not('.inited').click (function () {
            $(this).parentsUntil('li.burialLayer').parent().hide({
                effect: 'blind',
                duration: 300,
            }, function () {
                $(this).remove();
                wc.reorderLayers();
            });
            return false;
        }).addClass('inited');
    },
    initAutoAd2BpFields: function (scope) {
        scope = scope || '#wizardContainer';

        $('.makeInputAd input[name$="_ybp]"]', scope).not ('.inited').each (function () {
            var $i = $(this);
            var bpName = $i.attr('name');
            var adName = bpName.substr (0, bpName.length - 3) + 'ad]';
            var newInp = $('<input type="hidden" name="'+bpName+'" />');
            var isBp = $('<span class="isBp ui-corner-all" style="padding: .2em;"></span>');
            $i.attr('name', adName).attr ('autocomplete', 'off')
                .parentsUntil('form').parent().attr ('autocomplete', 'off');
            newInp.insertAfter ($i);
            isBp.insertAfter ($i);
            // on load, the value will be in bp, so put it in the bp field
            var ival = $i.val();
            if (!(isNaN (ival) || '' == ival || ival.length == 0)) {
                newInp.val($i.val());
                $i.val (useful.bp2ad (newInp.val()));
            }
            $i.keyup (function () {
                    var $this = $(this);
                    var lv = $this.data('last_value');
                    $this.data('last_value', $this.val())
                    if (lv != $this.val()) {
                        var ival = $i.val();
                        //newInp.val (useful.ad2bp (ival));
                        if (isNaN (ival) || '' == ival || ival.length == 0) {
                            $(this).siblings ('span.isBp').text ('(must be a number; years AD)').not(':animated').effect ('highlight', {}, 3000);
                        }
                        else {
                            newInp.val (useful.ad2bp (ival));
                            $(this).siblings ('span.isBp').text (' = ' + newInp.val() + ' bp').not(':animated').effect ('highlight', {}, 3000);
                        }
                        return true;
                    }

                    
                });
            $i.keyup();
        }).addClass ('inited');
    },
    initBurialForm: function (scope) {
        scope = scope || '#wizardContainer';
        
        this.initAutoAd2BpFields (scope);
        
        var list = $( "#burialLayersList > ul", scope);
        
        var template = $('li.burialLayer:first', list);
        list.data ('liTemplate', template);
        template.remove();
        
        if (wc) wc.reorderLayers = function (scope) {
            scope = scope || '#wizardContainer';
            var layers = $('input.layerOrder', scope);
            wc.local.burial.layersindex = 0;
            layers.each (function () {
                $(this).val(wc.local.burial.layersindex);
                wc.local.burial.layersindex++;
            });
            $('input#BurialNumLayers').val(wc.local.burial.layersindex);
        };
        
        list.sortable({
            axis: 'y',
            containment: 'div#burialLayersList',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            handle: '.sort-handle',
            update: function (event, ui) {
                if (wc) wc.reorderLayers (list);
            },
            create: function (event, ui) {
                if (wc) wc.reorderLayers (list);
            },
            revert: 100
        });
        
        this.initLayerDeleteButtons (scope);
        this.reorderLayers (scope);
        this.local.burial.nextIndex = $('input#BurialNumLayers', scope).val();
        
        $('#addSoilLayerButton', scope).click(function () {
            var template = list.data ('liTemplate').clone();
            var attrs = ['id', 'name', 'for'];
            var nid = wc.local.burial.nextIndex;
            wc.local.burial.nextIndex++;
            var newItem = $(template);
            for (p in attrs) {
                var atr = attrs[p];
                newItem.find('input, label, select').each (function () {
                    var $this = $(this);
                    if (typeof $this.attr(atr) != 'undefined') {
                        var val = $this.attr (atr);
                        val = val.replace ('-1', nid)
                        $this.attr (atr, val);
                    }
                });
            }

            $(newItem).appendTo (list).show ({
                effect: 'blind',
                duration: 300,
            });
            initialiseTAUI (list);
            wc.initLayerDeleteButtons (list);
            wc.reorderLayers (list);
            return false;
        });
        

        
    },
    initStorageForm: function (scope) {
        scope = scope || '#wizardContainer';
        //var $me = $(scope);
        
        this.initAutoAd2BpFields (scope);
    },
    initMapLoadButton: function () {
        $('#FindLatLonByMapButton')
            .attr ('href','')
            .click (function () {
                wc.loadGmapsAsync ("wc.initMap");
                /*$(this).hide ('fade', function () {
                    $(this).remove();
                }, 250);*/
                $(this)
                    .attr ('id', 'CentreMapButton')
                    .unbind ('click')
                    .click (function () {
                        wc.local.map.fromBoxen ();
                        return false;
                    })
                    .text ('Centre Map')
                    .removeClass ('ui-corner-all')
                    .addClass ('no-v-margin ui-corner-top')
                .clone (1,1)
                    .attr ('id', 'GmapDemLookupButton')
                    .unbind ('click')
                    .click (function () {
                        var latlng = new google.maps.LatLng(parseFloat ($('#SiteLatDec').val()) || 58, parseFloat ($('#SiteLonDec').val()) || 9.5);
                        wc.getElevation (latlng);
                        return false;
                    })
                    .text ('Elevation from Map')
                    .removeClass ('ui-corner-top')
                    .addClass ('ui-corner-bottom bump-button-up')
                    .insertAfter (this)
                .parent()
                    .wrapInner ($('<div class="fg-buttonset fg-buttonset-single griddedButton2xContainer"></div>'));
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
            var latlng = new google.maps.LatLng(parseFloat ($('#SiteLatDec').val()) || 58, parseFloat ($('#SiteLonDec').val()) || 9.5);

            myOptions = {
                zoom: 3,
                center: latlng,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                },
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
                    this.info.setContent ('<div id="'+iwi+'" style="height: 4.7em; width: 250px; overflow: hidden;">Loading...</div>');
                    
                    google.maps.event.addListener (this.info, 'domready', function () {
                        ($('#'+iwi).text('')
                            .append ($('#rgsUseButton-'+place.placeIndex).clone(true)))
                            .append ($('<span class="placeTitle" style="margin-left: 3px;">'+place.placeTitle+'</span>'))
                            .append ($('<div class="help"><span class="geo"><span class="latitude">'+place.hrLat+'</span> <span class="longitude">'+place.hrLng+'</span></span> <span class="elevation">'+place.elevation+'</span><div>'));
                    });
                    google.maps.event.addListener (this.info, 'closeclick', function () {
                        wc.local.map.map.panTo (wc.local.map.marker.getPosition());
                    });
                    
                    this.info.open (this.map);
                    
                    return true;
                },
                fromBoxen: function () { // when user types in l/l boxen or lookup's Use button clicked
                    var latlng = new google.maps.LatLng(parseFloat ($('#SiteLatDec').val()) || 0, parseFloat ($('#SiteLonDec').val()) || 0);
                    wc.local.map.marker.setPosition (latlng);
                    wc.local.map.map.panTo (latlng);
                    wc.demLookup();
                },
            };
            
            $('#SiteLatDec, #SiteLonDec').keyup (function () {
                wc.local.map.fromBoxen();
            });
            
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
            var drop = function (event) {
                udm (event);
                var z = gmap.getZoom ();
                if (z < 16) {
                    z = Math.ceil (z * 1.15);
                    gmap.setZoom (z);
                }
                wc.demLookup();
            };

            google.maps.event.addListener (marker, 'dragend', drop);
            google.maps.event.addListener (gmap, 'click', drop);
            
            google.maps.event.addListener (gmap, 'resize', function () {
                gmap.setCenter (marker.getPosition());
            });

            var mapResizeHandler = function (i, width) {
                setTimeout ("google.maps.event.trigger(wc.local.map.map, 'resize');", 150);
            };
            $('body').data ('resizeHandler', mapResizeHandler);
            
            $mc.resize (mapResizeHandler);
            
            wc.local.map.elevator = new google.maps.ElevationService();
            
        }, 150);
        
    },
    updateDemBoxen: function (data, boxen) {
        if (!(data['data'])) {
            return false;
        }
        for (box in boxen) {
            if (!!data['data'][box]) {
                boxen[box].val(data['data'][box]).effect ('highlight', {}, 3000);
            }
        }
        // DEMs lapse
        $('input#SiteCoarseFineLapseCorrection').val ( (((data['pmip2'] - data['worldclim']) / 1000) * 6.4).toFixed(4) );
        // Site lapse
        var siteAlt = $('input#SiteElevation').val ();
        if (isNaN (siteAlt) || siteAlt === '' || siteAlt === undefined || !$('input:checkbox#SiteLapseCorrect').is(':checked')) {
            $('input#SiteFineKnownLapseCorrection').val ('');
        }
        else {
            $('input#SiteFineKnownLapseCorrection').val ( (((data['worldclim'] - siteAlt) / 1000) * 6.4).toFixed(4) );
        }
    },
    demLookup: function () {
        var lat = $('input#SiteLatDec').val() || 0;
        lat = parseFloat (lat).toFixed(5);
        var lon = $('input#SiteLonDec').val() || 0;
        lon = parseFloat (lon).toFixed(5);

        var res = {
            'pmip2': $('input#SiteElevationDemCoarse'),
            'worldclim': $('input#SiteElevationDemFine'),
        };
        var form = $('form#SiteForm');

        var cache = form.data('cache');
        
        if (!cache) cache = {};
        if (!!cache[lat+'x'+lon])
            wc.updateDemBoxen(cache[lat+'x'+lon], res);
        else if (form.data ('lookupStatus') != 'loading') {
            form.data ('lookupStatus', 'loading');
            $.ajax ('/wiz/dem_lookup/', {
                data: {'lat': lat, 'lon': lon},
                context: form,
                success: function (data,status,xhr) {
                    data = $.parseJSON(data);
                    cache[lat+'x'+lon] = data;
                    wc.updateDemBoxen(data, res);
                    $(this)
                        .data ('lookupStatus', 'ok')
                        .data ('cache', cache);
                }
            });

        }
        return false;
    },
    initSiteForm: function (ele) {
        wc.initMapLoadButton ();
        wc.initLocationLookupButton ();
        //wc.initElevator ();
        $('input#SiteLatDec, input#SiteLonDec, input#SiteElevation').keyup (wc.demLookup);
        $('input:checkbox#SiteLapseCorrect').change(wc.demLookup);
        $('#reverseGeocodeResults').hide();
        $('input#SiteElevation').change (function () {
            var $this = $(this);
            if ($this.data('changedAuto') == true) {
                $this.data('changedAuto', false);
            }
            $('#SiteElevationCitationText').html ($('#SiteElevationSource').val('').val());
        })
        if ($('#SiteElevationSource').val() !== '') {
            $('#SiteElevationCitationText').html ('Source: <em>' + $('#SiteElevationSource').val() + '</em>');
        }
        wc.demLookup();
    },
    getElevation: function (latLng) {
        
        wc.local.map.elevator.getElevationForLocations ({ locations: [ latLng ] }, function(results, status) {
            console.log (results, status);
            if (status == google.maps.ElevationStatus.OK) {
                // Retrieve the first result
                if (results[0]) {
                    $.smoothScroll ({
                        scrollElement: $('#bg2'),
                        scrollTarget: $('input#SiteElevation')
                            .val(results[0].elevation.toFixed(4))
                            .data ('changedAuto', true)
                            .effect ('highlight', {}, 3000),
                        offset: -85
                    });
                    $('#SiteElevationCitationText').html ('Source: <em style="max-width: 5em;">' + $('#SiteElevationSource').val('Google (' + results[0].resolution.toFixed(1) + 'm res.)').val() + '</em>');
                }
                else {
                    alert("No results found");
                    return false;
                }
            }
            else {
                alert("Elevation service failed due to: " + status);
                return false;
            }
        });
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
            if (!wc.local.map.map) {
                wc.local.mapqueue = function () {
                    wc.local.map.showPlace (event.data);
                };
                $('#FindLatLonByMapButton').click();
            }
            else {
                wc.local.map.showPlace (event.data);
            }
            $.smoothScroll ({
                scrollElement: $('#bg2'),
                scrollTarget: $('#gMapGridBox'),
                offset: -85
            });
            
            return false;
        };
        var placeUse = function (event) {
            var place = event.data;
            
            var dswm = function (place) {
                if (!!wc.local.map.map) {
                    wc.local.map.info.close();
                }
                $([
                    $('input#SiteName').val(place.placeTitle),
                    $('input#SiteLatDec').val(place.lat),
                    $('input#SiteLonDec').val(place.lng),
                    $('textarea#SiteDescription').val(place.summary + '\n(elevation: '+place.elevation+'m)'),
                    $('input#SiteElevation').val(place.elevation.trim()).data ('changedAuto', true)
                ]).effect ('highlight', {}, 3000);
                
                $('#SiteElevationCitationText').html ('Source: <em>' + $('#SiteElevationSource').val('Wikipedia').val() + '</em>');
                $('.rgsClearResultsButton').click();
                if (!!wc.local.map.map) {
                    wc.local.map.fromBoxen ();
                    $.smoothScroll ({
                        scrollElement: $('#bg2'),
                        scrollTarget: $('#SiteName'),
                        offset: -85
                    });
                }
                else {
                    wc.demLookup();
                }
            };
            
            /*if (!wc.local.map.map) {
                wc.local.mapqueue = function () {
                    dswm (place);
                };
                $('#FindLatLonByMapButton').click();
            }
            else {*/
                dswm (place);
            /*}*/
            
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
                summary: $('div.summary', this).text(),
            };
            $row.data ('place', place);
            
            $('.rgsMapButton', $row).click (place, placeMap);
            $('.rgsUseButton', $row).click (place, placeUse);
            
            
            
        });


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
            .blur (function () {
                $(this).toTitleCase();
            })
            .trigger ('blur')
            //.filter ('#ReactionReactionName, #ReactionSubstrateName')
            .keyup(function () {
                    var $me = $(this);
                    var llk = 'reaction.'+$me.attr('id')+'.lastLen';
                    if ($me.data(llk) && $me.data(llk) != $me.val().length) {
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
                    }
                    $me.data(llk, $me.val().length);
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
    },
    initReviewForm: function (ele) {
        ele = ele || '#wizardContainer';
        var $me = $(ele);
        // do stuff here.
    },
    initSpreadsheetSetupForm: function (ele) {
        ele = ele || '#wizardContainer';
        var $me = $(ele);
        // do stuff here.
    }
};




