<div class="grid_7"><div class="smartbox">
<h1>Login</h1>

<?php echo $this->Form->create  ('User', array('url' => $this->here, 'class' => 'ui-corner-all', 'style' => "margin: 1em")); ?>

<?= $this->Form->input('User.username', array(
    'label' => 'Username/Email',
    'placeholder' => 'e.g. me@example.com',
));?>
<?= $this->Form->input('User.password', array(
    'label' => 'Password',
    'placeholder' => '',
    'value' => ''
));?>
<?= $this->Form->submit("Login");?>

<?php echo $this->Form->end(); ?>

</div></div>
<div class="grid_5"><div class="smartbox">
<h2>External</h2>
<ul>

<?php
if (isset($of)) {
    echo "<li>";
    var_dump ($of);
    echo "</li>";
}
echo "<li>";
echo $this->Html->link ($this->Html->image('oauth_google_red_large.png', array (
        'alt' => "Login with Google",
        'style' => 'max-width: 100%; max-height: 46px;'
    )), array (
    'controller' => 'users',
    'action' => 'oauth'
), array (
    'escape' => false
));
?>
</li>
</ul>
</div></div>