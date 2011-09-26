<div class="grid_12"><div class="xsmartbox">
<h1 class="sbHeading">Thanks for your input!</h1>
<p>
    We appreciate you taking the time to let us know what you think. User feedback will help make
    this site better for everyone.
</p>

<?php echo $this->Html->link(
    'Close Window',
    array ('/'),
    array('class' => 'fg-button ui-corner-all ui-state-default ui-priority-primary', 'escape' => false, 'style' => 'display: none', 'id' => 'fbfCloseButton')); ?>
</div></div>
<script type="text/javascript">
$(document).ready (function () {
    $('#fbfDialog').each (function () {
        $('#fbfCloseButton', this).not('.inited').click(function () {
            $('#fbfDialog').dialog ('close');
            return false;
        }).addClass('inited').show ();
    });
});
</script>