(function ($) {
    window.fnInitSvgs = function () {
        
        //console.log ("svg");
        /* When the viewport is resized, scale embeds to match new parent width while maintaining
        original aspect ratio. */
        var embedIndex = 0;
        $('.expand-embeds').find('embed').each (function () {
                var did = 'embed-svg-' + embedIndex++;
                //console.log (this);return;
                var $this = $(this).hide();
                var before = {
                    width: $this.width(),
                    height: $this.height()
                };
                var svgFile = $this.attr('src');

                var div = $('<div></div>').attr('id',did).addClass('embed-svg').css(before);

                div.insertAfter($this).svg({
                    onLoad: function() {
                        //console.log (svg,svgFile,did);
                        var embd = $('#'+did);
                        var svg = embd.svg('get');
                        svg.load(svgFile, {
                            addTo: false,
                            changeSize: true
                        });
                    },
                    settings: {}
                });

                $this.remove();

        });

        $('.expand-embeds').resize (function () {
            var $pard = $(this);
            $('.loading-spinner', $pard.parent()).hide({
                'effect': 'blind',
                'duration': 200
            }, function () {
                $(this).remove();
            });
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
            });
        }).resize();
    };
    $(document).on('ready', window.fnInitSvgs);
}(jQuery));


