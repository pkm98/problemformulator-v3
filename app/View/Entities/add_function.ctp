<!-- app/View/Entities/add_function.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Function'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('action' => 'add_function', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding entity")', 'success' => 'create_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>