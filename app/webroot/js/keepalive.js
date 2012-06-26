$(function () {
    var keepaliver = $('body').data('keepaliver');
    if (!keepaliver) {
        keepaliver = {
            requestCountdown: false,
            requestPending: false,
            lastResponseTime: 0,
            timer: null,
            interval: (5*60*1000),
            abandonSessionAfter: (35*60*1000),
            pingTimeout: 6000,
            element: null,
            url: '/wiz/keepalive',
            
            init: function () {
                keepaliver.element = '#keep-alive-timer';
                keepaliver.sendPing();
                $('body').data('keepaliver', keepaliver);
            },
            startTimer: function () {
                keepaliver.timer = setInterval(keepaliver.sendPing, keepaliver.interval);
                keepaliver.requestCountdown = true;
            },
            stopTimer: function () {
                clearInterval (keepaliver.timer);
                keepaliver.requestCountdown = false;
            },
            sendPing: function () {
                var d = new Date();
                var now = d.getTime();
                if (keepaliver.requestPending) {
                    // we are still waiting for a previous ping, do nothing
                }
                else {
                    
                    keepaliver.stopTimer();
                    keepaliver.requestPending = true;
                    $.ajax (keepaliver.url, {
                        success: keepaliver.pingSuccess,
                        error: keepaliver.pingError,
                        //dataType: 'html',
                        timeout: keepaliver.pingTimeout
                    });
                }
            },
            pingSuccess: function (data, textStatus, jqXHR) {
                
                var d = new Date();
                var now = d.getTime();
                var diff = now - keepaliver.lastResponseTime;
                
                keepaliver.requestPending = false;
                keepaliver.lastResponseTime = now;
                
                if (diff > this.abandonSessionAfter) {
                    // connectivity has resumed after too long without connectivity
                    $(keepaliver.element).text('Keepalive disabled (offline too long)');
                    keepaliver.stopTimer();
                }
                else {
                    $(keepaliver.element).parent().html(data);
                    keepaliver.startTimer();
                }
            },
            pingError: function (jqXHR, textStatus, errorThrown) {
                console.log ('keepalive err', textStatus);
                keepaliver.requestPending = false;
                $(keepaliver.element).html('<div class="ui-corner-all error-message">Connection Error!</div>');
                keepaliver.startTimer();
            },
            
        };
        keepaliver.init();
    }
});
