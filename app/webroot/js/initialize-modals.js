// needs to be updated with the subdirectories in the URL
var subdirectory = "../..";

$(function() {
	
		// Makes the buttons JS animated buttons and binds the modals
		// to the buttons.
	
		// search stuff
		$( '#clear-search').button();
		
		$( "#search" ).on('click','#clear-search', function(event){
			searchActive = false;
			searchFocus = null;
			stuck = false;
			stuckFocus = null;
			$('#search-name').val('');
			$('#search-name').removeClass('search-highlight');
			//removeSearchHighlighting();
			unhighlight();
		});

		$( "#search" ).on('autocompleteselect','#search-name', function(event){
			$(this).addClass('search-highlight');
			var name = $(this).val();
			var id;
			for(var i in entities){
				if(entities[i].name == name){
					id = i;
					break;
				}
			}
			
			searchActive = true;
			searchFocus = id;
			
			highlight(id,0,true,highlightblue);
			//searchHighlightName($(this).val());
		});
	
		// requirement
		$( "#addRequirement" ).button();
		$( "#addRequirement" ).click(function() { 
			$( "#modal" ).load(subdirectory + '/entities/add_requirement/' + problem_map['ProblemMap']['id']).dialog({
					height: 600,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add Requirement"
			});
		});		
		
		// function
		$( "#addFunction" ).button();
		$( "#addFunction" ).click(function() { 
			$( "#modal" ).load(subdirectory + '/entities/add_function/' + problem_map['ProblemMap']['id']).dialog({
					height: 300,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add Function"
			});
		});
		
		// artifact
		$( "#addArtifact" ).button();
		$( "#addArtifact" ).click(function() { 
			$( "#modal" ).load(subdirectory + '/entities/add_artifact/' + problem_map['ProblemMap']['id']).dialog({
					height: 375,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add Artifact"
			});
		});
		
		// behavior
		$( "#addBehavior" ).button();
		$( "#addBehavior" ).click(function() { 
			$( "#modal" ).load(subdirectory + '/entities/add_behavior/' + problem_map['ProblemMap']['id']).dialog({
					height: 375,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add Behavior"
			});
		});
		
		// issue
		$( "#addIssue" ).button();
		$( "#addIssue" ).click(function() { 
			$( "#modal" ).load(subdirectory + '/entities/add_issue/' + problem_map['ProblemMap']['id']).dialog({
					height: 375,
					width: 600,
					modal: true,
					draggable: false,
					resizable: false,
					title: "Add Issue"
			});
		});
	});