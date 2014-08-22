<?php
// app/Model/Model.php
class ProblemMap extends AppModel {
	
	// model name
    public $name = 'ProblemMap';

	// model relationships
    public $belongsTo = array(
        'User' => array(
            'className'    => 'User',
            'foreignKey'   => 'user_id'
        )
	);
	
	public $hasMany = array(
	        'Entities' => array(
				'className' => 'Entity',
				'dependent' => true
			),
			'Links' => array(
				'className' => 'Link',
				'dependent' => true
			),
			'Decompositions' => array(
				'className' => 'Decomposition',
				'dependent' => true
			),
			'PartialOrderings' => array(
				'className' => 'PartialOrdering',
				'dependent' => true
			),
			'Attributes' => array(
				'className' => 'Attribute',
				'dependent' => true
			),
			'LogEntries' => array(
				'className' 	=> 'LogEntry',
				'dependent'		=> true
			)
	);

	// model validation
    public $validate = array(
		'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A name is required'
            )
        )
    );

	// method to determine if a user owns a problem map
	public function isOwnedBy($pmap, $user) {
		//return true;
	    return $this->field('id', array('id' => $pmap, 'user_id' => $user)) === $pmap;
	}
	
}