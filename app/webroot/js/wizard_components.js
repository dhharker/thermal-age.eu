// hat, cloak etc.
var wc = {
    init: function (sel) {
        var context = $(sel || '#wizardControlBox');
        wc.step.stageMenu ();
    },
    step: {
        stageMenu: function (sel) {
            sel = sel || '#wizardRightColumn ul.menu li a';
            $(sel).hover (
                function (e) {
                    $(this).find('.blurbCon').not(':animated').animate ({
                        height: '+=1.2em',
                    }, 150, 'easeInOutQuad');
                },
                function (e) {
                    $(this).find('.blurbCon').animate ({
                        height: '-=1.2em'
                    }, 150, 'easeInOutQuad');
                }
            ).click (function () {
                $(this)
                    .blur()
                    .effect ('highlight',{
                            color: '#ffffff'
                        }, 600)
                ;
                    
                return false;
            });
            
        },
        init: function () {
            
        }
    }
}

$(document).ready (function () {
    wc.init ();
});
