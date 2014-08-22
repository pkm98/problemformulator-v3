<!-- app/View/Entities/add_artifact.ctp -->
<div id="success" style="display:none;"></div>
<div class="entity form">
<?php echo $this->Form->create();?>
    <fieldset>
        <legend><?php echo __('Add Partial Ordering'); ?></legend>
    <?php

		$options = array();
		$types = array('before' => 'Before', 'concurrently_with' => 'Concurrently with', 'after' => 'After');
		foreach($entities as $entity)
		{
		    $options[$entity['Entity']['id']] = $entity['Entity']['name'];
		}
		
		echo $this->Form->input('PartialOrdering.type', array(
			'options' => $types,
			'default' => 'before'
			)
		);
		
		echo $this->Form->input('PartialOrdering.other_entity_id', array(
			'options' => $options
			)
		);
    ?>
    </fieldset>
<?php 
	echo $this->Js->submit('Submit', array('url' => array('controller' => 'PartialOrderings', 'action' => 'add', 'ext' => 'json', $id), 'before' => '$("#modal").dialog("close")', 'error' => 'alert("error adding partial ordering")', 'success' => 'console.log(data);'));
	echo $this->Form->end();
?>
</div>