// function to create an entity
function Entity(entity) {
	// variable of the entity object
	this.id = parseInt(entity.id);
	this.eid = this.id;
	this.parent_decomposition = entity.decomposition_id;
	this.current_decomposition = entity.current_decomposition;
	this.name = entity.name;
	this.type = entity.type;
	this.x = parseInt(entity.x);
	this.y = parseInt(entity.y);
	this.group = null;
	this.rjsElement = null;
	this.lastElementOver = null;
	this.outLinks = [];
	this.inLinks = [];
	this.color = null;
}

// function to draw the entity
Entity.prototype.draw = function(paper, xoffset, yoffset, width, parent){
	
	// these are useful if you're not automatically arranging the entities (which we are).
	if(xoffset == null)
		xoffset = this.x;
	if(yoffset == null)
		yoffset = this.y;
	
	// if you have no parent (top level) and your parent decomposition is non null then don't draw the entity here.
	// also if your parent decomposition is not the same as the parent then don't draw the entity here.
	if (typeof parent == 'undefined'){
		if (this.parent_decomposition != null && this.parent_decomposition != "null"){
			return;
		}
	}
	else if (this.parent_decomposition != parent){
		return;
	}

	
	// create a group for dragging this object around.
	this.group = paper.set();
	//this.name.length
	
	this.color = "hsb(0, 1, 1)";
	
	//console.log(this.type);
	/*
	if(this.type == "requirement")
		// red
		this.color = "#FF0000";
	else if(this.type == "function")
		// green
		this.color = "#006600";
	else if(this.type == "artifact")
		// orange
		this.color = "#FF9933";
	else if(this.type == "behavior")
		// blue
		this.color = "#3366CC";
	else if(this.type == "issue")
		// purple
		this.color = "#663399";
	*/
	var invalid = false;
	for(i in invalids){
		if(invalids[i] == this.eid){
			invalid = true;
			break;
		}
	}
	if(invalid){
		// switch these if you want to shut off the highlighting of invalid entities.
		this.color = highlightred;
		//this.color = "#efefef";	
	}
	else{	
		// grey
		this.color = "#efefef";
	}
	
	var r = paper.rect(xoffset, yoffset, width, 30,0).attr({fill: this.color, stroke: "black", opacity: 1});
	
	
	
	// add the id and class to the object for event handling
	$(r.node).attr('eid', this.id).attr('class', "entity context-menu-one");

	this.rjsElement = r;
	
	var t = paper.text(0, 0, "");
	
	// add the id and class to the object for event handling
	$(t.node).attr('eid', this.id).attr('class', "entity");
	//var t = paper.text(0, 0, this.name);
	
	// add the + or - based on whether or not it has decompositions
	/*
	if(this.current_decomposition == null || this.current_decomposition == "null"){
		var found = false;
		for(i in decompositions){
			if(parseInt(decompositions[i]['entity_id']) == this.id){
				name = "+ : " + this.name;
				found = true;
				break;
			}
		}
		if(!found)
			name = this.name;
	}
	else
		name = "- : " + this.name;
	*/
	
	var count = 0;
	for(i in decompositions){
		if(parseInt(decompositions[i]['entity_id']) == this.id){
			// add plus
			count += 1;
		}
	}
	
	if(this.current_decomposition == null || this.current_decomposition == "null"){
		if(count > 0){
			this.s = paper.image("../../img/plus.gif", xoffset + 3, yoffset + 5, 15, 15);
		}
		if(count > 1){
			this.c = paper.text(xoffset+18, yoffset + 6, "" + count);
			this.c.attr({'font-size': 8});
		}
	}
	else{
		if(count > 0)
			this.s = paper.image("../../img/minus.gif", xoffset + 3, yoffset + 5, 15, 15);
		if(count > 1){
			this.c = paper.text(xoffset+18, yoffset + 6, "" + count);
			this.c.attr({'font-size': 8});
		}
	}
	
	name = this.name;
	
	// programatic word wrap
	var words = name.split(" ");
	var tempText = "";
	for (var i=0; i<words.length; i++) {
	  t.attr("text", tempText + " " + words[i]);
	  if (t.getBBox().width > width - 10) {
	    tempText += "\n" + words[i];
	  } else {
	    tempText += " " + words[i];
	  }
	}
	t.attr("text", tempText.substring(1));
	
	// This does left text align (if you want it).
	//t.attr({'text-anchor': 'start'})
	
	var tbb = t.getBBox();
	r.attr({height: 5 + tbb.height + 5});
	var rbb = r.getBBox();
	
	//t.attr({x: rbb.x + 10, y: rbb.y + (tbb.height / 2) + 5}); // for left align
	t.attr({x: rbb.x + rbb.width / 2, y: rbb.y + (tbb.height / 2) + 5}); // for centered
	
	r.group = this.group;
	t.group = this.group;
	this.text = t;
	
	r.eid = this.id;
	t.eid = this.id;
	
	this.group.push(r);
	if(this.s)
		this.group.push(this.s);
	if(this.c)
		this.group.push(this.c);
	this.group.push(t);

	this.group.drag(this.move, this.start, this.up);
	this.group.onDragOver(function(data){
		if (this.group[0].eid != data.eid){
			// used to determine if an entity is on another entity at the end of the drag.
			this.lastElementOver = data.eid;
		}
	});
	
	var entity_offset = 0;
	
	if(this.current_decomposition != null){
		
		for(i in entities){
			if(entities[i].parent_decomposition != null && entities[i].parent_decomposition != "null" &&
			   entities[i].parent_decomposition == this.current_decomposition){

				var entity_group = entities[i].draw(paper, xoffset + 10, yoffset + tbb.height + 10 + entity_offset, width - 20, this.current_decomposition);

				entity_offset += entity_group.getBBox().height;
				this.group.push(entity_group);
			}
		}
	}
	
	r.attr({height: 5 + tbb.height + 5 + entity_offset + 5});
	//rbb = r.getBBox();
	
	var bb = this.group.getBBox();
	this.group.originalX = bb.x;
	this.group.originalY = bb.y;
	
	// click handlers for entities
	// click
	//console.log("drawing");
	$( "svg" ).off('mouseup', 'rect.entity');
	$( "svg" ).on('mouseup', 'rect.entity', mouseUp);
	$( "svg" ).off('mouseenter', 'rect.entity');
	$( "svg" ).on('mouseenter', 'rect.entity', mouseEnter);
	$( "svg" ).off('mouseleave', 'rect.entity');
	$( "svg" ).on('mouseleave', 'rect.entity', mouseExit);
	//$( "svg" ).off('mouseRightC', 'rect.entity');
	//$( "svg" ).on('mouseup', 'rect.entity', mouseRightClick);
	
	// mouseup action
	function mouseUp(event){
		//console.log("click");

		clicks += 1;
		if(clicks == 1){
			var e = this;
			//console.log(e);
			window.setTimeout(function(){
				if (clicks == 1){
					mouseSingleClick(e,event);
					clicks = 0;
				}
			}, 300);
		}
		else{
			//console.log("double click");
			//doubleClick(this,event);
			mouseDblClick(this,event);
			clicks = 0;
		}
	}
	
	// mouse enter action
	function mouseEnter(event){
		if(stuck || searchActive)
			return;
		
		var id = $(this).attr('eid');
		
		highlight(id,0,false, highlightyellow);
		
		for(i in entities[id].outLinks){
			highlight(entities[id].outLinks[i],0,true, highlightyellow);
		}
		for(i in entities[id].inLinks){
			highlight(entities[id].inLinks[i],0,true, highlightyellow);
		}
	}
	
	// mouse exit action
	function mouseExit(event){
		if(stuck || searchActive)
			return;

		unhighlight();
	}
	
	// mouse double click action
	function mouseDblClick(e,event){
		var id = $(e).attr('eid');
		
		if(entities[id].current_decomposition == null || entities[id].current_decomposition == "null"){	

			var found = false;
			for(i in decompositions){
				if(parseInt(decompositions[i]['entity_id']) == entities[id].id){
					found = true;
					break;
				}
			}
			if(found){
				$( "#modal" ).load(subdirectory + '/entities/view/' + id).dialog({
						height: 400,
						width: 600,
						modal: true,
						draggable: false,
						resizable: false,
						title: "Select Decomposition"
				});
			}
			
		}
		else{
			// remove current decomposition and redraw
			entities[id].setCurrentDecomposition("null");
		}
	}
	
	// mouse single click action
	function mouseSingleClick(e,event){
		//console.log("Single");
		if(dragging == false){
			//console.log("sticky");
			stuck = !stuck;
			if(stuck){
				stuckFocus = $(e).attr('eid');
			}
		}
		dragging = false;
	}

	return this.group;
}

