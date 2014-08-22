<?php
// Controller/DecompositionsController.php
class DecompositionsController extends AppController {
	
	public $uses = array('Decomposition', 'Entity');
	public $components = array('RequestHandler');

	public function isAuthorized($user = NULL){
        // TODO restrict access
		return true;
	}

    public function index() {
        $conditions = array();
        foreach ($this->params['url'] as $key => $value){
            $conditions['Decomposition.' . $key] = $value;    
        }
        $Decompositions = $this->Decomposition->find('all', array('recursive' => -1, 'conditions' => $conditions));
        $this->set(compact('Decompositions'));
		$this->set('_serialize', 'Decompositions');
    }

    public function view($id) {
        $this->Decomposition->recursive = -1;
        $Decomposition = $this->Decomposition->findById($id);
        $this->set(compact('Decomposition'));
		$this->set('_serialize', 'Decomposition');
    }

    public function add() {
        if ($this->Decomposition->save($this->request->data)) {
            $message = $this->Decomposition->read();
            $this->log_entry($this->Decomposition->field('problem_map_id'), "DecompositionsController, add, " . $this->Decomposition->field('id') . ", " . $this->Decomposition->field('entity_id'));
        } else {
            $message = 'Error';
        }
        $this->set(compact("message"));
		$this->set('_serialize', 'message');
    }

    public function edit($id) {
        $this->Decomposition->id = $id;
        if ($this->Decomposition->save($this->request->data)) {
            $message = $this->Decomposition->read();
            $this->log_entry($this->Decomposition->field('problem_map_id'), "DecompositionsController, edit, " . $id . ", " . $this->Decomposition->field('entity_id'));
        }
        else {
            $message = 'Error';
        }
        $this->set(compact("message"));
        $this->set('_serialize', 'message');
    }

    public function delete($id) {

        //TODO might want to log the dependent models that get deleted. 
        $this->Decomposition->id = $id;
        $this->log_entry($this->Decomposition->field('problem_map_id'), "DecompositionsController, delete, " . $id);
        if ( $this->Decomposition->delete($id) ) {
            $message = 'Deleted';
        }
        else {
            $message = 'Error';
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }
}
