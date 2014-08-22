// global variables
var paper = null;
var problem_map = null;
var decompositions = {};
var entities = {};
var links = new Array();
var clicks = 0; // used for double click vs single click detection.
var stuck = false;
var entityNames = Array();
var searchActive = false;
var searchFocus = null;
var stuckFocus = null;
var dragging = false;
var invalids = null;

// for highlighting
var highlightyellow = "#ffff00";
var highlightblue = "#0000ff";
var highlightred = '#F5A9BC';
//var highlightred = "#e77471"; // darker red

// for groups
var groups = {};

$(function(){
	// first function called to start the entire shebang.
	initialize();
});

// initialize and load everything.
function initialize(){
	// Creates canvas
	paper = Raphael('canvas_container', "100%", '200%');
	
	// unhighlight this line if you want zoom and pan functionality... not needed.
	//var zpd = new RaphaelZPD(paper,{zoom: true, pan:true, drag: true});
	
	// Load Problem Map
	load_problem_map();
	
}

// create an entity given json returned by cake
function create_entity(entity){
	//console.log(entity);
	var e = new Entity(entity);
	entities[e.id] = e;
	
	groups[e.type].add(e);
	
	getEntityNames();
	$("#search-name").autocomplete({source:entityNames});
	
}

// update an entity give json returned by cake
function update_entity(entity){
	var e = new Entity(entity);
	if(entities[e.id]){
		e.inLinks = entities[e.id].inLinks;
		e.outLinks = entities[e.id].outLinks;
	}
	entities[e.id] = e;
	groups[e.type].update(e);
	
	getEntityNames();
	$("#search-name").autocomplete({source:entityNames});

}

// delete an entity and associated links and redraw given
// an entity id.
function delete_entity(eid){
	var type = entities[eid].type;
	
	for(i in entities[eid].outLinks){
		for(j in entities[entities[eid].outLinks[i]].inLinks){
			if(entities[entities[eid].outLinks[i]].inLinks[j] == eid){
				entities[entities[eid].outLinks[i]].inLinks.splice(j,1);
				//break;
			}
		}
	}
	for(i in entities[eid].inLinks){
		for(j in entities[entities[eid].inLinks[i]].outLinks){
			if(entities[entities[eid].inLinks[i]].outLinks[j] == eid){
				entities[entities[eid].inLinks[i]].outLinks.splice(j,1);
				//break;
			}
		}
	}
	
	delete entities[eid];
	groups[type].remove(eid);
	
	getEntityNames();
	$("#search-name").autocomplete({source:entityNames});
	
	redraw_groups();
}

// draw the problem map
function draw_problem_map(){
	
	// draw groups
	draw_groups(paper);
	
	// store decompositions
	for(i in problem_map['Decompositions']){
		decompositions[problem_map['Decompositions'][i]['id']] = problem_map['Decompositions'][i];
		//console.log(problem_map['Decompositions'][i]);
	}
	//console.log(decompositions);
	
	// create entities
	for(i in problem_map['Entities']){
		create_entity(problem_map['Entities'][i]);
	}
	//console.log(entities);
	
	// redraw groups
	redraw_groups();
	
	// draw links
	
	for (i in problem_map['Links']){
		//console.log(problem_map['Links'][i]['from_entity_id']);
		//console.log(i);
		//console.log(problem_map['Links']);
		if(entities[problem_map['Links'][i]['from_entity_id']]){
			entities[problem_map['Links'][i]['from_entity_id']].outLinks.push(problem_map['Links'][i]['to_entity_id']);
			entities[problem_map['Links'][i]['to_entity_id']].inLinks.push(problem_map['Links'][i]['from_entity_id']);
		}
		//links.push(paper.connection(entities[problem_map['Links'][i].from_entity_id].rjsElement, entities[problem_map['Links'][i].to_entity_id].rjsElement, "#fff"));
	}
	
}

// load the problem map data and then draw it.
function load_problem_map(){
	var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname + ".json";
	$.getJSON(newURL, function(data) {
		problem_map = data['ProblemMap'];
		draw_problem_map();
	});
}

// draw each of the groups.
function draw_groups(paper){
	
	groups['requirement'] = new Group("Requirements", paper.set(), 0);
	groups['requirement'].create(paper);
	
	groups['function'] = new Group("Functions", paper.set(), 1);
	groups['function'].create(paper);
	
	groups['artifact'] = new Group("Artifacts", paper.set(), 2);
	groups['artifact'].create(paper);
	
	groups['behavior'] = new Group("Behaviors", paper.set(), 3);
	groups['behavior'].create(paper);
	
	groups['issue'] = new Group("Issues", paper.set(), 4);
	groups['issue'].create(paper);
}

