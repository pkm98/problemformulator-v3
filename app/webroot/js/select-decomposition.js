// global variables

// get the entity id you are displaying possible decompositions for
var eid = $('#choices').attr('eid');

var n = 0;

// for each of the decompositions draw a div with the decomp
for(var i in decompositions){
	if(parseInt(decompositions[i]['entity_id']) == parseInt(eid)){
		
		$('#choices').append('<div data-did="' + decompositions[i]['id'] + '" data-eid="' + eid + '" class="ui-widget-content decomp-widget"><div class="ui-widget-header">Decomposition ' + n + '</div><ul></ul></div>');
		//console.log(entities);
		
		$.each(entities,function(index,element){
			
			if(element.parent_decomposition == decompositions[i]['id'])
				$('div[data-did="' + decompositions[i]['id'] + '"] ul').append('<li class="ui-widget-content decomp-entity" data-eid="' + element.id + '">' + element.name + '</li>');
			
		});
		
		n += 1;
	}
}

// highlight the decomposition.
highlight_decomp();

// highlights the appropriate entities if highlighting is activated.
function highlight_decomp(){
	
	if(stuck){
		
		eid = stuckFocus;
		
		hEntities = Array();
		
		// TODO put in thing here to determine depth
		for(i in entities[eid].outLinks){
			var leid = entities[eid].outLinks[i];
			hEntities.push(leid);
			while(entities[leid].parent_decomposition != null && entities[leid].parent_decomposition != "null"){
				var leid = decompositions[entities[leid].parent_decomposition]['entity_id'];
				hEntities.push(leid);
			}
		}
		
		for(i in entities[eid].inLinks){
			var leid = entities[eid].inLinks[i];
			hEntities.push(leid);
			while(entities[leid].parent_decomposition != null && entities[leid].parent_decomposition != "null"){
				var leid = decompositions[entities[leid].parent_decomposition]['entity_id'];
				hEntities.push(leid);
			}
		}
	
		names = {};
		
		for( i in entities ){
			names[entities[i].name] = i;
		}
	
		$('.decomp-entity').each(function(index){
			
			name = $(this).html();
			
			for(i in hEntities){
				//console.log(hEntities[i]);
				//console.log(names[name]);
				if(names[name] == hEntities[i]){
					$('li[data-eid="' + names[name] + '"]').addClass('stuck-highlight');
				}
			}			
		});
	}
	
	if(searchActive){
		
		eid = searchFocus;
		
		hEntities = Array();
		hEntities.push(eid);
		
		// TODO put in thing here to determine depth
		var leid = eid;
		while(entities[leid].parent_decomposition != null && entities[leid].parent_decomposition != "null"){
			var leid = decompositions[entities[leid].parent_decomposition]['entity_id'];
			hEntities.push(leid);
		}
		
		names = {};
		
		for( i in entities ){
			names[entities[i].name] = i;
		}
	
		$('.decomp-entity').each(function(index){
			
			name = $(this).html();
			
			for(i in hEntities){
				//console.log(hEntities[i]);
				//console.log(names[name]);
				if(names[name] == hEntities[i]){
					$('li[data-eid="' + names[name] + '"]').addClass('search-highlight');
				}
			}			
		});
	}
	
}

// make this a javascript animated button
$(".decomp-widget").button();

// bind the action to the button
$(".decomp-widget").on("click", function(e){
	var eid = $(this).attr('data-eid');
	var did = $(this).attr('data-did');
	entities[eid].setCurrentDecomposition(did);
	$('#modal').dialog("close");
});