// unhighlight all entities
function unhighlight(){
	for (i in entities){
		if($('rect[eid="' + entities[i].eid + '"]').length > 0)
			$('rect[eid="' + entities[i].eid + '"]').attr('fill', entities[i].color);
	}
}

// highlight a specific entity, depth, recursive, and color determine how far from
// the original entity the current entity is, if you should highlight the parents,
// what color to highlight respectively. 
function highlight(eid, depth, recursive, color){
	if(!color)
		color = $('rect[eid="' + eid + '"]').attr('fill');
	
	var percent = depth * (Math.pow(0.6,depth));
	if(percent < 0.05)
		percent = 0.05;
	if($('rect[eid="' + eid + '"]').length > 0)
		$('rect[eid="' + eid + '"]').attr('fill', MoveColor(color, entities[eid].color, percent));
		//$('rect[eid="' + eid + '"]').attr('fill', LightenDarkenColor(color, amt));

	if(recursive){
		if(entities[eid].parent_decomposition != null && entities[eid].parent_decomposition != "null"){
			var pid = decompositions[entities[eid].parent_decomposition]['entity_id'];
			highlight(pid,depth+1,true, color);
		}
	}
}

// action to start an entity drag
Entity.prototype.start = function(){
	// bring the object in front of all others
	this.group.toFront();
	
	// if in a parent decomp then set last element over to the parent entity
	//if(this.parent_decomposition != null && this.parent_decompositon != "null")
	this.lastElementOver = -1;
	
	// save its initial position
	this.group.oBB = this.group.getBBox();
	
	// make it semi transparent
    this.group.animate({opacity: .25}, 500, ">");
}

