<?php $this->Html->css('view_graphNew', null, array('inline' => false)); ?>
<?php $this->Html->script('underscore.min', false); ?>
<?php $this->Html->script('backbone.min', false); ?>
<?php $this->Html->script('backbone-relational', false); ?>
<?php $this->Html->script('jquery-sortable.min', false); ?>
<?php $this->Html->script('bootstrap-contextmenu', false); ?>
<?php $this->Html->script('http://d3js.org/d3.v3.min.js', false); ?>
<style>

.node {
  font: 300 14px "Helvetica Neue", Helvetica, Arial, sans-serif;
  fill: #bbb;
}

.node:hover {
  fill: #000;
}

.link {
  stroke: steelblue;
  stroke-opacity: .4;
  fill: none;
  pointer-events: none;
}

.thelink {
  stroke: steelblue;
  stroke-opacity: .4;
  fill: none;
  pointer-events: none;
}

.node:hover,
.node--source,
.node--target {
  font-weight: 700;
}

.node--source {
  fill: #2ca02c;
}

.node--target {
  fill: #d62728;
}

.link--source,
.link--target {
  stroke-opacity: 1;
  stroke-width: 2px;
}

.link--source {
  stroke: #d62728;
}

.link--target {
  stroke: #d62728;
}

.thelink--source,
.thelink--target {
  stroke-opacity: 1;
  stroke-width: 2px;
}

.thelink--source {
  stroke: #CDCD00;
}

.thelink--target {
  stroke: #CDCD00;
}

.page-header {
  margin-bottom: 2px;
  margin-top: 2px;
}

</style>
<body>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

var diameter = 950,
    radius = diameter / 2,
    innerRadius = radius - 120;

var cluster = d3.layout.cluster()
    .size([360, innerRadius])
    .sort(null)
    .value(function(d) { return d.size; });

var bundle = d3.layout.bundle();

var line = d3.svg.line.radial()
    .interpolate("bundle")
    .tension(.85)
    .radius(function(d) { return d.y; })
    .angle(function(d) { return d.x / 180 * Math.PI; });

var svg = d3.select("body").append("svg")
	.attr("height", diameter)
	.attr("width", diameter)
    .attr("style", "margin-left: 8%;")
  	.append("g")
    .attr("transform", "translate(" + radius + "," + radius + ")");

var link = svg.append("g").selectAll(".link"),
    node = svg.append("g").selectAll(".node"),
    thelink = svg.append("g").selectAll(".thelink");

d3.json("../../problemMapStructure.json", function(error, classes) {
  var nodes = cluster.nodes(packageHierarchy(classes)),
      links = packageImports(nodes),
      thelinks = packageImports1(nodes);
  
  link = link
      .data(bundle(links))
      .enter().append("path")
      .each(function(d) { d.source = d[0], d.target = d[d.length - 1]; })
      .attr("class", "link")
      .attr("d", line);

  thelink = thelink
      .data(bundle(thelinks))
      .enter().append("path")
      .each(function(d) { d.source = d[0], d.target = d[d.length - 1]; })
      .attr("class", "thelink")
      .attr("d", line);	
      
  node = node
      .data(nodes.filter(function(n) { return !n.children; }))
      .enter().append("text")
	  .style("fill", function(d) { return setTextColor(d);})
      .attr("class", "node")
      .attr("dx", function(d) { return d.x < 180 ? 8 : -8; })
      .attr("dy", ".31em")
      .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + d.y + ")" + (d.x < 180 ? "" : "rotate(180)"); })
      .style("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
      .text(function(d) { return d.key })
      .on("mouseover", mouseovered)
      .on("mouseout", mouseouted);
	  
});

// Set text color based on category
function setTextColor(d) {
	if (d["type"]== "requirement") return "#FF0000";
	else if (d["type"]== "userscenario") return "#B80FEE";
	else if (d["type"]== "function") return "#0000FF";
	else if (d["type"]== "artifact") return "#39D339";
	else if (d["type"]== "behavior") return "#FF8500";
	else if (d["type"]== "issue") return "#24D4E4";
}

