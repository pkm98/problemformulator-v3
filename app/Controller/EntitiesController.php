<?php

// Controller/EntitiesController.php

class EntitiesController extends AppController {

    // used to display in XML and JSON
    public $components = array(
        'RequestHandler'
    );

    // models that are used
    var $uses = array(
        'ProblemMap',
        'Entity',
        'Decomposition',
        'Attribute',
        'Link'
    );

    public function isAuthorized($user = NULL) {
        //TODO Make the access more restricted.
        return true;
    }

    // list all entities
    public function index() {
        $conditions = array();
        foreach ($this->params['url'] as $key => $value){
            if ($value === 'null')
                $value = null;
            $conditions['Entity.' . $key] = $value;    
        }
        $Entities = $this->Entity->find('all', array('recursive' => -1, 'conditions' => $conditions));
        $this->set(compact('Entities'));
        $this->set('_serialize', 'Entities');
    }

    // view a specific entity
    public function view($id) {
        $this->Entity->recursive = -1;
        $Entity = $this->Entity->findById($id);
        $this->set(compact('Entity'));
        $this->set('_serialize', 'Entity');
    }

    public function add() {

        if ($this->Entity->save($this->request->data)) {
            $message = $this->Entity->read();
            $this->log_entry($this->Entity->field('problem_map_id'), 
            "EntitiesController, add, "
            . $this->Entity->field('id') . ", " 
            . $this->Entity->field('name') . ", " 
            . $this->Entity->field('type') . ", " 
            . $this->Entity->field('decomposition_id') . ", " 
            . $this->Entity->field('current_decomposition'));
        }
        else {
            $message = 'Error';
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }

    public function edit($id) {

        $this->Entity->id = $id;
        if ($this->Entity->save($this->request->data)) {
            $message = $this->Entity->read();
            $this->log_entry($this->Entity->field('problem_map_id'), 
            "EntitiesController, edit, "
            . $this->Entity->field('id') . ", " 
            . $this->Entity->field('name') . ", " 
            . $this->Entity->field('type') . ", " 
            . $this->Entity->field('decomposition_id') . ", " 
            . $this->Entity->field('current_decomposition'));
        }
        else {
            $message = 'Error';
        }
        $this->set(compact("message"));
        $this->set('_serialize', 'message');
    }

    // delete entity
    public function delete($id) {

        // TODO might want to log the dependent models that get deleted. 
        $this->Entity->id = $id;
        $this->log_entry($this->Entity->field('problem_map_id'), "EntitiesController, delete, " . $id);

        if ( $this->Entity->delete($id) ) {
            $message = 'Deleted';
        }
        else {
            $message = 'Error';
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }
}