// action to take place at each step of the entity drag
Entity.prototype.move = function(dx,dy){
	
	dragging = true;
	
	// move the entity a little bit.
	var bb = this.group.getBBox();
	this.group.translate(this.group.oBB.x - bb.x + dx, this.group.oBB.y - bb.y + dy);
	
	// update the links
	for (var i = links.length; i--;) {
		paper.connection(links[i]);
	}
	paper.safari();
}

// action to create a link
createLink = function(from_eid, to_eid, type){
	// create link on paper
	//links.push(paper.connection(entities[from_eid].rjsElement, entities[to_eid].rjsElement, "#fff"));
	
	// if the link exists exit
	var found = false;
	for(i in entities[from_eid].outLinks){
		if(entities[from_eid].outLinks[i] == to_eid){
			found = true;
			break;
		}
	}
	
	if(found)
		return;
	
	// add link to database
	var newURL = '../../Links/add.json';// + problem_map['ProblemMap']['id'] + '.json';

	$.post(newURL, { from_entity_id: from_eid, to_entity_id: to_eid, type: type, problem_map_id: problem_map['ProblemMap']['id'] }, function(data){
		//console.log("saving link:");
		//console.log(data);
		if(data['message'] == "Saved"){
			entities[from_eid].outLinks.push(to_eid);
			entities[to_eid].inLinks.push(from_eid);
			redraw_groups();
		}
	} );
	
}

