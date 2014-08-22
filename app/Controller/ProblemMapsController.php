<?php
App::uses('File', 'Utility');
require_once("../Config/database.php");

// Controller/ProblemMapsController.php
class ProblemMapRank {
	public $id;
	public $decomposition_id;
	public $name;
	public $type;
	public $current_decomposition;
	public $problem_map_id;
	public $thelinks=array();
	public $children1=array();
}


class ProblemMapsController extends AppController {

    // used for XML and JSON output
    public $components = array(
        'RequestHandler'
    );

    // models used
    public $uses = array(
        'ProblemMap',
        'LogEntry',
		'Entity',
		'Decomposition',
		'EntitySubtypes',
		'TutorialType',
		'TutorialPrompt'
    );

    // determine if the file extension is prolog and if so set the appropriate layout
    public function beforeFilter() {

        parent::beforeFilter();
        $this->RequestHandler->setContent('pl', 'text/pl');
    }

    // check if the user is authorized
    public function isAuthorized($user = NULL) {


        // if they are viewing the list or adding problem maps
        // then they are allowed


        if (in_array($this->action, array(
            'index',
            'add',
			'tutorial_prompts'
        ))) {
            return true;
        }

        // The owner of a post can edit and delete it

        if (in_array($this->action, array(
            'view',
            'view_list',
            'view_graph',
            'view_graphNew',
            'edit',
            'delete',
            'view_log',
            'getInvalidEntities',
			'tutorial_switch',
			'view_objtree',
			'tree_traversal_view_objtree',
			'print_objtree',
			'getChildrenEntities',
			'getChildrenDecomps',
			'save_objtree_weights'
        ))) {
            $pmapId = $this->request->params['pass'][0];

            if ($this->ProblemMap->isOwnedBy($pmapId, $user['id'])) {
                return true;
            }
        }

        // this enables the admin to access everything
        return parent::isAuthorized($user);
    }

    // gets the invalid entities using ASP and the checker rules
    public function getInvalidEntities($id) {

        $ProblemMap = $this->ProblemMap->findById($id);

        // save problem map to view variables
        $this->set(compact('ProblemMap'));

        // and for XML / JSON display
        $this->set('_serialize', array(
            'ProblemMap'
        ));

        // create a new view
        $view = new View($this);

        // render the problem map as a prolog file
        $viewdata = $view->render('pl/view', 'pl/default', 'pl');

        //set the file name to save the View's output
        $path = WWW_ROOT . 'files/pl/' . $id . '.pl';
        $file = new File($path, true);

        //write the content to the file
        $file->write($viewdata);

        //echo 'clingo -n 0 ' . WWW_ROOT . 'files/pl/' . $id . '.pl' . " " . WWW_ROOT . 'pl/completeness_rules.pl';
        // call clingo on the file and get the invalid entities.

        //$invalid_string = shell_exec('clingo -n 0 ' . WWW_ROOT . 'files/pl/' . $id . '.pl' . " " . WWW_ROOT . 'pl/completeness_rules.pl' . " | grep 'invalid'");

        $invalid_string = shell_exec('ulimit -t 15; clingo -n 0 ' . WWW_ROOT . 'files/pl/' . $id . '.pl' . " " . WWW_ROOT . 'pl/completeness_rules.pl' . " | grep 'invalid'");

        //echo $invalid_string;
        // extract all the invalid ids

        $invalids = explode(" ", trim($invalid_string));

        // clean up the ids
        foreach ($invalids as & $i) {
            $i = ereg_replace("[^0-9]", "", $i);
        }

        //print_r($invalids);
        //return $invalids;

        // set the view variables

        $this->set(compact('invalids'));

        // and XML/JSON display
        $this->set('_serialize', array(
            'invalids'
        ));

        //echo WWW_ROOT;
        //$this->render('view', 'default', 'pl');

        //$this->render();


    }

