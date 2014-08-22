<?php
// app/Model/User.php
class User extends AppModel {
	
	// name of the model
    public $name = 'User';

	// relationships
	public $hasMany = array(
	        'ProblemMaps' => array(
	            'className'     => 'ProblemMap',
	            'foreignKey'    => 'user_id',
	            'order'         => 'problem_maps.name DESC',
	            'dependent'     => true
	        )
	    );

	// validation performed on the model
    public $validate = array(
		'email' => array(
        	'validEmail' => array(
	            'rule' => 'email',
				'message' => "A valid email is required"
	        ),
	        'uniqueEmail' => array(
	            'rule' => 'isUnique',
				'message' => "Email address already registered with our system"
	            // extra keys like on, required, etc. go here...
	        )
		),
		'firstname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A firstname is required'
            ),
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Firstname must be less than 50 characters long'
			)
        ),
		'lastname' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A lastname is required'
            ),
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Lastname must be less than 50 characters long'
			)
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        )
    );

	// before saving a user hash the password
	public function beforeSave( $options = Array() ) {
	    if (isset($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
	    }
	    return true;
	}
}
