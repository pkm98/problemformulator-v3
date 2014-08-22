/* Global variables.. yuck I know... */
var subdirectory = '';
var EntityGraph;
var paper;
var Entities;
var Links;
var Decompositions;

/* Collapsible Tree Global variables  - Change made by Pradeep */
var tree, diagonal, svg = [];
var inc = 0, duration = 750, root = [];

/* Helper function */
function nonBlockingLoop(fun, iterations){
    fun();
    if (iterations > 0){
        setTimeout(function() { nonBlockingLoop(fun,iterations-1); }, 25); 
    }
    else if(iterations == -1){
        setTimeout(function() { nonBlockingLoop(fun,-1); }, 25); 
    }
}

/* trips whitespace off beginning and end of a string */
function trim (str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

/* The main show */
$(function(){
    var url = window.location.pathname 

    if (window.location.pathname.split('/')[1] != 'problem_maps'){
	subdirectory =  '/' + window.location.pathname.split('/')[1];
    }
    //console.log(subdirectory);
    var pMapId = url.substr(url.lastIndexOf('/') + 1);

	/* Models */
	var Entity = Backbone.Model.extend({
		url: function(){
			if (this.get('id'))
		return subdirectory + '/entities/' + this.get('id') + '.json';
			else
		return subdirectory + '/entities.json';
		},
		parse: function(response) {
			return response.Entity
		},
		toJSON: function() {
			var entity = _.clone(this.attributes);
			return {'Entity': entity};
		}
	});

	var Decomposition = Backbone.Model.extend({
		url: function(){
			if (this.get('id'))
		return subdirectory + '/decompositions/' + this.get('id') + '.json';
			else
		return subdirectory + '/decompositions.json';
		},
		parse: function(response) {
			return response.Decomposition
		},
		toJSON: function() {
			var decomposition = _.clone(this.attributes);
			return {'Decomposition': decomposition};
		}
	});

	var Link = Backbone.Model.extend({
		url: function(){
			if (this.get('id'))
		return subdirectory + '/links/' + this.get('id') + '.json';
			else
		return subdirectory + '/links.json';
		},
		parse: function(response) {
			return response.Link
		},
		toJSON: function() {
			var link = _.clone(this.attributes);
			return {'Link': link};
		}
	});

	/* Collections */ 
	var EntityList = Backbone.Collection.extend({
		model: Entity,
		url: function() {
			return subdirectory + '/entities.json?problem_map_id=' + pMapId;
		},
		comparator: function(entity){
			// sort first by list_order then by id (order of creation)
			return parseInt(entity.get('list_order')) + 0.00001 * parseInt(entity.get('id'));
		}
	});

	var DecompositionList = Backbone.Collection.extend({
		model: Decomposition,
		url: function() {
			return subdirectory + '/decompositions.json?problem_map_id=' + pMapId;
		}
	});

	var LinkList = Backbone.Collection.extend({
		model: Link,
		url: function() {
			return subdirectory + '/links.json?problem_map_id=' + pMapId;
		}
	});

	/* Instantiations of the collections */
	Entities = new EntityList();
	Decompositions = new DecompositionList();
	Links = new LinkList();

	/* Views */
	var EntityView = Backbone.View.extend({
	});

	var LinkView = Backbone.View.extend({
	});

	var DecompositionView = Backbone.View.extend({
	});

	var EntityGraphView = Backbone.View.extend({
	});

	//EntityGraph = new EntityGraphView();

	function getChildrenEntities(entityType, id){
		//console.log("ent");
		var children = [];

		if (id == null){
			//console.log('beep');
			Entities.where({type: entityType, decomposition_id: null}).forEach(function(element){
					var data = {}
					data['name'] = element.get('name');
					data['children'] = getChildrenDecomps(entityType, element.id);
					/*
					if(data['children'].length) {
						var child = data['children'][0];
						data['children'] = child['children'];
					}
					*/
					children.push(data);
			});
		} else {
			Entities.where({type: entityType, decomposition_id: id}).forEach(function(element){
					var data = {}
					//data['name'] = "<div style='width:100px'>" + element.get('name') + "</div>";
					//data['name'] = "this is a test of a long entity\n I'm not sure if the new lines work\n We shall see.";
					data['name'] = element.get('name');
					data['children'] = getChildrenDecomps(entityType, element.id);
					
					//console.log(JSON.stringify(data)+" count:"+data['children'].length);
					/*
					if(data['children'].length) {
						var child = data['children'][0];
						data['children'] = child['children'];
					}
					*/
					
					children.push(data);
			});
		}

		return children;
	}

	function getChildrenDecomps(entityType, id){
		//console.log("dec");
		var children = [];

		Decompositions.where({entity_id: id}).forEach(function(element){
			var data = {}
			
			data['name'] = 'Decomp' + element.get('id');
			data['children'] = getChildrenEntities(entityType, element.get('id'));
			
			// if(data['children'].length == 1) {
				// var child = data['children'][0];
				// data['children'] = child['children'];
			// }
			/*
			if(data['children'].length > 1)
				data['name'] = 'OR';
			else
				data['name'] = 'AND';
			*/
			
			children.push(data);
		});

		return children;
	}

	function collapse(d) {
		if (d.children) {
		  d._children = d.children;
		  d._children.forEach(collapse);
		  d.children = null;
		}
	}

	/* function to build collapsible tree */
	function collapsibleTree(type){

		var data = {};
		data['name'] = type + "s";
		data['children'] = getChildrenEntities(type);

		var width = 350 + Entities.where({type: type}).length * 200;
		var height = Entities.where({type: type}).length * 20;

		tree = d3.layout.tree().size([height, width]);

		diagonal = d3.svg.diagonal().projection(function(d) { return [d.y, d.x]; });

		svg[type] = d3.select("#tabs div[id='"+type+"']").append("svg")
		.attr("type", type)
		.attr("width", width)
		.attr("height", height)
		.append("g")
		.attr("transform", "translate(100,0)");

		root[type] = data;
		root[type].x0 = height / 2;
		root[type].y0 = 0;
		
		update(root[type], type);

		d3.select(self.frameElement).style("height", "800px");
	}

	/* create or update collabsible tree */
	function update(source,type) {
		// Compute height and width of the tree layout
		var width = 350 + Entities.where({type: type}).length * 200;
		var height = Entities.where({type: type}).length * 20;

		tree = tree.size([height, width]);

		// Compute the new tree layout.
		var nodes = tree.nodes(root[type]).reverse(),
		  links = tree.links(nodes);

		// Normalize for fixed-depth.
		nodes.forEach(function(d) { d.y = d.depth * 180; });

		// Update the nodes…
		var node = svg[type].selectAll("g.node")
		  .data(nodes, function(d) { return d.id || (d.id = ++inc); });

		// Enter any new nodes at the parent's previous position.
		var nodeEnter = node.enter().append("g")
		  .attr("class", "node")
		  .attr("transform", function(d) {return "translate(" + source.y0 + "," + source.x0 + ")"; });
		  
		nodeEnter.append("circle")
		  .attr("r", 1e-6)
		  .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; })
		  .on('click', click);

		// nodeEnter.append("foreignObject")
			// .attr({width: 200, height: 20})
			// .append("xhtml:body")
			// .append("xhtml:text")
			// .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
			// .attr("dy", ".35em")
			// .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
			// .attr("contenteditable", true)
			// .html(function(d) { return d.name })
			// .on("keyup", function(d, i){
                    // console.log(d3.select(this).text());
            // }); 
		  
		nodeEnter.append("text")
		  .attr("class", function(d) { if(d.name.search("Decomp") > -1){return "Decomp";} })
		  .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
		  .attr("dy", ".35em")
		  .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
		  .text(function(d) { return d.name; })
		  .style("fill-opacity", 1e-6)
		  .attr("font-weight", "bold");

		// nodeEnter.append("text")
			// .attr("x", 7)
			// .attr("dy", 5)
			// .attr("text-anchor", "start")
			// .attr("font-weight", "bold")
			// .text(function(d) { return d.name; })
			// .style("fill-opacity", 1e-6);

		// Transition nodes to their new position.
		var nodeUpdate = node.transition()
		  .duration(duration)
		  .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

		nodeUpdate.select("circle")
		  .attr("r", 4.5)
		  .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

		nodeUpdate.select("text")
		  .style("fill-opacity", 1);

		// Transition exiting nodes to the parent's new position.
		var nodeExit = node.exit().transition()
		  .duration(duration)
		  .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
		  .remove();

		nodeExit.select("circle")
		  .attr("r", 1e-6);

		nodeExit.select("text")
		  .style("fill-opacity", 1e-6);

		// Update the links…
		var link = svg[type].selectAll("path.link")
		  .data(links, function(d) { return d.target.id; });

		// var link_attr;
		var counter = 0;
		
		// Enter any new links at the parent's previous position.
		link.enter().insert("path", "g")
			.attr("class", "link")
			.attr("d", function(d) {
				var o = {x: source.x0, y: source.y0};
				return diagonal({source: o, target: o});
			}).attr("stroke", "#ccc");

		// Transition links to their new position.
		link.transition()
			.duration(duration)
			.attr("d", diagonal);

		// Transition exiting nodes to the parent's new position.
		link.exit().transition()
			.duration(duration)
			.attr("d", function(d) {
				var o = {x: source.x, y: source.y};
				return diagonal({source: o, target: o});
			})
			.remove();

		// Stash the old positions for transition.
		nodes.forEach(function(d) {
			d.x0 = d.x;
			d.y0 = d.y;
		});
		
		inc = 0;
	}

	// Toggle children on click.
	function click(d) {
	  if (d.children) {
		d._children = d.children;
		d.children = null;
	  } 
	  else {
		d.children = d._children;
		d._children = null;
	  }
	  //console.log(this);
	  var type = $(this).parents('svg').attr('type');
	  //console.log(this.parentNode('svg'));
	  update(d, type);
	  setTimeout(function(){decomp_pathstroke();},800);
	  
	}

	// Old view - Commented out by Pradeep
	/*function load_reingold_tilford_tree(type){
		var data = {};
		data['name'] = type + "s";
		data['children'] = getChildrenEntities(type);
		
		var json_data = JSON.stringify(data);
		console.log(json_data);

		// d3 business.
		var width = 350 + Entities.where({type: type}).length * 200;
		var height = Entities.where({type: type}).length * 20;
		
		var tree = d3.layout.tree()
			.size([height, width - 200]);

		var diagonal = d3.svg.diagonal()
			.projection(function(d) { return [d.y, d.x]; });

		var svg = d3.select("#tabs").append("svg")
			.attr("width", width)
			.attr("height", height)
		  .append("g")
			.attr("transform", "translate(100,0)");


		// load the data
		var nodes = tree.nodes(data),
		  links = tree.links(nodes);
		
		var link = svg.selectAll("path.link")
		  .data(links)
		.enter().append("path")
		  .attr("class", "link")
		  .attr("d", diagonal);

		var node = svg.selectAll("g.node")
		  .data(nodes)
		.enter().append("g")
		  .attr("class", "node")
		  .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })

		node.append("circle")
		  .attr("r", 4.5);

		node.append("text")
		  .attr("dx", function(d) { return d.children ? -8 : 8; })
		  .attr("dy", 3)
		  .attr("text-anchor", function(d) { return d.children ? "end" : "start"; })
		  .text(function(d) { return d.name; });

		var	test = svg.selectAll("g.node.text")
		.data(nodes)
		.style("width", '100px');
	}
	*/

	/* Get data from server */
	Links.fetch().done(function() {
		Entities.fetch().done(function(){
			Decompositions.fetch().done(function(){
			
			// Old Graph View - Change made by Pradeep
			/*load_reingold_tilford_tree('requirement'); 
			$('#tabs').append("<h4>Functions</h4>");
			load_reingold_tilford_tree('function');     
			$('#tabs').append("<h4>Artifacts</h4>");
			load_reingold_tilford_tree('artifact'); 
			$('#tabs').append("<h4>Behaviors</h4>");
			load_reingold_tilford_tree('behavior');    
			$('#tabs').append("<h4>Issues</h4>");
			load_reingold_tilford_tree('issue'); */
			
			// Collabsible Tree View - Change made by Pradeep
			$('#tabs').append("<div id='requirement' class='headline'><div class='headline-in'><h4>Requirements</h4> 	\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('requirement');
            
			$('#tabs').append("<div id='usescenario' class='headline'><div class='headline-in'><h4>Use Scenarios</h4> 	\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('usescenario');            
			
			$('#tabs').append("<div id='function' class='headline'><div class='headline-in'><h4>Functions</h4> 			\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('function');            
			
			$('#tabs').append("<div id='artifact' class='headline'><div class='headline-in'><h4>Artifacts</h4> 			\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('artifact');            
			
			$('#tabs').append("<div id='behavior' class='headline'><div class='headline-in'><h4>Behaviors</h4> 			\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('behavior');            
			
			$('#tabs').append("<div id='issue' class='headline'><div class='headline-in'><h4>Issues</h4> 				\
								<a href='javascript:void(0);' id='toggle' onclick='toggleBlind(this);'>Click</a></div></div>");
			collapsibleTree('issue');

			setTimeout(function(){decomp_pathstroke();},800);
			
				/*
				// use the stuff here?
				var space = Entities.length * 50;

				$('#tabs').append("<canvas id='canvas' width='" + (space) + "' height='" + (space) + "'></canvas");
				var graph = new Springy.Graph();
				Entities.each(function(entity){
					var node = new Springy.Node("e"+entity.get('id'), 
												{label: entity.get('name'),
												 type: entity.get('type')});
					graph.addNode(node);
				});
				Links.each(function(link){
					graph.addEdges(["e" + link.get('from_entity_id'), "e" + link.get('to_entity_id'), {label: link.get('type')}]);
				});

				Decompositions.each(function(decomposition){
					var node = new Springy.Node("d" + decomposition.get('id'),
												{label: "  ",
												 type: "decomposition"});
					graph.addNode(node);
					graph.addEdges(["e" + decomposition.get('entity_id'),
								   "d" + decomposition.get('id'), {label: 'has child'}]);


					_.each(Entities.where({'decomposition_id': decomposition.get('id')}),
					function(entity){
						graph.addEdges(['d' + decomposition.get('id'), 
										'e' + entity.get('id'),
										{label: 'has child'}]);
					});

				});

				var springy = $('#canvas').springy({
					graph: graph
				});
				*/
			});  
		});
	});

	function updateSearchIcon(e){
		if($(e.target).val().length > 0){
			$(e.target).parent().find('i')
				.removeClass('icon-search')
				.addClass('icon-remove-sign')
				.unbind()
				.on('click', function(e){
					$(e.target).parent().parent().find('input').val('');
					$(e.target).parent().parent().find('input').change();
					$('.active').removeClass('active');
				});
		}
		else{
			$(e.target).parent().find('i')
				.unbind()
				.removeClass('icon-remove-sign')
				.addClass('icon-search');
		}

		var entity = Entities.findWhere({name: $(e.target).val()});

		if (entity){
			id = entity.get('id');
			selector = $('li[entity-id="' + id + '"]');
			while (selector.length == 0){
				var entity = Entities.findWhere({id: id});
				if (entity.get('decomposition_id') == null){
					 break;
				}
				var decomp = Decompositions.findWhere({id: entity.get('decomposition_id')})
				if (!decomp){
					break;
				}
				id = decomp.get('entity_id');
				selector = $('li[entity-id="' + id + '"]');
			}
			$('.active').removeClass('active');
			selector.addClass('active');
		}

	}

	$('.search-query').on('keyup', function(e){
		updateSearchIcon(e);
	});
	$('.search-query').on('change', function(e){
		updateSearchIcon(e);
	});

	$('.search-query').typeahead({
		source: function(query, process){
			return Entities.pluck('name');
		}
	});

});

function toggleBlind(a) {
	$(a).parents('.headline').children('svg').toggle('blind');
	$(a).toggleClass("plus");
}

function decomp_pathstroke(){

	var str1, str2, sp1, sp2, len;
	
	$("text.Decomp").each(function(){ 
		str2 = $(this).parent().attr('transform').replace('translate(','').split(',');
		$(this).hide();
		$("path").each(function(){
			str1 = $(this).attr("d");
			sp1 = str1.split(" ");
			len = sp1.length;
			sp2 = sp1[len-1].split(",");
			if(str2[0] == sp2[0])
				$(this).attr("stroke-dasharray", 10).attr("stroke","#FA8072");
		});
	});
}
