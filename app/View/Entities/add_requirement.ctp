<!-- app/View/Entities/add_requirement.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Requirement'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		echo $this->Form->input('Attribute.source');
		$types = array('requirement' => 'requirement', 'goal' => 'goal');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'requirement'
			)
		);
		$types = array('low' => 'Low', 'medium' => 'Medium', 'high' => 'High');
		echo $this->Form->input('Attribute.importance', array(
			'options' => $types,
			'default' => 'low'
			)
		);
		echo $this->Form->input('Attribute.goal_target');
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('action' => 'add_requirement', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding entity");', 'success' => 'console.log(data); create_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>