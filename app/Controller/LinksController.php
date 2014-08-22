<?php

// Controller/LinksController.php

class LinksController extends AppController {

    // used for displaying in XML and JSON
    public $components = array(
        'RequestHandler'
    );

    // allow all users to access this controller
    // TODO this is probably far too much access.

    public function isAuthorized($user = NULL) {
        return true;
    }

    // get all the links
    public function index() {
        $conditions = array();
        foreach ($this->params['url'] as $key => $value){
            $conditions['Link.' . $key] = $value;    
        }
        $Links = $this->Link->find('all', array('recursive' => -1, 'conditions' => $conditions));
        $this->set(compact('Links'));
        $this->set('_serialize', 'Links');
    }

    // get the specified link
    public function view($id) {
        $this->Link->recursive = -1;
        $Link = $this->Link->findById($id);
        $this->set(compact('Link'));
        $this->set('_serialize', 'Link');
    }

    // add a link
    public function add() {
        if ($this->Link->save($this->request->data)) {
            $message = $this->Link->read();
            $this->log_entry($this->Link->field('problem_map_id'), "LinksController, add, " . $this->Link->field('id') . ", " . $this->Link->field('from_entity_id') . ", " . $this->Link->field('to_entity_id') . ", " . $this->Link->field('type'));
        }
        else {
            $message = 'Failure';
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }

    // edit a link
    public function edit($id) {
        $this->Link->id = $id;
        if ($this->Link->save($this->request->data)) {
            $message = $this->Link->read();
            $this->log_entry($this->Link->field('problem_map_id'), "LinksController, edit, " . $this->Link->field('id') . ", " . $this->Link->field('from_entity_id') . ", " . $this->Link->field('to_entity_id') . ", " . $this->Link->field('type'));
        }
        else {
            $message = 'Error';
        }
        $this->set(compact("message"));
        $this->set('_serialize', 'message');
    }

    // delete a link
    public function delete($id) {

        $this->Link->id = $id;
        $this->log_entry($this->Link->field('problem_map_id'), "LinksController, delete, " . $this->Link->field('id'));
        if ( $this->Link->delete($id) ) {
            $message = 'Deleted';
        }
        else {
            $message = 'Error';
        }
        $this->set(compact('message'));
        $this->set('_serialize', 'message');
    }
}
