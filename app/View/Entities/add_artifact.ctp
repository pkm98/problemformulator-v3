<!-- app/View/Entities/add_artifact.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Artifact'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		$types = array('solution_principal' => 'Solution Principal', 'physical_embodiment' => 'Physical Embodiment');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'physical_embodiment'
			)
		);
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('action' => 'add_artifact', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding entity")', 'success' => 'create_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>