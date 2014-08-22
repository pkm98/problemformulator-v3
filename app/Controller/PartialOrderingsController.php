<?php
// Controller/PartialOrderingsController.php
class PartialOrderingsController extends AppController {
	
	
	// used to output XML and JSON
	public $components = array('RequestHandler');
	
	// uses these models
	public $uses = array('Entity', 'PartialOrdering');
	
	// allows all users to use this controller
	// TODO this is too much access.
	public function isAuthorized($user){
		return true;
	}

	// get all the partial orderings
    public function index() {
        $PartialOrderings = $this->PartialOrdering->find('all');
        $this->set(compact('PartialOrderings'));
		$this->set('_serialize', array('PartialOrderings'));
    }

	// get a specified partial ordering
    public function view($id) {
        $PartialOrdering = $this->PartialOrdering->findById($id);
        $this->set(compact('PartialOrdering'));
		$this->set('_serialize', array('PartialOrdering'));
    }

	// add a partial ordering take an entity id
    public function add($id) {
		// check if there is data to save
		if(!empty($this->request->data)){
			
			// set the id
			$this->request->data['PartialOrdering']['entity_id'] = $id;
			
			// get the entity
			$e = $this->Entity->findById($id);
			
			// set the problem map id
			$this->request->data['PartialOrdering']['problem_map_id'] = $e['Entity']['problem_map_id'];
			
			// check if the partial ordering saved successfully
	        if ($this->PartialOrdering->save($this->request->data)) {
	            $message = 'Saved';
				// get the partial ordering
				$po = $this->PartialOrdering->read();
				// write log output
				parent::log_entry($po['PartialOrdering']['problem_map_id'], "Added partial_ordering(" . $po['PartialOrdering']['id'] . ", " . $po['PartialOrdering']['entity_id'] . ", " . $po['PartialOrdering']['type'] . ", " . $po['PartialOrdering']['other_entity_id'] . ")");
	        } else {
	            $message = 'Error';
	        }
	
			// set the view variable
	        $this->set(compact("message"));
			// set output for JSON and XML
			$this->set('_serialize', array('message'));
		}
		else{
			// find the entity partial ordering is going to be added to
			$entity = $this->Entity->findById($id);
			$entities = $this->Entity->find('all', array(
			        'conditions' => array(
						'Entity.type' => $entity['Entity']['type'],
						'Entity.problem_map_id' => $entity['Entity']['problem_map_id'],
						'Entity.id <>' => $entity['Entity']['id']
					)
			));
			// set the view variables
			$this->set(compact('id'));
			$this->set(compact("entities"));
		}
    }

	// edit a partial ordering
    public function edit($id) {
		
        $this->PartialOrdering->id = $id;

		// check if there is data to save
		if(!empty($this->request->data)){
			// check if the partial ordering saved successfully
	        if ($this->PartialOrdering->save($this->request->data)) {
	            $message = 'Saved';
	
				// read the partial ordering to variable
				$po = $this->PartialOrdering->read();
				
				// write log output
				parent::log_entry($po['PartialOrdering']['problem_map_id'], "Edited partial_ordering(" . $po['PartialOrdering']['id'] . ", " . $po['PartialOrdering']['entity_id'] . ", " . $po['PartialOrdering']['type'] . ", " . $po['PartialOrdering']['other_entity_id'] . ")");
	        } else {
	            $message = 'Error';
	        }
	
			// set the view variable
	        $this->set(compact("message"));
			// set for JSON and XML display
			$this->set('_serialize', array('message'));
		}
		else{
			$entities = $this->Entity->find('all');
			$this->set(compact("entities"));
		}
		
    }

	// delete a partial ordering
    public function delete($id) {
	
		// find the partial ordering
		$po = $this->PartialOrdering->findById($id);
		
		// extract information for log output
		$pid = $po['PartialOrdering']['id'];
		$pmap_id = $po['PartialOrdering']['problem_map_id'];
		$eid = $po['PartialOrdering']['entity_id'];
		$type = $po['PartialOrdering']['type'];
		$oeid = $po['PartialOrdering']['other_entity_id'];

		// check if deleted successfully
        if ($this->PartialOrdering->delete($id)) {
            $message = 'Deleted';
			// write output to log file
			parent::log_entry($pmap_id, "Added partial_ordering(" . $pid . ", " . $eid . ", " . $type . ", " . $oeid . ")");
        } else {
            $message = 'Error';
        }
		
		// set view variable
        $this->set(compact("message"));
		// set output for JSON and XML
		$this->set('_serialize', array('message'));
    }
}