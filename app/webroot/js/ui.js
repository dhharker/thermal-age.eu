$(document).ready (function () {
    $('.fg-button').hover(
        function(){
            $(this).addClass("ui-state-hover");
        },
        function(){
            $(this).removeClass("ui-state-hover");
        }
    );
    
    $('div.spoiler').each (function () {
        $(this).css ({
            'height': '4.2em',
            'overflow': 'hidden',
            'clear': 'both',
            'margin': '0px 0px 2em 0px'
        }).after ($('<a style="margin: -1.5em 20px .5em 20px; display: block; float: right;" href="#" onclick="$(this).prev(\'.spoiler\').find(\'*\').unwrap (); $(this).remove(); return false;">read more...</a>'));
    });
    
    $('#wizardProgressBar #wpbContainer').progressbar ({
        value: 52,
        
    });
});

