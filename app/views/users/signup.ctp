<div class="grid_7"><div class="smartbox">
<h1 class="sbHeading">Sign Up</h1>

<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
	<?php
		echo $this->Form->input('name', array (
            'label' => 'Your full name as you would like it to appear'
        ));
		echo $this->Form->input('username',array (
            'label' => 'Username (used to log in)'
        ));
		echo $this->Form->input('password', array (
            'value' => ''
        ));
		echo $this->Form->input('repeat_password', array (
            'type' => 'password',
            'value' => '',
            'div' => 'required'
        ));
		echo $this->Form->input('email_priv', array (
            'label' => 'Email Address (private)'
        ));
		echo $this->Form->input('url', array (
            'label' => 'URL for your staff info page or website',
            'default' => 'http://'
        ));
		echo $this->Form->input('institution', array (
            'label' => 'Your academic/commercial context'
        ));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Create Account!', true), array (
    'class' => 'fg-button ui-state-default ui-corner-all cta-button'
));?>
    <br />
</div>
<?php
/*
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Groups', true), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Group', true), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Reactions', true), array('controller' => 'reactions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Reaction', true), array('controller' => 'reactions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('controller' => 'soils', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('controller' => 'temporothermals', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
 * 
 */
?>
    </div>
</div>

<div class="grid_5"><div class="smartbox">
<h2 class="sbHeading">Hello</h2>
<p>
    Please fill out this form to create an account. If you have a Google account and don't want to
    bother with this then you can sign in with Google by clicking here:
</p>
<p>
    <?php
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
</p>
</div>
</div>