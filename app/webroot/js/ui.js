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

        fbf.dialog ({
            show: {
                effect:'fade',
                duration:600
            },
            hide: {
                effect:'fade',
                duration:600
            },
            width: 550,
            minWidth: 200,
            minHeight: 200,
            modal: true,
            title: 'Your feedback helps us to improve',
            position: ['center', 50],
            autoOpen: false,
            close: function (e, u) {
                fbf.load ('/feedback');
            },
        });
        fbf.load ('/feedback');
        $(this).click (function () {
            fbf.dialog ('open');
            initialiseTAUI (fbf);
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
            autoOpen: false,
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


