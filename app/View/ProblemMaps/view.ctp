<?php

// Jquery
//echo $this->Html->script('jquery-1.7.2.min',FALSE);

// Jquery UI for the modal dialogs

echo $this->Html->script('jquery-ui-1.8.20.custom.min', FALSE);

// Jquery context menu
echo $this->Html->script('jquery.contextMenu', FALSE);

// Raphael JS library
echo $this->Html->script('raphael-min', FALSE);

// Raphael JS zoom-pan plugin
echo $this->Html->script('raphael-zpd', FALSE);

// Custom file to produce problem map rendering (this is where the action is).
echo $this->Html->script('raphael-interface', FALSE);

// Custom file with Entities class
echo $this->Html->script('entity', FALSE);

// Custom file with Groups class
echo $this->Html->script('group', FALSE);

// Custom file to load the modal dialog windows (i.e. add entity buttons).
echo $this->Html->script('initialize-modals', FALSE);

// Load the Jquery UI theme file
$this->Html->css('ui-lightness/jquery-ui-1.8.20.custom', null, array(
    'inline' => false
));

// load the CSS for this page
$this->Html->css('view-pmap', null, array(
    'inline' => false
));

// load the CSS for the context menu
$this->Html->css('jquery.contextMenu', null, array(
    'inline' => false
));
?>

<div id="top">
	<h1>Problem Formulator</h1>
	<div id="search" title="Search">
		<label for="search-name">Search:</label>
		<input type="text" name="search-name" id="search-name" class="text ui-widget-content ui-corner-all"/>
		<div id="clear-search">Clear</div>
	</div>
</div>

<!-- A modal window for popup forms -->
<div id="modal" style="display: none;"></div>

<!-- Toolbar for adding entities -->
<div id="toolbar">
	<div id="addRequirement">Add Requirement</div>
	<div id="addFunction">Add Function</div>
	<div id="addArtifact">Add Artifact</div>
	<div id="addBehavior">Add Behavior</div>
	<div id="addIssue">Add Issue</div>
</div>

<!-- Container for RaphaelJs rendering -->
<div id="canvas_container"></div>
