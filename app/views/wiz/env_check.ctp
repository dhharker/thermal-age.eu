<div class="grid_12">
    <div class="smartbox">
        <script type="text/javascript">
            $(document).ready (function () {
                var eff = 'blind';
                var ssCookie = <?= (isset ($cookie) && $cookie == true) ? 1 : 0; ?>;
                $('#envCheckMsg').show();
                
                if (ssCookie != 1) {
                    $('#envCheckMsg').hide(eff, function () {
                        $('#badCookieMsg').show(eff);
                    });
                }
                else if ($.support.ajax == true && $.support.boxModel == true) {
                    $('#envCheckMsg')
                        .hide()
                        .text ("Browser is compatible; redirecting you to the wizard now!")
                        .show(eff, function () {
                            window.location = '<?=$this->Html->url (array ('action' => $redirectTo))?>';
                        });
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
        <p id="badJsMsg" style="display: none" class="error ui-corner-all">
            You do have javascript enabled, but we have detected that your browser's javascript implementation cannot support this website. Please
            upgrade to a better browser like Chrome, Firefox or Safari.
        </p>
        <p id="badCookieMsg" style="display: none" class="error ui-corner-all">
            You need to have session cookies enabled to use the wizards. These only persist until
            you close your browser window. Please see our
            <?=$this->Html->link ("privacy policy", array ('controller' => 'pages', 'action' => 'legal', 'privacy'))?>
            if you have concerns about this.
        </p>
    </div>
</div>