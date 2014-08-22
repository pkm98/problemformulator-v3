<?php
// app/Model/Model.php
class PartialOrdering extends AppModel {
	
    public $name = 'PartialOrdering';

	public $belongsTo = array(
/*
        'Before' => array(
            'className' => 'Entity',
            'foreignKey' => 'before_entity_id'
        ),
        'After' => array(
            'className' => 'Entity',
            'foreignKey' => 'after_entity_id'
        ),
*/
	    'ProblemMap' => array(
	        'className'    => 'ProblemMap',
	        'foreignKey'   => 'problem_map_id'
	    )
    );

    public $validate = array(
		'entity_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'An entity is required'
		        )
		),
		'other_entity_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'Another entity is required'
		        )
		),
		'type' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => "An ordering type is required"
			)
		),
		'problem_map_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A problem map is required'
		        )
		)
    );
}