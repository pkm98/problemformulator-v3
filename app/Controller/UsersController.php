<?php
// app/Controller/UsersController.php
class UsersController extends AppController {
	var $name = 'Users';
	// uses these models
	public $uses = array('ProblemMap', 'User');

	public function isAuthorized($user = NULL) {
        //TODO Make the access more restricted.
        return true;
    }
	
	// allow users to add new users and logout
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout');
    }

	// user login
	public function login() {
		// check if post
	    if ($this->request->is('post')) {
			// check if login successful
	        if ($this->Auth->login()) {
				// redirect user
	            $this->redirect($this->Auth->redirect());
	        } else {
				// give error logging in message
	            $this->Session->setFlash(__('Invalid email or password, try again'));
	        }
	    }
	}	

	// user logout
	public function logout() {
		// redirect user and logout
	    $this->redirect($this->Auth->logout());
	}

	// add a user
    public function add() {
		// check if a post request
        if ($this->request->is('post')) {
			//print_r($this->request->data);
			if($this->request->data['User']['agree'] == 1){
					
				// create new user
	            $this->User->create();

				// check if user saved successfully
	            if ($this->User->save($this->request->data)) {
	
					// log the user manually into their created account
					$id = $this->User->id;
					$this->request->data['User'] = array_merge($this->request->data['User'], array('id' => $id));
			        $this->Auth->login($this->request->data['User']);
                
					// set message letting user know their user account has beenc reated
					$this->Session->setFlash(__('The user has been saved'));
					// redirect the user to the index page
	                $this->redirect(array('controller' => 'ProblemMaps', 'action' => 'index'));
			
	            } else {
					// let the user know their user account could not be created.
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }
			}
			else{
				$this->Session->setFlash(__('To create an account you must agree to the terms of use.'));
			}
			
        }
    }


}
