<?php
// app/Model/Model.php
class Link extends AppModel {
	
    public $name = 'Link';

	public $belongsTo = array(
/*
        'From' => array(
            'className' => 'Entity',
            'foreignKey' => 'from_entity_id',
        ),
        'To' => array(
            'className' => 'Entity',
            'foreignKey' => 'to_entity_id',
        ),
*/
	    'ProblemMap' => array(
	        'className' => 'ProblemMap'
	    )
    );

    public $validate = array(
		'type' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A type is required'
		        ),
				'maxLength' => array(
					'rule' => array('maxLength', 100),
					'message' => 'type must be less than 100 characters long'
				)
		),
		'from_entity_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A from entity is required'
		        )
		),
		'to_entity_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A to entity is required'
		        )
		)
    );
}