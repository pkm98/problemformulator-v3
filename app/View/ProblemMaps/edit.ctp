<!-- app/View/ProblemMaps/edit.ctp -->
<div class="row-fluid">
    <div class="span5 offset3">
        <?php echo $this->Form->create(array(
            'class' => 'form-horizontal',
            'inputDefaults' => array(
                'format' => array(
                    'before',
                    'label',
                    'between',
                    'input',
                    'error',
                    'after'
                ) ,
                'div' => array(
                    'class' => 'control-group'
                ) ,
                'label' => array(
                    'class' => 'control-label'
                ) ,
                'between' => '<div class="controls">',
                'after' => '</div>',
                'error' => array(
                    'attributes' => array(
                        'wrap' => 'span',
                        'class' => 'help-inline'
                    )
                ) ,
            )
        ));
        ?>
        <fieldset>
            <legend><?php echo __('Edit Problem Map'); ?></legend>
                <?php
                echo $this->Form->input('name');
                echo $this->Form->input('description');
                echo '<div class="controls"><button type="submit" class="btn 
                    btn-primary">Edit Map</button> ';
                echo $this->Html->link('Cancel', 
                    array('controller' => 'problem_maps', 'action' => 
                    'index'), array('class'=>'btn'));
                ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
