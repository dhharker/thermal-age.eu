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


