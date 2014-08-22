<!-- app/View/Entities/add.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Issue'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		//echo $this->Form->input('Attribute.importance', array('type' => 'text'));
		$types = array('low' => 'Low', 'medium' => 'Medium', 'high' => 'High');
		echo $this->Form->input('Attribute.importance', array(
			'options' => $types,
			'default' => 'low'
			)
		);
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('action' => 'add_issue', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding entity")', 'success' => 'create_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>