// action to create a decomposition
createDecompositon = function(parent, child){
	// create link on paper
	//links.push(paper.connection(entities[from_eid].rjsElement, entities[to_eid].rjsElement, "#fff"));
	// add link to database
	var newURL = '../../Decompositions/add.json'; //'/' + problem_map['ProblemMap']['id'] + '.json';

	$.post(newURL, { entity_id: parent, problem_map_id: problem_map['ProblemMap']['id'] }, function(data){
		//console.log("saving decomposition:");
		//console.log(data);
		if(data['message'] == "Saved"){
			//console.log(parseInt(data['decomp']));
			//entities[child].decomposition_id = parseInt(data['id']);
			entities[parent].setCurrentDecomposition(parseInt(data['decomp']['Decomposition']['id']));
			entities[child].setParentDecomposition(parseInt(data['decomp']['Decomposition']['id']));
			//console.log(data['decomp']);
			decompositions[data['decomp']['Decomposition']['id']] = data['decomp']['Decomposition'];
		}
		
	} );
	
}

// action to remove entity from current decomposition
Entity.prototype.removeFromCurrentDecomposition = function(){
	var entity = this;
	//console.log(entity);
	var newURL = '../../Decompositions/delete/' + this.parent_decomposition + '.json'; //'/' + problem_map['ProblemMap']['id'] + '.json';
	var d_id = this.parent_decomposition;
	
	if(d_id != null && d_id != "null"){
		var found = false;
		for(i in entities){
			if(entity.id != entities[i]['id'] && entities[i].parent_decomposition == d_id){
				//console.log("FOUND another:");
				//console.log(entities[i]);
				found = true;
				break;
			}
		}
		if(!found){
			// remove decomposition here
			//console.log("removing " + d_id);
			//console.log(newURL);
			$.post(newURL, { }, function(data){
				//console.log(data);
				if(data['message'] == "Deleted"){
					entities[decompositions[d_id]['entity_id']].setCurrentDecomposition("null");
					//console.log(decompositions);
					delete decompositions[d_id];
					groups[entity.type].redraw();
				}
			});
		}
	}	
}

// action to set entities current decomposition
Entity.prototype.setCurrentDecomposition = function(d_id){
	
	//this.removeFromCurrentDecomposition(this.id);
	
	//console.log(entity);
	var newURL = '../../Entities/edit_' + entities[this.id].type + '/' + this.id + '.json'; //'/' + problem_map['ProblemMap']['id'] + '.json';
	var id = this.id;
	
//	this.decomposition_id = decomposition_id;
//	this.current_decomposition = current_decomposition;
	
	//console.log("decomp_id: " + d_id);
	//console.log(newURL);

	$.post(newURL, { current_decomposition: d_id }, function(data){
		//console.log("saving entity:");
		//console.log(data);	
		entities[id].current_decomposition = d_id;
		groups[entities[id].type].redraw();
	} );
}

// action to set entities parent decomposition
Entity.prototype.setParentDecomposition = function(d_id){
	
	//this.removeFromCurrentDecomposition(this.id);
		
	//console.log(entity);
	var newURL = '../../Entities/edit_' + entities[this.id].type + '/' + this.id + '.json'; //'/' + problem_map['ProblemMap']['id'] + '.json';
	var id = this.id;
//	this.decomposition_id = decomposition_id;
//	this.current_decomposition = current_decomposition;
	
	//console.log("decomp_id: " + d_id);
	//console.log(newURL);

	$.post(newURL, { decomposition_id: d_id }, function(data){
		/* 
		console.log("saving entity:");
		console.log(data);
		console.log(this);
		*/
		entities[id].parent_decomposition = d_id;
		groups[entities[id].type].redraw();
	} );
}

