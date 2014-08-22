<?php $this->Html->script('jquery-sortable.min', false); ?>
<?php $this->Html->script('bootstrap-contextmenu', false); ?>
<?php $this->Html->script('print_objtree', false); ?>
<?php $this->Html->script('jquery-ui-1.8.20.custom.min', false); ?>
<?php $this->Html->css('ui-lightness/jquery-ui-1.8.20.custom', null, array('inline' => false)); ?>

<script src="http://d3js.org/d3.v3.min.js"></script>
<style>
	.node circle {
	  fill: #fff;
	  stroke: steelblue;
	  stroke-width: 2px;
	}
	.node text { 
		font: 12px sans-serif; 
	}
	.link {
	  fill: none;
	  stroke: #ccc;
	  stroke-width: 2px;
	}
</style>
<textarea id='objtree_data' style='display:none;'>
	<?php echo $treedata;?>
</textarea>
<div id="objtree_graph">
	
</div>

