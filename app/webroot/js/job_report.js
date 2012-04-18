$(function () {

    //console.log (svg,svgFile,did);
    /* When the viewport is resized, scale embeds to match new parent width while maintaining
    original aspect ratio. */
    var embedIndex = 0;
    $('.expand-embeds').find('embed').each (function () {
            var did = 'embed-svg-' + embedIndex++;
            var $this = $(this);
            var before = {
                width: $this.width(),
                height: $this.height()
            };
            var svgFile = $this.attr('src');

            var div = $('<div></div>').attr('id',did).addClass('embed-svg').css(before);

            div.insertAfter($this).svg({
                onLoad: function() {
                    console.log (svg,svgFile,did);
                    var svg = $('#'+did).svg('get');
                    svg.load(svgFile, {
                        addTo: false,
                        changeSize: false
                    });
                },
                settings: {}
            });
            $this.remove();

    });

    $('.expand-embeds').resize (function () {
        var $pard = $(this);
        $pard.find('.embed-svg svg').each (function () {
            var before = {
                width: $(this).width(),
                height: $(this).height()
            };

            var nw = $pard.width();
            var after = {
                width: nw,
                height: ((nw / $(this).width()) * $(this).height())
            };

            
            // For some reason this isn't either working or causing an error
            $(this).animate (after, {
                duration: 200,
                complete: function () {
                    $(this).css (after);
                },
                easing: 'linear'
            }).parent().css({height: 'auto', width: 'auto'});
            //*/
            console.log (this, before, after);

            //$(this).css(after)
        });
    });
});


