<?php

// Controller/LogEntriesController.php
class LogEntriesController extends AppController {

    public $components = array('RequestHandler');

    public function index() {
        $logentries = $this->LogEntry->find('all');
        $this->set(array(
            'logentries' => $logentries,
            '_serialize' => array('logentries')
        ));
    } 

    public function view($id) {
        $logentry = $this->LogEntry->findById($id);
        $this->set(array(
            'logentry' => $logentry,
            '_serialize' => array('logentry')
        ));
    }

    public function edit($id) {
        $this->LogEntry->id = $id;
        if ($this->LogEntry->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function delete($id) {
        if ($this->LogEntry->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }
}

?>
