<?php $this->Html->script('jquery-sortable.min', false); ?>
<?php $this->Html->script('bootstrap-contextmenu', false); ?>
<?php $this->Html->script('view_objtree', false); ?>
<?php $this->Html->script('jquery-ui-1.8.20.custom.min', false); ?>
<?php $this->Html->css('ui-lightness/jquery-ui-1.8.20.custom', null, array('inline' => false)); ?>

<script src="http://d3js.org/d3.v3.min.js"></script>
<style>

.node circle {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 3px;
}

.node text { font: 12px sans-serif; }

.link {
  fill: none;
  stroke: #ccc;
  stroke-width: 2px;
}

</style>
<div class="row-fluid">
    <div class="span10 offset1 page-header">
        <h1>Objective Tree
            <small>(<?php echo $this->Html->link("List View", array(
                'controller' => 'problem_maps',
                'action' => 'view_list',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
			<small>(<?php echo $this->Html->link("Tree View", array(
                'controller' => 'problem_maps',
                'action' => 'view_graph',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
            <small>(<?php echo $this->Html->link("Network View", array(
                'controller' => 'problem_maps',
                'action' => 'view_graphNew',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
		</h1>
    </div>
</div>

<div id="edit_weight">Edit Objective Tree Weight</div>
<div id="print_objtree">Print</div>
<div id="calculate_weight">Calculate Weight</div>
<div id="submit_weight">Submit</div>
<div id="cancel_edit">Cancel</div>
<div id="objtree_form">
	<?php echo $objtree_html; ?>
</div>
<!--
<div id="objtree_graph">
	
</div>
-->
