<?php 

class LogEntry extends AppModel {

	public $belongsTo = array(
	    'ProblemMap' => array(
	        'className'    => 'ProblemMap'
	    )
    );

}