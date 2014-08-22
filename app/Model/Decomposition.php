<?php
// app/Model/Model.php
class Decomposition extends AppModel {
	
    public $name = 'Decomposition';

	public $belongsTo = array(
        'Entity' => array(
            'className' => 'Entity'
        ),
	    'ProblemMap' => array(
	        'className' => 'ProblemMap'
	    )
    );

    public $hasMany = array(
        'Entities' => array(
            'className' => 'Entity',
            'dependent' => true
        ),
    );

    public $validate = array(
		'entity_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A from entity is required'
		        )
		),
		'problem_map_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A to entity is required'
		        )
		)
    );
}
