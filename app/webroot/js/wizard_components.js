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
        
        wc.initLoadFromButton($me);
    },
    initLoadFromButton: function (scope) {
        scope = scope || '#wizardContainer';
        var $btn = $('#loadValuesFromButton');//.click (function () { return confirm('This will overwrite any data already in this form - continue?'); });
        var $inp = $('#loadValuesFromJobSelect');
        $inp.on('change', function(){
            $btn.attr ('href','/wiz/get_values_from_job_screen/?' + $.param({
                job_id: $inp.val(),
                wiz_name: $btn.attr('data-wizn'),
                wiz_screen: $btn.attr('data-wscn')
            }))
        }).change();
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
            var ival = $i.attr('value');
            if (!( isNaN (ival) || '' === ival || ival.length == 0 )) {
                //console.log ("ival is a number", ival);
                newInp.val($i.val());
                $i.val (useful.bp2ad (newInp.val()));
                
            }
            else {
                //console.log ("ival is not a number", ival);
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
            //axis: 'y',
            //containment: 'div#burialLayersList',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            handle: '.sort-handle',
            update: function (event, ui) {
                if (wc) window.setTimeout('wc.reorderLayers',200, list);
            },
            create: function (event, ui) {
                if (wc) window.setTimeout('wc.reorderLayers',200, list);
            },
            revert: 400
        });
        
        $('.sort-handle').live('click',function(){ return false; });
        
        this.initLayerDeleteButtons (scope);
        this.initWaterSlides (list);
        this.reorderLayers (scope);
        this.local.burial.nextIndex = $('input#BurialNumLayers', scope).val();
        
        $('select[id$="Id"]',scope).live('change', function () {
            var $this = $(this);
            var $row = $this.parentsUntil ('fieldset');
            var val = $this.val();
            var vName = wc.local.soilsData.nameById[val];
            
            
            var graph = wc.local.soilsData.graphs[vName];
            
            var custom = $('input:checkbox[id$="Custom"]',$row).first().is(':checked');
            
            if ($this.is(':visible') && !custom)
                $('input[id$=Name]',$row).val(vName);
            
            if (graph != undefined && $this.is(':visible') && !custom) {
                
                $('.saturationSpark',$row)
                    .sparkline (graph, {
                        chartRangeMin: 0,
                        chartRangeMax: wc.local.soilsData.graphMaxDh,
                        width: '95.5%',
                        height: '23px'
                    })
                ;
                $('.show-graph:hidden',$row).show();
                $('.hide-graph:visible',$row).hide();
                var newMax = wc.local.soilsData.graphableMax[vName];
                var slider = $('.waterSlider',$row);
                var oldVal = slider.slider('value')
                    
                slider.slider('option','max',newMax).slider('refresh');
                if (newMax < oldVal)
                    slider.slider('value',newMax);
                else
                    slider.slider('value',oldVal);
            }
            else {
                $('.show-graph:visible',$row).hide();
                $('.hide-graph:hidden',$row).show();
                if ($this.is(':visible') && !custom) {
                    var inputDh = $row.find ('input[id$=ThermalDiffusivityM2Day]').first();
                    inputDh.val (wc.local.soilsData.dhById[val]);
                }
            }
            
        });
        
        $(window).on('resize', function () {
             $('select[id$="Id"]',scope).trigger ('change');
        });
        
        $('input:checkbox[id$="Custom"]',scope).live('change', function () {
            var $this = $(this);
            var $row = $this.parentsUntil ('fieldset');
            if ($this.is(':checked')) {
                $('select[id$="Id"]',$row).attr('disabled',true);
                $('.hide-custom',$row).hide();
                $('.show-custom',$row).show();
                //$('.required-custom input',$row).attr('disabled',false).parent().addClass('required').find('label:first').hide();
            }
            else {
                $('select[id$="Id"]',$row).attr('disabled',false);
                $('.hide-custom',$row).show();
                $('.show-custom',$row).hide();
                //$('.required-custom input',$row).attr('disabled',true).parent().removeClass('required').find('label:first').show();
            }
            $('select[id$="Id"]',$row).trigger('change');
        }).trigger('change');
        
        $('.required-custom input[id$="WaterContent"]').live('change',function () {
            var $this = $(this);
            var $row = $this.parentsUntil ('fieldset');
            var $ws = $('div.waterSlider',$row).first();
            var val = parseInt ($this.val());
            var max = $ws.slider('option', 'max');
            if (val > max) {
                $this.val(parseInt(max));
            }
            else {
                $ws.data('set-internally', true);
                $ws.slider('value', val);
            }
        });
        $('.required-custom input[id$="ThermalDiffusivityM2Day"]').live('focus',function () {
            var $this = $(this);
            $this.data('beforeFocus',$this.val());
        });
        $('.required-custom input[id$="ThermalDiffusivityM2Day"]').live('change',function () {
            var $this = $(this);
            var $row = $this.parentsUntil ('fieldset');
            var $ccb = $('input:checkbox[id$="Custom"]',$row);
            if (!$ccb.is(':checked') && $this.data('beforeFocus') != $(this).val()) {
                $ccb.attr('checked',true).trigger('change');
            }
        });
        
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
                duration: 300
            }, function () {
                $('select[id$="Id"]',this).trigger('change');
            });
            initialiseTAUI (list);
            wc.initLayerDeleteButtons (list);
            wc.initWaterSlides (list);
            wc.reorderLayers (list);
            return false;
        });
        
    },
    
    initWaterSlides: function (scope) {
        scope = scope || '#wizardContainer';
        $('div.waterSlider', scope).not ('.inited').each (function () {
            var slider = $(this);
            var $row = slider.parentsUntil ('fieldset');
            var inputPc = $row.find ('input[id$=WaterContent]').first();
            var inputMe = $row.find ('input[id$=H2o]').first();
            var inputDh = $row.find ('input[id$=ThermalDiffusivityM2Day]').first();
            var fnUpdate = function () {
                var val = parseInt (slider.slider('value'));
                if (slider.data('set-internally') === true)
                    slider.data('set-internally', false);
                else
                    inputPc.val (val);
                inputMe.val (val / 100.0);
                if (!$('input:checkbox[id$="Custom"]',$row).is(':checked'))
                    inputDh.val (wc.local.soilsData.graphs[$('input[id$=Name]',$row).first().val()][val]);
                
            };
            var fnSlide = function () {
                fnUpdate();
            };
            var fnChange = function () {
                fnUpdate();
                var $this = $(this);
                $this.find('a').trigger('blur');
                
            };
            
            slider.slider ({
                step: 1,
                min: 0,
                max: 100,
                value: inputMe.val() * 100,
                animate: true,
                slide: fnSlide,
                change: fnChange
            });
        })
        //.prepend('<div class="sliderLabelInternal" style="float: none; margin: -1px auto; clear: none; width: 8em; text-align: center;">WET</div>')
        .prepend('<div class="sliderLabelInternal" style="float: right">WET</div>')
        .prepend('<div class="sliderLabelInternal" style="float: left">DRY</div>')

        .addClass ('inited');
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
                //scrollElement: $('#bg2'),
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
                gmap.panTo (marker.getPosition());
                console.log ("UDM",event);
            };
            var udmnm = function (event) {
                marker.setPosition (event.latLng);
                $('#SiteLatDec').val(event.latLng.lat())
                $('#SiteLonDec').val(event.latLng.lng())
            };
            var drop = function (event) {
                udm (event);
                /*var z = gmap.getZoom ();
                if (z < 16) {
                    z = Math.ceil (z * 1.15);
                    gmap.setZoom (z);
                }// don't do the above actually because it conflicts with the default zoom on double click behaviour which won't go away as easily as it might */
                wc.demLookup();
            };
            var dblclick = function (event) {
                udmnm (event);
                wc.demLookup()
            };

            google.maps.event.addListener (marker, 'dragend', drop);
            google.maps.event.addListener (gmap, 'dblclick', dblclick);
            //google.maps.event.addListener (gmap, 'click', function () { return false; });
            
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
            if (data.data[box] === 0) {
                if (parseFloat(boxen[box].val()).toFixed(3) != parseFloat (data.data[box]).toFixed(3)) {
                    boxen[box].val(data.data[box].toFixed(3)).not(':animated').effect ('highlight', {}, 1500);
                }
            }
            else if (!!data.data[box]) {
                if (parseFloat(boxen[box].val()).toFixed(3) != parseFloat (data.data[box]).toFixed(3)) {
                    boxen[box].val(data.data[box].toFixed(3)).not(':animated').effect ('highlight', {}, 1500);
                }
            }
        }
        // DEMs lapse
        var cflc = (((data.data['pmip2'] - data.data['worldclim']) / 1000) * 6.4).toFixed(4);
        $('input#SiteCoarseFineLapseCorrection').val ((isNaN(cflc)) ? '' : cflc);
        // Site lapse
        var siteAlt = $('input#SiteElevation').val ();
        //if (isNaN (siteAlt) || siteAlt === '' || siteAlt === undefined || !$('input:checkbox#SiteLapseCorrect').is(':checked')) {
        if (isNaN (siteAlt) || siteAlt === '' || siteAlt === undefined) {
            $('input#SiteCoarseKnownLapseCorrection').val ('');
            if ($('input:checkbox#SiteLapseCorrect').is(':checked')) {
                $('input:checkbox#SiteLapseCorrect').attr('checked', false);
            }
        }
        else {
            $('input:checkbox#SiteLapseCorrect').attr('checked', true);
            $('input#SiteCoarseKnownLapseCorrection').val ( (((data.data['pmip2'] - siteAlt) / 1000) * 6.4).toFixed(4) );
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
                        //scrollElement: $('#bg2'),
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
                //scrollElement: $('#bg2'),
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
                        //scrollElement: $('#bg2'),
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
                        // @TODO showname is not updated when a custom reaction is used!
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
        
    }
};





