// action executed when a drag is concluded
Entity.prototype.up = function(){
	
	var overlap = false;
	if(this.lastElementOver && this.lastElementOver != -1 && entities[this.lastElementOver].detectOverlap(this)){
		overlap = true;
		//console.log("overlap");
	}	
	
	//console.log(this.lastElementOver);
	if(this.lastElementOver == -1 && entities[this.eid].parent_decomposition != null && entities[this.eid].parent_decomposition != "null" ){
		//console.log("test");
		// move back to original position
		var s = "T" + (this.group.oBB.x - this.group.originalX) + "," + (this.group.oBB.y - this.group.originalY);

		//console.log(s);				
		this.group.animate({transform: s}, 200, "<", function(){
			// redraw links
			/*
			for (var i = links.length; i--;) {
				paper.connection(links[i]);
			}
			paper.safari();
			*/
		});
		
		// also make the entity opaque again.
		this.group.animate({opacity: 1}, 500, ">");
	}
	else if(overlap){
		
		// if entities are different (or current parent/child) return the entity to its original position and create link.
		if(entities[this.lastElementOver].type != entities[this.eid].type || 
			(entities[this.lastElementOver].current_decomposition == entities[this.eid].parent_decomposition &&
			entities[this.lastElementOver].current_decomposition != null && entities[this.lastElementOver].current_decomposition != "null")){
			// if there is an intersection then move the entity back to its original position.
			var s = "T" + (this.group.oBB.x - this.group.originalX) + "," + (this.group.oBB.y - this.group.originalY);

			//console.log(s);				
			this.group.animate({transform: s}, 200, "<", function(){
				// redraw links
				/*
				for (var i = links.length; i--;) {
					paper.connection(links[i]);
				}
				paper.safari();
				*/
			});
			
			// also make the entity opaque again.
			this.group.animate({opacity: 1}, 500, ">");
		
		}
		
		// this adds the appropriate links overlapping with something other then the current parent entity.
		if(entities[this.lastElementOver].type == entities[this.eid].type &&
			(entities[this.lastElementOver].current_decomposition != entities[this.eid].parent_decomposition || 
			entities[this.lastElementOver].current_decomposition == null || entities[this.lastElementOver].current_decomposition == "null")){
			// this is something to do with creating decompsitions / adding elements to them.
			// move entity to the proper place (animate).
			
			// remove from current decomp
			entities[this.eid].removeFromCurrentDecomposition();
			
			//console.log("last element over: " + this.lastElementOver);
			//console.log(entities[this.lastElementOver]);
			
			// if dragged over entity doesn't have a current decompositon create one and add to it.
			// else add to the current decomposition
			if(entities[this.lastElementOver].current_decomposition == null || entities[this.lastElementOver].current_decomposition == "null"){
				createDecompositon(this.lastElementOver, this.eid);
			}
			else{
				entities[this.eid].setParentDecomposition(entities[this.lastElementOver].current_decomposition);
			}

		}
		else if(entities[this.lastElementOver].type == 'requirement' && entities[this.eid].type == 'function'){
			createLink(this.eid, this.lastElementOver, "satisfies");
		} else if(entities[this.lastElementOver].type == 'function' && entities[this.eid].type == 'requirement'){
			createLink(this.lastElementOver, this.eid, "satisfies");
		} else if(entities[this.lastElementOver].type == 'artifact' && entities[this.eid].type == 'function'){
			createLink(this.lastElementOver, this.eid, "realizes");
		} else if(entities[this.lastElementOver].type == 'function' && entities[this.eid].type == 'artifact'){
			createLink(this.eid, this.lastElementOver, "realizes");
		} else if(entities[this.lastElementOver].type == 'artifact' && entities[this.eid].type == 'behavior'){
			createLink(this.lastElementOver, this.eid, "parameterizes");
		} else if(entities[this.lastElementOver].type == 'behavior' && entities[this.eid].type == 'artifact'){
			createLink(this.lastElementOver, this.eid, "parameterizes");
		} else if(entities[this.lastElementOver].type == 'behavior' && entities[this.eid].type == 'function'){
			createLink(this.lastElementOver, this.eid, "controls");
		} else if(entities[this.lastElementOver].type == 'function' && entities[this.eid].type == 'behavior'){
			createLink(this.eid, this.lastElementOver, "controls");
		} else if(entities[this.lastElementOver].type == 'behavior' && entities[this.eid].type == 'requirement'){
			createLink(this.lastElementOver, this.eid, "manages");
		} else if(entities[this.lastElementOver].type == 'requirement' && entities[this.eid].type == 'behavior'){
			createLink(this.eid, this.lastElementOver, "manages");
		} else if(entities[this.lastElementOver].type == 'artifact' && entities[this.eid].type == 'requirement'){
			createLink(this.lastElementOver, this.eid, "fulfills");
		} else if(entities[this.lastElementOver].type == 'requirement' && entities[this.eid].type == 'artifact'){
			createLink(this.eid, this.lastElementOver, "fulfills");
		}else if(entities[this.eid].type == 'issue'){
			createLink(this.eid, this.lastElementOver, "is related to");
		} else if(entities[this.lastElementOver].type == 'issue'){
			createLink(this.lastElementOver, this.eid, "is related to");
		} 
	}
	else{
		
		// put the entity in its base group
		if(entities[this.eid].parent_decomposition != null && entities[this.eid].parent_decomposition != "null"){
			// remove from current decomp
			entities[this.eid].removeFromCurrentDecomposition();
			entities[this.eid].setParentDecomposition("null");
		}
		else{
		
		
			// move the entity back to its original position.
			var s = "T" + (this.group.oBB.x - this.group.originalX) + "," + (this.group.oBB.y - this.group.originalY);

			//console.log(s);				
			this.group.animate({transform: s}, 200, "<", function(){
					// redraw links
//					for (var i = links.length; i--;) {
//						paper.connection(links[i]);
//					}
//					paper.safari();

			});
		

			/*
			// save the new position in the database.
			var newURL = '../../Entities/edit/' + this.group[0].eid + '.json';

			this.x = this.group.getBBox().x;
			this.y = this.group.getBBox().y;
		
			$.post(newURL, { x: this.x, y: this.y }, function(data){
				//console.log("updating position:");
				//console.log(data);
			} );
			*/
		
			// also make the entity opaque again.
			this.group.animate({opacity: 1}, 500, ">");
		}
	}

}

