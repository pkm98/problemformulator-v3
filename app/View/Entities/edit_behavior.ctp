<!-- app/View/Entities/edit.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Edit Behavior'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		$types = array('behavior' => 'Behavior', 'equation' => 'Equation', 'parameter' => 'Parameter');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'behavior'
			)
		);
		echo $this->Form->input('Entity.id', array('type' => 'hidden'));
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Edit', array('url' => array('action' => 'edit_behavior', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error editing entity")', 'success' => 'update_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>