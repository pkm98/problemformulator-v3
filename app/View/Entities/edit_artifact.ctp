<!-- app/View/Entities/edit.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Edit Artifact'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		$types = array('solution_principal' => 'Solution Principal', 'physical_embodiment' => 'Physical Embodiment');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'solution_principal'
			)
		);
		echo $this->Form->input('Entity.id', array('type' => 'hidden'));
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Edit', array('url' => array('action' => 'edit_artifact', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error editing entity")', 'success' => 'update_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>