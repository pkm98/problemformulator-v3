<?php
// app/Model/Model.php
class Entity extends AppModel {

    public $name = 'Entity';
    public $belongsTo = array(
        'ProblemMap' => array(
            'className'    => 'ProblemMap'
        ),
        'Decomposition' => array(
            'className' => 'Decomposition'
        )
    );

    //TODO for some reason one set of links is not getting deleted.
    public $hasMany = array(
        'OutLinks' => array(
            'className' => 'Link',
            'foreignKey' => 'from_entity_id',
            'dependent' => true
        ),
        'InLinks' => array(
            'className' => 'Link',
            'foreignKey' => 'to_entity_id',
            'dependent' => true
        ),
        'Decompositions' => array(
            'className' => 'Decomposition',
            'dependent' => true
        ),
        'Attributes' => array(
            'className' => 'Attribute',
            'dependent' => true
        )
    );

    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A name is required'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 100),
                'message' => 'Name must be less than 100 characters long'
            )
        ),
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
        'problem_map_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A type is required'
            )
        )
    );

    public function isOwnedBy($eid, $user) {
        //return true;
        return $this->field('id', array('id' => $eid, 'user_id' => $user)) === $eid;
    }
}