// get all the invalids and then execute the callback function
function update_invalids(callback){
	// add annotations
	//console.log('../getInvalidEntities/' + problem_map['ProblemMap']['id'] + '.json');
	$.getJSON('../getInvalidEntities/' + problem_map['ProblemMap']['id'] + '.json', function(data) {
		//console.log("getting annotations");
		//console.log(invalids);
		invalids = data['invalids'];
		
		if(callback != null)
			callback();
	})
}

// redraw all of the groups (used after updating something).
function redraw_groups(){
	
	update_invalids(function(){
		for(i in groups){
			//console.log(groups[i]);
			groups[i].redraw();
		}
	});
	
}

// resize the groups on window resize
$(window).resize(function() {
	
	for(i in groups){
		groups[i].resize();
	}

});

// context Menu
$.contextMenu({
  selector: '.context-menu-one', 
  build: function($trigger) {
    var options = {
      callback: function(key, options) {
		if(key == 'addpo'){
			//console.log(subdirectory + '/partial_orderings/' + $trigger.attr('eid'));
			$( "#modal" ).load(subdirectory + '/partial_orderings/add/' + $trigger.attr('eid')).dialog({
					height: 375,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add partial ordering"
			});
		}
		else if(key == 'rename'){
			var h = 300;
			if(entities[$trigger.attr('eid')].type == 'requirement'){
				h = 600;
			}
			else if (entities[$trigger.attr('eid')].type == 'artifact' || entities[$trigger.attr('eid')].type == 'behavior' ||
					 entities[$trigger.attr('eid')].type == 'issue'){
				h = 375;
			}
			//console.log($trigger.attr('eid'));
			
			$( "#modal" ).load(subdirectory + '/entities/edit_' + entities[$trigger.attr('eid')].type + '/' + $trigger.attr('eid')).dialog({
					height: h,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Edit Entity"
			});
			//console.log('edit');
		}
		else if(key == 'delete'){
			if(confirm("Are you sure you want to delete this entity?")){
				//console.log('delete');
				var newURL = '../../Entities/delete/' + $trigger.attr('eid') + '.json';
				//console.log(newURL);
				$.getJSON(newURL, function(data) {
					//console.log(data);
					//console.log("RETURNED!");
					if(data['message'] == 'Deleted'){
						if(entities[$trigger.attr('eid')].parent_decomposition != null && entities[$trigger.attr('eid')].parent_decomposition != "null")
							entities[$trigger.attr('eid')].removeFromCurrentDecomposition();
						delete_entity($trigger.attr('eid'));
					}
				});
				
			}
		}
		else if(key != 'sep1'){
			//console.log('delete link');
			//console.log(key);
			delete_link($trigger.attr('eid'), key);
			delete_link(key,$trigger.attr('eid'));
		}
      },
      items: {}
    };
	
	options.items["addpo"] = {name: "Add Partial Ordering", icon: "copy"};
	options.items["rename"] = {name: "Edit", icon: "edit"};
	options.items["delete"] = {name: "Delete", icon: "delete"};
	options.items['sep1'] = "-----";
	
	//console.log($trigger);
	for (i in entities[$trigger.attr('eid')].inLinks){
		options.items[entities[$trigger.attr('eid')].inLinks[i]] = {name: "Delete link to " + entities[entities[$trigger.attr('eid')].inLinks[i]].name, icon: "delete"};			
	}
	for (i in entities[$trigger.attr('eid')].outLinks){
		options.items[entities[$trigger.attr('eid')].outLinks[i]] = {name: "Delete link to " + entities[entities[$trigger.attr('eid')].outLinks[i]].name, icon: "delete"};			
	}
	
	/*
	for(var i=0; i < entities[$trigger.attr('id')].intergroup.length; i++){
		options.items[entities[$trigger.attr('id')].intergroup[i]] = {name: "Delete link to " + entities[entities[$trigger.attr('id')].intergroup[i]].name, icon: "delete"};
	}
	*/

    return options;
  }
});

// delete a link given an from and to entity id.
function delete_link(from,to){
	// add link to database
	var newURL = '../../Links/delete.json';// + problem_map['ProblemMap']['id'] + '.json';
	//console.log("delete link called");
	$.post(newURL, { from_entity_id: from, to_entity_id: to }, function(data){
		//console.log("deleting link:");
		//console.log(data);
		if(data['message'] == "Deleted"){
			for(i in entities[from].outLinks){
				if(entities[from].outLinks[i] == to)
					entities[from].outLinks.splice(i,1);
			}
			for(i in entities[to].inLinks){
				if(entities[to].inLinks[i] == from)
					entities[to].inLinks.splice(i,1);
			}
			//entities[from_eid].outLinks.push(to_eid);
			//entities[to_eid].inLinks.push(from_eid);
			redraw_groups();
		}
	} );
}

// get the names of all the entities for the search auto complete.
function getEntityNames(){
	entityNames = new Array();
	for(var e in entities){
		entityNames.push(entities[e].name);
	}
}

/*
getEntityNames();
$("#search-name").autocomplete({source:entityNames});
*/

