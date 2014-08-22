<!-- app/View/Users/add.ctp -->
<div class="row-fluid">
    <div class="span4 offset4">
    <?php echo $this->Form->create('User', array(
        'class' => 'form-horizontal',
        'inputDefaults' => array(
            'format' => array('before', 'label', 'between', 'input', 'error', 
            'after'),
            'div' => array('class' => 'control-group'),
            'label' => array('class' => 'control-label'),
            'between' => '<div class="controls">',
            'after' => '</div>',
            'error' => array('attributes' => array('wrap' => 'span', 'class' => 
            'help-inline')),
        )));
        ?>
        <fieldset>
            <legend><?php echo __('Login'); ?></legend>
            <?php echo $this->Session->flash('auth'); ?>
        <?php
            echo $this->Form->input('email');
            echo $this->Form->input('password');
        ?>
        <div class="controls"><button type="submit" class="btn 
        btn-primary">Login </button></div>
        </fieldset>
    <?php echo $this->Form->end();?>
    </div>
</div>