    // list all the problem maps
    public function index() {

        // if admin find all problem maps
        if ($this->Auth->user('admin') == 1) {
			$this->set("admin",1);
            $ProblemMaps = $this->ProblemMap->find('all', array('recursive' => 0));
        }
        else {
			$this->set("admin",0);
            // get all the problem maps belonging to the user
            $ProblemMaps = $this->ProblemMap->find('all', array(
                'conditions' => array(
                    'ProblemMap.user_id' => $this->Auth->user('id')
                )
            ));
        }

        // set them in a variable accessible in the view
        $this->set(compact('ProblemMaps'));

        // save them in a format accessible from JSON / XML
        $this->set('_serialize', array(
            'ProblemMaps'
        ));
    }
	
    public function view_list($id) {

        $this->log_entry($id, "ProblemMapsController, view_list, " . $id);

        // retrieve the problem map and set it to a variable accessible in the view
        $ProblemMap = $this->ProblemMap->findById($id);
		
		$this->set(compact('ProblemMap'));

        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'ProblemMap'
        ));

		/* Entity Subtypes - Start */
		$EntitySubtypes = $this->EntitySubtypes->find('all');
		$Entitytypes = $this->EntitySubtypes->find('all', array('fields' => array('DISTINCT EntitySubtypes.type')));
		$subtypes = [];
		foreach($Entitytypes as $Entitytype){
			$subtypes[$Entitytype['EntitySubtypes']['type']] = [];
		}
		foreach($EntitySubtypes as $EntitySubtype){
			array_push($subtypes[$EntitySubtype['EntitySubtypes']['type']], $EntitySubtype['EntitySubtypes']['subtype']);
		}
	
		$this->set(compact('subtypes'));
		/* Entity Subtypes - End */
    }
	
	public function tutorial_prompts($step){
		$TutorialPrompt = $this->TutorialPrompt->find('first', array('conditions' => array('TutorialPrompt.step' => $step)));
		$this->set(compact('TutorialPrompt'));
		
		$neighbors = $this->TutorialPrompt->find('neighbors', array('field' => 'id', 'value' => $TutorialPrompt['TutorialPrompt']['id']));
		
		// print_r($TutorialPrompt);
		// print_r($neighbors);
		
		$prompt_html = '';
		$prompt_html .= '<h4>'.$TutorialPrompt['TutorialType']['name'].' for Formulating a Problem</h4>';
		//$prompt_html .= '<small><i>'.$TutorialPrompt['TutorialType']['description'].'</i></small><br><br><hr>';
		$prompt_html .= '<div id="promptBox">';
			$prompt_html .= '<div id="promptMsg">';
				$prompt_html .= '<b>Step</b>: <span>'.$TutorialPrompt['TutorialPrompt']['description'].'</span>';
				$prompt_html .=	'<br><br>';
				
				$prompt_html .=	'<span>';
					if(count($neighbors['prev']))
						$prompt_html .=	'<button id="promptButton" class="navButton" onclick="tutorial_prompt(\''.$neighbors['prev']['TutorialPrompt']['step'].'\')">Prev</button>';
					else
						$prompt_html .=	'<button id="promptButton" class="navButton disabled" disabled>Prev</button>';

					if(count($TutorialPrompt['TutorialPrompt']['no'])){
						$prompt_html .=	'<span>';
							$prompt_html .=	'<button id="promptButton" class="decisionButton" onclick="tutorial_prompt(\''.$neighbors['next']['TutorialPrompt']['step'].'\')">Yes</button>';
							$prompt_html .=	'<button id="promptButton" class="decisionButton" onclick="tutorial_prompt(\''.$TutorialPrompt['TutorialPrompt']['no'].'\')">No</button>';
						$prompt_html .=	'</span>';
					}
					
					if(count($neighbors['next']))
						$prompt_html .=	'<button id="promptButton" class="navButton" onclick="tutorial_prompt(\''.$neighbors['next']['TutorialPrompt']['step'].'\')">Next</button>';
					else
						$prompt_html .=	'<button id="promptButton" class="navButton disabled" disabled>Next</button>';
				$prompt_html .=	'</span>';
			$prompt_html .=	'</div>';
		$prompt_html .=	'</div>';
		
		echo $prompt_html;
		$this->autoRender = false;
	}
	
	/* Switch Tutorial Prompts On/Off */
	function tutorial_switch($id, $switch_on){
		$data = array('id'=> $id, 'tutorial_on' => $switch_on);
		$this->ProblemMap->save($data);
		$this->autoRender = false;
	}
	
	public function view_objtree($id) {
		$ProblemMap = $this->ProblemMap->findById($id);
		$this->set(compact('ProblemMap'));
		$Entities = $this->Entity->find('all', array(
                'conditions' => array(
                    'Entity.problem_map_id' => $id,
                    'Entity.type' => 'requirement'
                )
            ));
			
		$Decompositions = $this->Decomposition->find('all', array(
                'conditions' => array(
                    'Decomposition.problem_map_id' => $id
                )
        ));
		
		$ent_arr = [];
		$dec_arr = [];
		foreach($Entities as $entity){
			array_push($ent_arr, $entity['Entity']);
		}
		foreach($Decompositions as $decomposition){
			array_push($dec_arr, $decomposition['Decomposition']);
		}

		$data = [];
		$data['name'] = $ProblemMap['ProblemMap']['name'];
		$data['children'] = $this->getChildrenEntities(null, $ent_arr, $dec_arr);
		
		//print_r (json_encode($data));
		// echo "<textarea id='objtree_data' style='display:none;'>";
		// echo json_encode($data);
		// echo "</textarea>";
		
		//echo "<textarea id='objtree' style='display:none;'>";
			$child_count = count($data['children']);
			if($child_count)
				$objtree_html = $this->tree_traversal_view_objtree($data['children'], $data['name'], $child_count);
		//echo "</textarea>";
		
		$this->set('objtree_html', $objtree_html);
	}
	
	public function tree_traversal_view_objtree($dataArr, $name, $child_count){
		$child_arr = [];
		$objtree_html = '';
		$objtree_html .= "<h3 style='text-align: center;'>".$name."</h3>";
		$objtree_html .= "<table cellpadding='5' style='width: auto; margin: 0 auto;'>";
		
		if (strpos($dataArr[0]['name'], 'Decomp') === FALSE)
			$objtree_html .= "<tr><th>Name</th><th>Weight</th></tr>";
		foreach($dataArr as $key => $arr){
			$count = count($arr['children']);
			if (strpos($arr['name'], 'Decomp') !== FALSE){
				$objtree_html .= $this->tree_traversal_view_objtree($arr['children'], '<small>'.$arr['name'].'</small>', $count);
			}
			else {
				if($count > 0){
					array_push($child_arr, $key);
				}
				$objtree_html .= "<tr>";
				$objtree_html .= "<td>".$arr['name']."</td><td>: <span id='".$arr['id']."'>".$arr['weight']."</span></td>";
				$objtree_html .= "<td>";
				$objtree_html .= "<select id='".$arr['id']."' onchange='reset_highlight(this);' style='width:auto;'>";
				//$objtree_html .= "<option disabled> -- select -- </option>";
				for($j = 1; $j <= $child_count ; $j++){
					//$objtree_html .= $arr['weight_option'];
					if($arr['weight_option'] == $j){
						$objtree_html .= "<option value='".$j."' selected>".$j."</option>";
					} else
						$objtree_html .= "<option value='".$j."'>".$j."</option>";
				}
				$objtree_html .= "</select>";
				$objtree_html .= "</td>";
				$objtree_html .= "</tr>";
			}
		}
		$objtree_html .= "</table>";
		$objtree_html .= "<br>";
		foreach($child_arr as $key){
			$objtree_html .= $this->tree_traversal_view_objtree($dataArr[$key]['children'], $dataArr[$key]['name'], $count);
		}
		return $objtree_html;
	}
	
	public function print_objtree($id) {
		$ProblemMap = $this->ProblemMap->findById($id);
		$this->set(compact('ProblemMap'));
		$Entities = $this->Entity->find('all', array(
                'conditions' => array(
                    'Entity.problem_map_id' => $id,
                    'Entity.type' => 'requirement'
                )
            ));
		
		$Decompositions = $this->Decomposition->find('all', array(
                'conditions' => array(
                    'Decomposition.problem_map_id' => $id
                )
        ));
		
		// print_r(json_encode($Decompositions));
		
		$ent_arr = [];
		$dec_arr = [];
		foreach($Entities as $entity){
			array_push($ent_arr, $entity['Entity']);
		}
		foreach($Decompositions as $decomposition){
			array_push($dec_arr, $decomposition['Decomposition']);
		}

		$data = [];
		$data['id'] = 0;
		$data['parent_id'] = null;
		$data['name'] = $ProblemMap['ProblemMap']['name'];
		//array_push($data, $this->getChildrenEntities(null, $ent_arr, $dec_arr))//////////////////
		$data['children'] = $this->getChildrenEntities(null, $ent_arr, $dec_arr);
		//echo json_encode($data);
		//print_r (json_encode($data));
		
		$treedata = json_encode($data);
		
		$this->set('treedata', $treedata);
		// echo "<textarea id='objtree_data' style='display:none;'>";
		// echo ;
		// echo "</textarea>";
	}
	/*----------------------------------------------------*/
	/*----------------------------------------------------*/
	/*----------------------------------------------------*/	
	/*
	public function tree_traversal_print_objtree($dataArr, $name, $child_count){
		$child_arr = [];
		
		if (strpos($dataArr[0]['name'], 'Decomp') === FALSE)
			echo "<tr><th>Name</th><th>Weight</th></tr>";
		foreach($dataArr as $key => $arr){
			$count = count($arr['children']);
			if (strpos($arr['name'], 'Decomp') !== FALSE){
				$this->tree_traversal_print_objtree($arr['children'], '<small>'.$arr['name'].'</small>', $count);
			}
			else {
				if($count > 0){
					array_push($child_arr, $key);
				}
				echo "<tr>";
				echo "<td>".$arr['name']."</td><td>: <span id='".$arr['id']."'>".$arr['weight']."</span></td>";
				echo "<td>";
				echo "<select id='".$arr['id']."'>";
				echo "<option disabled> -- select -- </option>";
				for($j = 1; $j <= $child_count ; $j++){
					echo $arr['weight_option'];
					if($arr['weight_option'] == $j)
						echo "<option value='".$j."' selected>".$j."</option>";
					else
						echo "<option value='".$j."'>".$j."</option>";
				}
				echo "</select>";
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "<br>";
		foreach($child_arr as $key){
			$this->tree_traversal_print_objtree($dataArr[$key]['children'], $dataArr[$key]['name'], $count);
		}
	}
	*/
	public function getChildrenEntities($id, $ent_arr, $dec_arr){
		$children = [];
			
		if($id == null){
			foreach($ent_arr as $ent){
				if($ent['decomposition_id'] == null){
					$data = [];
					$data['id'] = $ent['id'];
					$data['parent_id'] = 0;
					$data['weight'] = $ent['weight'];
					$data['weight_option'] = $ent['weight_option'];
					$data['name'] = $ent['name'];
					$data['children'] = $this->getChildrenDecomps($ent['id'], $ent_arr, $dec_arr);
					array_push($children, $data);
				}
			}
		} else {
			foreach($ent_arr as $ent){
				if($ent['decomposition_id'] == $id){
					//echo json_encode($ent).'<br><br>';
					$data = [];
					$data['id'] = $ent['id'];
					
					foreach($dec_arr as $dec){
						if($dec['id'] == $ent['decomposition_id'])
							$data['parent_id'] = $dec['entity_id'];
					}
					
					$data['weight'] = $ent['weight'];
					$data['weight_option'] = $ent['weight_option'];
					$data['name'] = $ent['name'];
					$data['children'] = $this->getChildrenDecomps($ent['id'], $ent_arr, $dec_arr);
					array_push($children, $data);
				}
			}
		}
		
		return $children;
	}

	public function getChildrenDecomps($id, $ent_arr, $dec_arr){
		//console.log("dec");
		$children = [];

		foreach($dec_arr as $dec){
			if($dec['entity_id'] == $id){
				$data = [];
				$data['name'] = 'Decomp' . $dec['id'];
				$data['children'] = $this->getChildrenEntities($dec['id'], $ent_arr, $dec_arr);
				array_push($children, $data);
			}
		}
		return $children;
	}

	public function save_objtree_weights($id, $weight, $weight_option){
		$data = array('id'=> $id, 'weight' => $weight, 'weight_option' => $weight_option);
		$this->Entity->save($data);
		
		$this->autoRender = false;
	}
	/*----------------------------------------------------*/
	/*----------------------------------------------------*/
	/*----------------------------------------------------*/
    public function view_graph($id) {

        $this->log_entry($id, "ProblemMapsController, view_graph, " . $id);

        // retrieve the problem map and set it to a variable accessible in the view
        $ProblemMap = $this->ProblemMap->findById($id);
        $this->set(compact('ProblemMap'));
		
        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'ProblemMap'
        ));
    }
	
	//Create a new graph view by Zongkun
	public function view_graphNew($id) {
		//For nodes and children
		$array = array();
		$return_arr = array();
		
		//$conn=mysql_connect("localhost","root","root");
		//$select=mysql_select_db("problemformulator",$conn);
		
		$dbclass = new DATABASE_CONFIG;
		$conn = $dbclass->getConnection();
		
		//Requirements----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'requirement'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//User Scenario----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'usescenario'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//Functions----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'function'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//Artifacts----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'artifact'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//Behaviors----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'behavior'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//Issues----------------
		$fetch = mysql_query("SELECT * FROM `entities` where problem_map_id = $id and type = 'issue'"); 
		while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
			$e = new ProblemMapRank;
			$e->id = $row['id'];
			$e->decomposition_id = $row['decomposition_id'];
			$e->name = $row['name'];
			$e->type = $row['type'];
			$e->current_decomposition = $row['current_decomposition'];
			$e->problem_map_id = $row['problem_map_id'];
			$array[] = $e;
		}
		//print_r($array);
		//echo json_encode($array);
		foreach ($array as $e) {
    		//unset($array[$i]);
    		
    		foreach ($array as $tmp){
    			if($tmp->decomposition_id!=null){
    				if($e->current_decomposition!=null){
		    			if($tmp->decomposition_id == $e->current_decomposition ){
							$e->children1[] = $tmp->name;
		    			}
					}
    			}
    		//echo json_encode($tmp->decomposition_id);
    		//echo json_encode($e->current_decomposition);
    		//echo json_encode($e->children);
    		}
    		// print_r(" ;name up: ");
			// echo json_encode($e->name);
			// print_r("children: ");
			// echo json_encode($e->children);
			//echo json_encode($e);
		}
		//For links
		$linkFetch = mysql_query("SELECT * FROM `links` where problem_map_id = $id"); 
		while ($row = mysql_fetch_array($linkFetch, MYSQL_ASSOC)) {
			//echo json_encode($row['from_entity_id']);
			foreach ($array as $e) {
				if($row['from_entity_id']==$e->id){
					//$e->thelinks[] = $row['to_entity_id'];
					foreach ($array as $tmp) {
						if($tmp->id == $row['to_entity_id']){
							$e->thelinks[] = $tmp->name;
						}
					}
				}
			}
		}
		$outPutJson =  json_encode($array);
		file_put_contents('problemMapStructure.json',$outPutJson);
		
		
         $this->log_entry($id, "ProblemMapsController, view_graph_2, " . $id);

         // retrieve the problem map and set it to a variable accessible in the view
         $ProblemMap = $this->ProblemMap->findById($id);
         $this->set(compact('ProblemMap'));
 		
         // this is for JSON and XML requests.
         $this->set('_serialize', array(
             'ProblemMap'
         ));
    }
		
	public function view_predicate($id) {

        $this->log_entry($id, "ProblemMapsController, view_predicate, " . $id);

        // retrieve the problem map and set it to a variable accessible in the view
        $ProblemMap = $this->ProblemMap->findById($id);
		
        $this->set(compact('ProblemMap'));

        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'ProblemMap'
        ));
    }
	
	public function view_text($id) {

        $this->log_entry($id, "ProblemMapsController, view_predicate, " . $id);

        // retrieve the problem map and set it to a variable accessible in the view
        $ProblemMap = $this->ProblemMap->findById($id);
        $this->set(compact('ProblemMap'));

        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'ProblemMap'
        ));
    }

    public function view($id) {

        // retrieve the problem map and set it to a variable accessible in the view
        $ProblemMap = $this->ProblemMap->findById($id);
        $this->set(compact('ProblemMap'));

        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'ProblemMap'
        ));
    }

    public function view_log($id) {

        // retrieve the problem map log entries and set it to a variable accessible in the view
        $Log = $this->LogEntry->find('all', array(
            'conditions' => array(
                'LogEntry.problem_map_id =' => $id
            )
        ));
        $this->set(compact('Log'));

        // this is for JSON and XML requests.
        $this->set('_serialize', array(
            'Log'
        ));
    }

    public function add() {
		$this->Session->setFlash('.....');
        $error = false;

        // check if the data is being posted (submitted).

        if ($this->request->is('post')) {

            // get the logged in user id
            $this->request->data['ProblemMap']['user_id'] = $this->Auth->user('id');

            // start database transaction
            $this->ProblemMap->begin();

            // Save Problem Map

            if (!$this->ProblemMap->save($this->request->data)) {
                $error = true;
            }

            // handle transaction and message

            if ($error) {

                // rollback transaction
                $this->ProblemMap->rollback();
                $message = 'Error';

                // set message to be displayed to user via CakePHP flash
                $this->Session->setFlash('Unable to create problem map.');
            }
            else {

                // commit transaction
                $this->ProblemMap->commit();
                $message = 'Saved';

                // set message to be displayed to user via CakePHP flash
                $this->Session->setFlash('Your Problem Map has been created.');

                // redirec the user
                $this->redirect(array(
                    'action' => 'index'
                ));
            }

            // this is for JSON and XML requests.
            $this->set(compact("message"));
            $this->set('_serialize', array(
                'message'
            ));
        }
    }

    // edit the problem map
    public function edit($id) {

        $this->log_entry($id, "ProblemMapsController, edit, " . $id);

        // retrieve the current problem map if loading the form.
        $this->ProblemMap->id = $id;

        // check if get request (not submitting)

        if ($this->request->is('get')) {
            $this->request->data = $this->ProblemMap->read();
        }
        else {

            // here if the data has been posted. Save the new data and return result.

            if ($this->ProblemMap->save($this->request->data)) {
                $this->Session->setFlash('Your problem map has been updated.');
                $this->redirect(array(
                    'action' => 'index'
                ));
                $message = 'Saved';
            }
            else {
                $this->Session->setFlash('Unable to update your post.');
                $message = 'Error';
            }
        }

        // this is for JSON and XML requests.
        $this->set(compact("message"));
        $this->set('_serialize', array(
            'message'
        ));
    }

    // delete problem map
    public function delete($id) {

        $this->log_entry($id, "ProblemMapsController, delete, " . $id);

        // cannot delete with a get request (only POST).

        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        // delete the problem map and return the result.

        if ($this->ProblemMap->delete($id)) {

            // set message to display to user
            $this->Session->setFlash('The problem map with id: ' . $id . ' has been deleted.');

            // redirect user back to index
            $this->redirect(array(
                'action' => 'index'
            ));
            $message = 'Deleted';
        }
        else {
            $message = 'Error';
        }

        // this is for JSON and XML requests.
        $this->set(compact("message"));
        $this->set('_serialize', array(
            'message'
        ));
    }
}
