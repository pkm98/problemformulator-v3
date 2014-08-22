<?php
// Controller/AttributesController.php
class AttributesController extends AppController {
	
	// uses the request handler helper for JSON and XML.
	public $components = array('RequestHandler');

	public function isAuthorized($user = NULL) {
        //TODO Make the access more restricted.
        return true;
    }
	
	// gets all of the attributes
    public function index() {
		
		// find all attributes.
        $Attributes = $this->Attribute->find('all');

		// sets in view variable
        $this->set(compact('Attributes'));

		// sets for JSON and XML display
		$this->set('_serialize', array('Attributes'));
    }

	// gets the specified attribute
    public function view($id) {
	
		// find the specified attribute
        $Attribute = $this->Attribute->findById($id);

		// set in view variable
        $this->set(compact('Attribute'));

		// set for JSON and XML display
		$this->set('_serialize', array('Attribute'));
    }

	// create a new attribute
    public function add() {
		// check if the attribute was saved successfully
        if ($this->Attribute->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }

		// set in view variable
        $this->set(compact("message"));

		// set for JSON and XML display
		$this->set('_serialize', array('message'));
    }

	// edit an attribute
    public function edit($id) {
		// load specified attribute
        $this->Attribute->id = $id;

		// check if attribute changes were saved successfully
        if ($this->Attribute->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }

		// set in view variable
        $this->set(compact("message"));

		// set for JSON and XML display
		$this->set('_serialize', array('message'));
    }

	// delete an attribute
    public function delete($id) {
	
		// check if attribute was deleted successfully
        if ($this->Attribute->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
		
		// set in view variable
        $this->set(compact("message"));

		// set for JSON and XML display
		$this->set('_serialize', array('message'));
    }
}