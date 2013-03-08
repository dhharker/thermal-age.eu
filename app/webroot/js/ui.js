
var useful = {
    ad2bp: function (ad) {
        return (ad - 1950) * -1;
    },
    bp2ad: function (bp) {
        return (bp / -1) + 1950;
    },
    _isJsFunction: function (obj) {
        return !!(obj && obj.constructor && obj.call && obj.apply);
    },
    /*Reloads the contents of an element via ajax less frequently the less stuff happens
     */
    ajaxReloader: function (container,url,options) {

        var $container = $(container);
        var defaults = {
            sinceEpoch: 0, // since 1970 in case no starting ts supplied
            startDelayS: 6, // number of seconds after the fn is called before it refreshes the content
            addDelayS: function (dS) {return dS + 1;}, // example of fn value. can be float. number of seconds to wait before re-check after no-changes response
            maxDelayS: 7200, // If they've left the browser open then check every 2 hours by default
            //requestType: 'post', // must be post so there.
            params: {}, // to pass with request,
            latestTsParamName: 'since', // param to tell endpoint how up to date we are
            latestTsHeaderName: 'ax-new-epoch' // what is the name of the response header containing the time we're now up to date to
        };
        var settings = $.extend({}, defaults, options);
        var timer = null; // careful with this ;-)
        
        // Declare these above updateContent (IE might get upset otherwise, not sure but belt & braces!)
        var doUpdate = function () {};
        
        
        // function for when request has succeeded
        var startTimer = function () {
            var state = $container.data('ajaxReloader');
            if (!!!state.currentDelay) state.currentDelay = 5;
            window.clearTimeout(timer);
            timer = window.setTimeout(doUpdate,state.currentDelay*1000);
        }
        var updateContent = function (data,xhr) {
            $container.html (data);
            $container.data('ajaxReloader.currentDelay',settings.startDelayS);
            var ne = xhr.getResponseHeader(settings.latestTsHeaderName);
            if (ne > 0)
                $container.data('ajaxReloader.sinceEpoch', parseInt(ne));
            startTimer();
        }
        // for when the request fails or there's no recent data (do nothing, wait longer next time.)
        var noUpdateContent = function (xhr) {
            var state = $container.data('ajaxReloader');
            state.currentDelay = (useful._isJsFunction(settings.addDelayS)) ?
                settings.addDelayS (state.currentDelay) : settings.addDelayS + state.currentDelay;
            var ne = xhr.getResponseHeader(settings.latestTsHeaderName);
            if (ne > 0) {
                state.sinceEpoch = parseInt(ne);
            }
            $container.data('ajaxReloader',state);
            startTimer();
        };
        
        
        // make an ajax request and update the field on non-null response (update delay either way)
        doUpdate = function () {
            var state = $container.data('ajaxReloader');
            var ts = {}; 
            if (typeof state == 'undefined') {
                state = {
                    'currentDelay': settings.startDelayS,
                    'sinceEpoch': settings.sinceEpoch
                };
            }
            else {
                ts[settings.latestTsParamName] = state.sinceEpoch;
            }
            var sendData = $.extend({},settings.params,ts);
            
            var requestOpts = {
                type: 'post',
                data: sendData,
                success: function (data,strStatus,xhr) {
                    if (data.toString().length == 0)
                        noUpdateContent(xhr)
                    else
                        updateContent(data,xhr);
                    return false;
                },
                failure: function (data,strStatus,xhr) {
                    noUpdateContent(xhr);
                    return false;
                }
            }
            $.ajax(url, requestOpts);
            $container.data('ajaxReloader',state);
        };
        
        doUpdate();
    }
};

