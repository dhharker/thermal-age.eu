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
));?>
<?= $this->Form->submit("Login");?>

<?php echo $this->Form->end(); ?>

</div></div>
<div class="grid_5"><div class="smartbox">
<h2>External</h2>
<?php
if (isset($response))
    var_dump ($response);
else
    echo $this->Html->link ("Login with Google", array (
        'controller' => 'users',
        'action' => 'oauth',
        'google',
    ));
?>
</div></div>