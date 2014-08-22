<!-- app/View/Entities/add.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Behavior'); ?></legend>
    <?php
        echo $this->Form->input('Entity.name');
		$types = array('behavior' => 'Behavior', 'equation' => 'Equation', 'parameter' => 'Parameter');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'behavior'
			)
		);
		/*$types = array('abstract' => 'abstract', 'qualitative' => 'qualitative', 'quantitative' => 'quantitative');
		echo $this->Form->input('Attribute.subtype', array(
			'options' => $types,
			'default' => 'abstract'
			)
		);
		*/
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('action' => 'add_behavior', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding entity")', 'success' => 'create_entity(data["message"]["Entity"]); redraw_groups();'));
	echo $this->Form->end();
?>
</div>