var initialiseTAUI = function (scope) {
    scope = scope || 'body';
    $('.fg-button,.hover', scope).hover(
        function(){
            $(this).addClass("ui-state-hover");
        },
        function(){
            $(this).removeClass("ui-state-hover");
        }
    );
    
    $('div.spoiler', scope).each (function () {
        $(this).css ({
            'height': '4.2em',
            'overflow': 'hidden',
            'clear': 'both',
            'margin': '0px 0px 2em 0px'
        }).after (
            $('<a>read more...</a>')
                .css ({
                    'margin': '-1.5em 20px .5em 20px',
                    'display': 'block',
                    'float': 'right'
                })
                .attr ('href', '#')
                .click (function () {
                    $(this)
                        .prev('.spoiler')
                        .children()
                        .unwrap ();
                    $(this).remove();
                    return false;
                })
        );
    });
    
    $('#feedbackButton', scope).not('.inited').each (function () {
        var fbf = $('<div id="fbfDialog"></div>');

        fbf
            .hide()
            
            .dialog ({
                show: {
                    effect:'fade',
                    duration:600
                },
                hide: {
                    effect:'fade',
                    duration:600
                },
//                width: 550,
                minWidth: 300,
                minHeight: 200,
                modal: true,
                title: 'Your feedback helps us to improve',
                position: ['center', 50],
                autoOpen: false,
                open: function (e, u) {
                    $(this).loadingAnim();
                    fbf.load ('/feedback', function () {
                            initialiseTAUI (this);
                            $(this).show({
                                effect: 'blind',
                                duration: 600
                            });
                        });
                }
            });
        
        $(this).click (function () {
            fbf.dialog ('open');
            return false;
        });
        return false;
        
    }).addClass ('inited');
    
    $('.dialogise', scope).not ('.inited').each (function () {
        var clicker = $('<a class="fg-button ui-state-default dialogise-clicker ui-corner-all">Show &raquo;</a>');
        clicker.click (function () {
            $(this).siblings('.dialogise-dialog').dialog ('open');
        })
        $(this).before (clicker).wrap('<div class="dialogise-dialog"></div>').parent().hide().dialog ({
            position: ['top', 50],
            width: 600,
            minWidth: 300,
            autoOpen: false
        })
    }).addClass ('inited');
    
    $('div.moodSlider', scope).not ('.inited').each (function () {
        var slider = $(this);
        var input = slider.parent().find ('input[id$=Mood]');
        var udhv = function () {
            input.val ($(this).slider('value') / 100.0);
        };
        
        slider.slider ({
            step: 1,
            min: -100,
            max: 100,
            value: input.val() * 100,
            animate: true,
            slide: udhv,
            change: udhv
        });
    })
    .prepend('<div class="sliderLabelInternal" style="float: none; margin: -2px auto; clear: none; width: 8em; text-align: center;">INDIFFERENT</div>')
    .prepend('<div class="sliderLabelInternal" style="float: right">GOOD</div>')
    .prepend('<div class="sliderLabelInternal" style="float: left">BAD</div>')

    .addClass ('inited');
    
    /*
    $('.class', scope).not ('.inited').each (function () {
        $(this).doStuff();
    }).addClass ('inited');
    */
    
};

$(function () {
    initialiseTAUI();
    (function($){
      $.fn.loadingAnim = function(options) {
        var settings = options || {
            show: {
                effect: 'blind',
                duration: 200
            },
            hide: {
                effect: '',
                duration: 0
            }
        };
        this.hide(settings.hide)
            .html ('<div style="text-align: center; margin: 2em;"><img src="/img/loading_spinner_blue.gif" alt="loading..." /><br /><span style="font-size: 120%; font-style: italic;">Loading...</span></div>')
            .show(settings.show);
        return this;
      };
    })(jQuery);

});

// thanks to http://jamiethompson.co.uk/web/2008/07/21/jquerygetscript-does-not-cache/
$.getScript = function(url, callback, cache){
	$.ajax({
			type: "GET",
			url: url,
			success: callback,
			dataType: "script",
			cache: cache
	});
};


// this has to be loaded after wc exists. note this allows cacheing
$.getScript ('/js/adapt/adapt.js', function () {}, 0);


