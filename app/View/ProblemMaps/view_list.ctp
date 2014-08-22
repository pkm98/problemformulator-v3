<?php $this->Html->css('view_list', null, array('inline' => false)); ?>
<?php $this->Html->script('underscore.min', false); ?>
<?php $this->Html->script('backbone.min', false); ?>
<?php $this->Html->script('backbone-relational', false); ?>
<?php $this->Html->script('jquery-sortable.min', false); ?>
<?php $this->Html->script('bootstrap-contextmenu', false); ?>
<?php $this->Html->script('view_list', false); ?>
<?php $this->Html->script('tutorial_prompts', false); ?>

<script type="text/template" id="entity-template">
<% if (num_decomps > 0 && Entity.current_decomposition == null) { %>
    <i class="icon icon-folder-close pull-left"></i>
<% } else if (num_decomps > 0) { %>
    <i class="icon icon-folder-open pull-left"></i>
<% } else { %>
    <i class="icon icon-file pull-left"></i>
<% } %>
<% if (num_decomps > 1) { %>
    <span class='sup pull-left'><%= num_decomps %></span>
<% } else { %>
    <span class='sup pull-left'></span>
<% } %>
<!--<div class='name pull-left editable' contenteditable=false>-->
<div class='name editable' contenteditable=false>
    <%= Entity.name %>
</div>
<a class='destroy pull-right' href="#">X</a>
<!--<a class='pull-right' href="#">stype</a>-->
<!--<div class="clear"></div>-->
</script>


<textarea id="entity-subtypes" style="display: none;">
<?php echo json_encode($subtypes);?>
</textarea>

<script type="text/template" id="entity-tab-template">
<div class="row-fluid">
    <h2><%= title %>
    <a href="#" id="<%= type %>-tooltip"><i class="icon-question-sign"></i></a>
    </h2>
    <!--<a href="#" id="<%= type %>-csv" class="download-csv">(download csv)</a>-->
</div>
<div class="row-fluid">
    <div class="input-append entity-dialog">
        <input id='new-<%= type %>' type='text' class='entity-input' 
        placeholder='New <%= type %>'></input>
        <button type='submit' class='btn-primary entity-input'>
            <i class="icon-plus"></i>
        </button>
    </div>
</div>
<div class="entity-dialog">
	<select id="entity-subtypes" class="<%= type %>-subtypes" type="<%= type %>" style="width: 100%;"></select>
</div>
<!--<hr>-->
<div class="row-fluid">
    <ul id='<%= type %>' class='entity-list'>
    </ul>
</div>
</script>

<div class="row-fluid">
    <div class="span10 offset1 page-header">
        <h1><?php echo $ProblemMap['ProblemMap']['name']; ?>
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
            <small>(<?php echo $this->Html->link("Objective Tree", array(
                'controller' => 'problem_maps',
                'action' => 'view_objtree',
                $ProblemMap['ProblemMap']['id']
            )); ?>)</small>
            <div class="navbar-search pull-right">
				<!-- Tutorial Prompt On/Off -->
				<input id="tutorial_switch" type="checkbox" onclick="tutorial_switch(<?php echo $ProblemMap['ProblemMap']['id'];?>);" <?php if($ProblemMap['ProblemMap']['tutorial_on']) echo 'checked';?>>
                <div class="input-append">
                    <input type="text" class="search-query" placeholder="Search entities">
                    <span class="forsearch"><i class='icon-search'></i></span>
                </div>
            </div>
        </h1>
    </div>
</div>

<div id="prompt_container" <?php if(!$ProblemMap['ProblemMap']['tutorial_on']) echo 'style="display: none";';?>>
	<div id="tutorial_prompt">
	
	</div>
</div>
<div id='tabs' class="row-fluid">
</div>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"
        aria-hidden="true">x</button>
    <h3 id="myModalLabel">Which decomposition?</h3>
  </div>
  <div class="modal-body temp-decomps">
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </div>
</div>

<div id="context-menu">
    <ul class="dropdown-menu" role="menu">
    </ul>
</div>