function mouseovered(d) {
  node
      .each(function(n) { n.target = n.source = false; });

  link
      .classed("link--target", function(l) { if (l.target === d) return l.source.source = true; })
      .classed("link--source", function(l) { if (l.source === d) return l.target.target = true; })
      .filter(function(l) { return l.target === d || l.source === d; })
      .each(function() { this.parentNode.appendChild(this); });
      
  thelink
      .classed("thelink--target", function(l) { if (l.target === d) return l.source.source = true; })
      .classed("thelink--source", function(l) { if (l.source === d) return l.target.target = true; })
      .filter(function(l) { return l.target === d || l.source === d; })
      .each(function() { this.parentNode.appendChild(this); });

  node
      .classed("node--target", function(n) { return n.target; })
      .classed("node--source", function(n) { return n.source; });
}

function mouseouted(d) {
  link
      .classed("link--target", false)
      .classed("link--source", false);
  
  thelink
      .classed("thelink--target", false)
      .classed("thelink--source", false);

  node
      .classed("node--target", false)
      .classed("node--source", false);
}

d3.select(self.frameElement).style("height", diameter + "px");

// Lazily construct the package hierarchy from class names.
function packageHierarchy(classes) {
  var map = {};

  function find(name, data) {
    var node = map[name], i;
    if (!node) {
      node = map[name] = data || {name: name, children: []};
      if (name.length) {
        node.parent = find(name.substring(0, i = name.lastIndexOf(".")));
        node.parent.children.push(node);
        node.key = name.substring(i + 1);
      }
    }
    return node;
  }

  classes.forEach(function(d) {
    find(d.name, d);
  });
  //alert(map);
  return map[""];
}

// Return a list of imports for the given array of nodes.
function packageImports1(nodes) {
  var map = {},
      thelinks = [];

  // Compute a map from name to node.
  nodes.forEach(function(d) {
    map[d.name] = d;
  });

  // For each import, construct a link from the source to target node.
  nodes.forEach(function(d) {
    if (d.thelinks) d.thelinks.forEach(function(i) {
      thelinks.push({source: map[d.name], target: map[i]});
    });
  });

  return thelinks;
}

function packageImports(nodes) {
  var map = {},
      children1 = [];

  // Compute a map from name to node.
  nodes.forEach(function(d) {
    map[d.name] = d;
  });

  // For each import, construct a link from the source to target node.
  nodes.forEach(function(d) {
    if (d.children1) d.children1.forEach(function(i) {
      children1.push({source: map[d.name], target: map[i]});
    });
  });

  return children1;
}

</script>

<div class="row-fluid">
    <div class="span10 offset1 page-header">
        <h1><?php echo $ProblemMap['ProblemMap']['name']; ?>
            <small>(<?php echo $this->Html->link("List view", array(
                'controller' => 'problem_maps',
                'action' => 'view_list',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
			<small>(<?php echo $this->Html->link("Tree view", array(
                'controller' => 'problem_maps',
                'action' => 'view_graph',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
			<small>(<?php echo $this->Html->link("Objective Tree", array(
                'controller' => 'problem_maps',
                'action' => 'view_objtree',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
		</h1>
		<div class="color-code">
			<small>
				<u>Entities</u>: &nbsp;
				<font><span style="display: inline-block; padding: 5px; background: #FF0000; margin-right: 5px;"></span>Requirements</font> | 
				<font><span style="display: inline-block; padding: 5px; background: #B80FEE; margin-right: 5px;"></span>User Scenarios</font> | 
				<font><span style="display: inline-block; padding: 5px; background: #0000FF; margin-right: 5px;"></span>Functions</font> | 
				<font><span style="display: inline-block; padding: 5px; background: #39D339; margin-right: 5px;"></span>Artifacts</font> | 
				<font><span style="display: inline-block; padding: 5px; background: #FF8500; margin-right: 5px;"></span>Behaviors</font> | 
				<font><span style="display: inline-block; padding: 5px; background: #24D4E4; margin-right: 5px;"></span>Issues</font>
			</small>
			<span style="display: inline-block; width: 25px;"></span>
			<small>
				<u>Relationship</u>: &nbsp;
            	<font><span style="display: inline-block; padding: 2px 25px; margin-right: 5px;border-top: 2px solid #FF0000;"></span> Parent-Child </font> | 
            	<font><span style="display: inline-block; padding: 2px 25px; margin-right: 5px;border-top: 2px solid #CDCD00;"></span> Inter-Group</font>
            </small>
		</div>
    </div>
</div>
