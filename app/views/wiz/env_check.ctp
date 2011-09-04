<div class="grid_12">
    <div class="smartbox">
        <script type="text/javascript">
            $(document).ready (function () {
                var eff = 'blind';
                var ssCookie = <?= (isset ($cookie) && $cookie == true) ? 1 : 0; ?>;
                var ssIe = <?= (isset ($ie) && $ie == true) ? 1 : 0; ?>;
                var redFunc = function () {
                    window.location = '<?=$this->Html->url (array ('action' => $redirectTo))?>';
                    return false;
                };

                $('.recklessButton').click (redFunc);

                $('#envCheckMsg').show();

                if (ssIe != 0) {
                    $('#envCheckMsg').hide(eff, function () {
                        $('#ieMsg').show(eff);
                    });
                }
                else if (ssCookie != 1) {
                    $('#envCheckMsg').hide(eff, function () {
                        $('#badCookieMsg').show(eff);
                    });
                }
                else if (0 && $.support.ajax == true && $.support.boxModel == true) {
                    $('#envCheckMsg')
                        .hide()
                        .text ("Browser is compatible; redirecting you to the wizard now!")
                        .show(eff, redFunc);
                }
                else {
                    $('#envCheckMsg').hide(eff, function () {
                        $('#badJsMsg').show(eff);
                    });
                }
            });
        </script>
        <noscript>
            <p class="error ui-corner-all">You need to enable javascript to use the wizards</p>
        </noscript>
        <p id="envCheckMsg" style="display: none">
            Checking your browser for compatibility, please wait...
        </p>

        <p id="ieMsg" style="display: none; margin-right: 200px;" class="error ui-corner-all">
            <img src="/img/no_ie.png" alt="No IE" style="float: right; margin: .5em; margin-right: -200px;">

            This website is not fully compatible with Internet Explorer. Please upgrade to a better browser like 
            <a href="http://www.google.com/chrome/" alt="Download Google Chrome">Chrome</a>,
            <a href="http://www.mozilla.com/firefox/" alt="Download Mozilla Firefox">Firefox</a> or
            <a href="http://www.apple.com/safari/download/" alt="Download Apple Safari">Safari</a>. If you want to give
            it a try in IE then click below, but be warned it may not work well or at all.

            <?php echo $this->Html->link(
                "Try it anyway...",
                '',
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button recklessButton', 'escape' => false)
            ); ?>
        </p>
        <p id="badJsMsg" style="display: none" class="error ui-corner-all">
            You have javascript enabled, but we have detected that your browser's javascript implementation cannot support this website. Please upgrade to a better browser like
            <a href="http://www.google.com/chrome/" alt="Download Google Chrome">Chrome</a>,
            <a href="http://www.mozilla.com/firefox/" alt="Download Mozilla Firefox">Firefox</a> or
            <a href="http://www.apple.com/safari/download/" alt="Download Apple Safari">Safari</a>.
            You can click below if you want to try it anyway, please let us know how you get on using the &quot;Feedback&quot; link above.

            <?php echo $this->Html->link(
                "Try it anyway...",
                '',
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button recklessButton', 'escape' => false)
            ); ?>
        </p>
        <p id="badCookieMsg" style="display: none" class="error ui-corner-all">
            You need to have session cookies enabled to use the wizards. These only persist until
            you close your browser window. Please see our
            <?=$this->Html->link ("privacy policy", array ('controller' => 'pages', 'action' => 'legal', 'privacy'))?>
            if you have concerns about this.
        </p>
    </div>
</div>