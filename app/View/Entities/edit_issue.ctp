<!-- app/View/Entities/edit.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Edit Issue'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		$types = array('low' => 'Low', 'medium' => 'Medium', 'high' => 'High');
		echo $this->Form->input('Attribute.importance', array(
			'options' => $types,
			'default' => 'low'
			)
		);
		echo $this->Form->input('Entity.id', array('type' => 'hidden'));
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Edit', array('url' => array('action' => 'edit_issue', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error editing entity")', 'success' => 'update_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>