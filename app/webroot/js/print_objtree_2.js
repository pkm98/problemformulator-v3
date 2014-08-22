$( document ).ready(function() {
	$(".navbar").remove();
    treeData = JSON.parse($("textarea#objtree_data").val());
	//console.log(treeData);
	$("#objtree_graph").append("<h3>"+treeData['name']+" Objective Tree</h3>");
	//load_reingold_tilford_tree(treeData);
	
	objtree_graph(treeData);
});

function objtree_graph(data){
		
		// d3 business.
		var width = 1000;
		var height = 200;
		
		var tree = d3.layout.tree()
			.size([height, width - 200]);

		var diagonal = d3.svg.diagonal()
			.projection(function(d) { return [d.y, d.x]; });

		var svg = d3.select("#objtree_graph").append("svg")
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
	  
}