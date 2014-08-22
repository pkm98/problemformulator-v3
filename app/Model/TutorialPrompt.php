<?php
// app/Model/TutorialPrompt.php
class TutorialPrompt extends AppModel {
	public $belongsTo = array(
		'TutorialType' => array(
			'className' => 'TutorialType',
			'foreignKey' => 'tutorial_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}