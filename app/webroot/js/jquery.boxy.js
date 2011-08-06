
$(document).ready(function() {
    
    (function( $ ){
        var methods = {
            setOpts: function (options) {
                $(this).data ('boxyOptions', options);
            },
            init: function (options) {
                options = options || {};
                var settings = {
                    initBoxen: 0,
                    minBoxen: 0,
                    maxBoxen: 10,
                    filter: '*',
                    title: 'untitled :-('
                };
                
                if (options) { 
                    $.extend(settings, options);
                }
                
                var filter =/* options.filter ||*/ settings.filter;
                
                $('ul.boxy', this.parent()).filter (filter).each (function () {
                    $this = $(this);
                    var data = $this.data('boxy');
                    var sdata = $this.data('boxyOptions');
                    if (!data) {
                        data = {};
                        data.settings = settings;
                        if (sdata) {
                            $.extend (data.settings, sdata);
                        }
                        var template = $('li:first', this);
                        data.template = template.clone ();
                        template.remove ();
                        $this.data ('boxy', data);
                        $this.boxy('initBoxen', this);
                        $this.boxy (options);
                    }
                    
                });
            },
            initBoxen: function (ele) {
                ele = ele || this;
                var $e = $(ele);
                var data = $e.data('boxy');
//                 console.log (data);
                
                $('li.boxyBox', $e).remove();
                $('li.boxyCtl', $e).remove();
                var ctl = $('<li />')
                    .addClass ('boxyCtl')
                    .append (
                        $('<a />')
                            .click (function () {
                                $(this).closest ("ul.boxy").boxy ('addBox');
                                $(this).blur();
                                return false;
                            })
                            .text ('Add ' + data.title)
                            .attr ('href', '#')
                            .addClass ('addBox'));
//                     .append (
//                         $('<a />')
//                             .click (function () {
//                                 $(this).closest ("ul.boxy").boxy ('delBox');
//                                 return false;
//                             })
//                             .text ('Del')
//                             .attr ('href', ''));
                            
                $e.append (ctl);
                var data = $e.data('boxy');
                
                for (var i = 0; i < data.settings.initBoxen; i++) {
                    $e.boxy('addBox');
                }
//                     //offsetFieldIndexString = ofis
//                     var nofis = $e.boxy ('getParentOffsets', $e) + '[' + i + ']';
//                     var newBox = $(data.template).clone ();
//                     $(newBox).data ('boxy', {offset: i, ofis: nofis}).addClass ("boxyBox");
//                     $('input', newBox).each (function () {
//                         $(this).attr ('name', $(this).attr ('name') + nofis)
//                     });
//                     $e.append (newBox);
//                     //console.log (nofis, newBox);
                //}
                //
            },
            getParentOffsets: function (ele) {
                //pli = $(ele).parents('ul.boxy > li:first');
                $this = $(this);
                var pli = $this.closest ('ul.boxy > li.boxyBox');
                if (pli) {
                    var data = pli.data('boxy');
                    return (data && data.ofis) ? data.ofis : '';
                }
                else {
                    return '';
                }
            },
            addBox: function () {
                var numBoxen = this.boxy('numBoxen');
                var data = this.data ('boxy');
                if (numBoxen < data.settings.maxBoxen) {
                    var lis = $('li.boxyBox:last', this);
                    var ld = lis.data('boxy');
                    if (ld)
                        var lastInd = $('li.boxyBox:last', this).data('boxy').offset;
                    else
                        var lastInd = -1;
                    var newInd = lastInd + 1;
                    
                    var newBox = $(data.template).clone ();
                    var ofis = this.boxy ('getParentOffsets', this);
                    var nofis = ofis + '[' + newInd + ']';
                    
                    $(newBox)
                        .data ('boxy', {offset: newInd, ofis: ofis})
                        .addClass ("boxyBox")
                        .prepend (
                            $('<div />').addClass ('boxyTitle')//.text (data.settings.title)
                        )
                        .prepend (
                            $('<a />')
                            .click (function () {
                                $(this).closest ("li.boxyBox").boxy ('delBox');
                                $(this).blur();
                                return false;
                            })
                            .text ('[Delete]')
                            .attr ('href', '')
                            .addClass ('delBox'));
                    
                    $(':input', newBox).each (function () {
                        $(this).attr ('name', $(this).attr ('name') + nofis)
                    });
                    
                    newBox.hide();
                    this.append (newBox);
                    newBox.show ("fast");
                    this.data ('boxy', data);
                    newBox.boxy (data.settings);
                    $.smoothScroll ({
                        scrollElement: $('#bg2'),
                        scrollTarget: newBox,
                        offset: -1 * ($(window).height () / 2 - $(this).height () / 2)
                    });
                    if (data.settings.onUpdate)
                            data.settings.onUpdate.apply ();
                    
                    return false;
                    
                }
            },
            delBox: function () {
                if (this.hasClass ("boxyBox")) {
                    var boxy = this.closest ('ul.boxy');
                    var data = boxy.data ('boxy');
                    var numBoxen = boxy.boxy ('numBoxen');
                    if (jQuery.queue (this).length > 0)
                        return false; // not if animating
                    if (data.settings.minBoxen < numBoxen)
                        this.hide("fast", function () {
                            $(this).remove ();
                        });
                        var prevBox = $(this).prev('li.boxyCtl');
                        $.smoothScroll ({
                                scrollElement: $('#bg2'),
                                scrollTarget: prevBox,
                                offset: -1 * ($(window).height () / 2 - $(prevBox).height () / 2)
                            });
                    return true;
                }
                return false;
            },
            numBoxen: function () {
                return $('li.boxyBox',this).length - $('li.boxyBox',this.children()).length;
            }
        };
        
        $.fn.boxy = function (method) {
            
            if ( methods[method] ) {
                return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
            }
            else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, arguments );
            }
            else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.boxy' );
            }  
            
        };
    })( jQuery );
    
    
    
    
    
    
    
    
    
    
    
    
    
});