// function to detect if there is an overlap between an other entity
Entity.prototype.detectOverlap = function(other){
	var Ax1 = this.group.getBBox().x;
	var Ay1 = this.group.getBBox().y;
	var Ax2 = Ax1 + this.group.getBBox().width;
	var Ay2 = Ay1 + this.group.getBBox().height;

	var Bx1 = other.group.getBBox().x;
	var By1 = other.group.getBBox().y;
	var Bx2 = Bx1 + other.group.getBBox().width;
	var By2 = By1 + other.group.getBBox().height;

	if (Ax1 < Bx2 && Ax2 > Bx1 &&
	    Ay1 < By2 && Ay2 > By1)
		return true;
	else
		return false;
}

// action to draw connection between 2 objects
Raphael.fn.connection = function (obj1, obj2, line, bg) {
    if (obj1.line && obj1.from && obj1.to) {
        line = obj1;
        obj1 = line.from;
        obj2 = line.to;
    }
    var bb1 = obj1.getBBox(),
        bb2 = obj2.getBBox(),
        p = [{x: bb1.x + bb1.width / 2, y: bb1.y - 1},
        {x: bb1.x + bb1.width / 2, y: bb1.y + bb1.height + 1},
        {x: bb1.x - 1, y: bb1.y + bb1.height / 2},
        {x: bb1.x + bb1.width + 1, y: bb1.y + bb1.height / 2},
        {x: bb2.x + bb2.width / 2, y: bb2.y - 1},
        {x: bb2.x + bb2.width / 2, y: bb2.y + bb2.height + 1},
        {x: bb2.x - 1, y: bb2.y + bb2.height / 2},
        {x: bb2.x + bb2.width + 1, y: bb2.y + bb2.height / 2}],
        d = {}, dis = [];
    for (var i = 0; i < 4; i++) {
        for (var j = 4; j < 8; j++) {
            var dx = Math.abs(p[i].x - p[j].x),
                dy = Math.abs(p[i].y - p[j].y);
            if ((i == j - 4) || (((i != 3 && j != 6) || p[i].x < p[j].x) && ((i != 2 && j != 7) || p[i].x > p[j].x) && ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
                dis.push(dx + dy);
                d[dis[dis.length - 1]] = [i, j];
            }
        }
    }
    if (dis.length == 0) {
        var res = [0, 4];
    } else {
        res = d[Math.min.apply(Math, dis)];
    }
    var x1 = p[res[0]].x,
        y1 = p[res[0]].y,
        x4 = p[res[1]].x,
        y4 = p[res[1]].y;
    dx = Math.max(Math.abs(x1 - x4) / 2, 10);
    dy = Math.max(Math.abs(y1 - y4) / 2, 10);
    var x2 = [x1, x1, x1 - dx, x1 + dx][res[0]].toFixed(3),
        y2 = [y1 - dy, y1 + dy, y1, y1][res[0]].toFixed(3),
        x3 = [0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx][res[1]].toFixed(3),
        y3 = [0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4][res[1]].toFixed(3);
    var path = ["M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3, x4.toFixed(3), y4.toFixed(3)].join(",");
    if (line && line.line) {
        line.bg && line.bg.attr({path: path});
        line.line.attr({path: path, "arrow-end": "classic-wide-long", "stroke-width": "2"});
    } else {
        var color = typeof line == "string" ? line : "#000";
        return {
            bg: bg && bg.split && this.path(path).attr({stroke: bg.split("|")[0], fill: "none", "stroke-width": bg.split("|")[1] || 3}),
            line: this.path(path).attr({stroke: "black", fill: "none", "arrow-end": "classic-wide-long", "stroke-width": "2"}),
            from: obj1,
            to: obj2
        };
    }
};

// helper functions to do programmatic highlighting
function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

function rgbToHex(R,G,B) {return toHex(R)+toHex(G)+toHex(B)}
function toHex(n) {
 n = parseInt(n,10);
 if (isNaN(n)) return "00";
 n = Math.max(0,Math.min(n,255));
 return "0123456789ABCDEF".charAt((n-n%16)/16)
      + "0123456789ABCDEF".charAt(n%16);
}

/**
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes r, g, and b are contained in the set [0, 255] and
 * returns h, s, and l in the set [0, 1].
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
 */
function rgbToHsl(r, g, b){
    r /= 255, g /= 255, b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0; // achromatic
    }else{
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return [h, s, l];
}

/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
 * Assumes h, s, and l are contained in the set [0, 1] and
 * returns r, g, and b in the set [0, 255].
 *
 * @param   Number  h       The hue
 * @param   Number  s       The saturation
 * @param   Number  l       The lightness
 * @return  Array           The RGB representation
 */
function hslToRgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [r * 255, g * 255, b * 255];
}

function MoveColor(col1, col2, val){
	//val = percent / 100.0;
	
	R1 = hexToR(col1);
	G1 = hexToG(col1);
	B1 = hexToB(col1);
	
	R2 = hexToR(col2);
	G2 = hexToG(col2);
	B2 = hexToB(col2);
	
	R3 = R1 - ((R1 - R2) * val);
	G3 = G1 - ((G1 - G2) * val);
	B3 = B1 - ((B1 - B2) * val);
		
	return '#' + rgbToHex(R3, G3, B3);
	
}

function LightenDarkenColor(col,amt) {
	R = hexToR(col);
	G = hexToG(col);
	B = hexToB(col);
	
	HSL = rgbToHsl(R,G,B);
	
	//console.log(HSL);
	
	RGB = hslToRgb(HSL[0], HSL[1], HSL[2] * (1 + amt/100));
	
	//console.log(HSL[2] * (1 + amt/100));
	
	return '#' + rgbToHex(RGB[0], RGB[1], RGB[2]);
	
}
