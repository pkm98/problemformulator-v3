<?php
// app/Model/Attribute.php
class Attribute extends AppModel {
	
    //public $name = 'Attribute';

	public $belongsTo = array(
        'Entity' => array(
            'className' => 'Entity'
        ),
	    'ProblemMap' => array(
	        'className'    => 'ProblemMap'
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
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 500),
				'message' => 'type must be less than 500 characters long'
			)
		),
		'problem_map_id' => array(
       	 	'required' => array(
		            'rule' => array('notEmpty'),
		            'message' => 'A type is required'
		        )
		)
    );
}