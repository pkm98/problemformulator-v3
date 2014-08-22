var i = 0;
var tree, diagonal, svg;
var treeData;

$( document ).ready(function() {
	$(".navbar").remove();
    treeData = JSON.parse($("textarea#objtree_data").val());
	//console.log(treeData);
	$("#objtree_graph").append("<h3>"+treeData['name']+" Objective Tree</h3>");
	//load_reingold_tilford_tree(treeData);
	
	var margin = {top: 40, right: 120, bottom: 20, left: 120},
	width = 960 - margin.right - margin.left,
	height = 500 - margin.top - margin.bottom;
	
	tree = d3.layout.tree()
		.size([height, width]);

	diagonal = d3.svg.diagonal()
		.projection(function(d) { return [d.x, d.y]; });

	svg = d3.select("#objtree_graph").append("svg")
		.attr("width", width + margin.right + margin.left)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	root = treeData;
	  
	objtree_graph(treeData);
});

function objtree_graph(treeData){
	// Compute the new tree layout.
  var nodes = tree.nodes(treeData).reverse(),
	  links = tree.links(nodes);

  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 100; });

  // Declare the nodes…
  var node = svg.selectAll("g.node")
	  .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter the nodes.
  var nodeEnter = node.enter().append("g")
	  .attr("class", "node")
	  .attr("transform", function(d) { 
		  return "translate(" + d.x + "," + d.y + ")"; });

  nodeEnter.append("circle")
	  .attr("r", 4)
	  .style("fill", "#fff");

  nodeEnter.append("text")
	  .attr("y", function(d) { 
		  return d.children || d._children ? -18 : 18; })
	  .attr("dy", ".35em")
	  .style("fill-opacity", 1)
	  .attr("text-anchor", "middle")
	  .each(function (d) {
			if(d.name.indexOf('Decomp')>-1) return '';
			var arr = d.name.split(" ");
			if (arr != undefined) {
				for (i = 0; i < arr.length; i++) {
					d3.select(this).append("tspan")
						.text(arr[i])
						.attr("dy", i ? "1.2em" : 0)
						.attr("x", 0)
						.attr("class", "tspan" + i);
				}
				if(d.weight > 0)
					d3.select(this).append("tspan")
						.text(' ('+d.weight+')')
						.attr("dy", i ? "1.2em" : 0)
						.attr("x", 0)
						.attr("class", "tspan");
			}
		});

  // Declare the links…
  var link = svg.selectAll("path.link")
	  .data(links, function(d) { return d.target.id; });

  // Enter the links.
  link.enter().insert("path", "g")
	  .attr("class", "link")
	  .attr("d", diagonal);
	  
	arrangeLabels();
}

function arrangeLabels() {
  var move = 1;
  while(move > 0) {
    move = 0;
    svg.selectAll("text")
       .each(function() {
         var that = this,
             a = this.getBoundingClientRect();
         svg.selectAll("text")
            .each(function() {
              if(this != that) {
                var b = this.getBoundingClientRect();
                if((Math.abs(a.left - b.left) * 2 < (a.width + b.width)) &&
                   (Math.abs(a.top - b.top) * 2 < (a.height + b.height))) {
                  // overlap, move labels
                  var dx = (Math.max(0, a.right - b.left) +
                           Math.min(0, a.left - b.right)) * 0.01,
                      dy = (Math.max(0, a.bottom - b.top) +
                           Math.min(0, a.top - b.bottom)) * 0.02,
                      tt = d3.transform(d3.select(this).attr("transform")),
                      to = d3.transform(d3.select(that).attr("transform"));
                  move += Math.abs(dx) + Math.abs(dy);
                
                  to.translate = [ to.translate[0] + dx, to.translate[1] + dy ];
                  tt.translate = [ tt.translate[0] - dx, tt.translate[1] - dy ];
                  d3.select(this).attr("transform", "translate(" + tt.translate + ")");
                  d3.select(that).attr("transform", "translate(" + to.translate + ")");
                  a = this.getBoundingClientRect();
                }
              }
            });
       });
  }
  window.print();
}
