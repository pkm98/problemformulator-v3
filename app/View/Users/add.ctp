<!-- app/View/Users/add.ctp -->
<div class="row-fluid">
    <div class="span5 offset3">
    <?php //echo $this->Form->create('User');?>
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
            <legend><?php echo __('Create Account'); ?></legend>
        <?php
            echo $this->Form->input('firstname');
            echo $this->Form->input('lastname');
            echo $this->Form->input('email');
            echo $this->Form->input('password');
            echo $this->Form->input('agree', 
                                    array('type'=>'checkbox',
                                    'label' => false, 
                                    'after' => ' By checking this box, I agree 
                                    to the Problem Formulator ' .  
                                    $this->Html->link('Terms of Use', 
                                    array('controller' => 'pages', 'action' => 
                                    'display', 'terms_of_use')) . '. </div>'));
            echo '<div class="controls"><button type="submit" class="btn btn-primary">Create 
                Account</button></div>';
        ?>
        </fieldset>
    <?php echo $this->Form->end();?>
    </div>
</div>
