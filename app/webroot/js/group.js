// function to create a group
function Group(name, set, offset) {
	this.name = name;
	this.elements = null;
	this.set = [];
	this.offset = offset;
	this.xoffset = 0;
	this.yoffset = 0;
	this.width = 0;
	this.paper = null;
}

// function to add an entity to the group
Group.prototype.add = function(entity){
	this.set.push(entity);
	this.redraw();
}

// function to update an entity in the group
Group.prototype.update = function(entity){
	for(i in this.set){
		if(entity.id == this.set[i].id)
			this.set[i] = entity;
	}
}

// function to remove an entity from the group by id.
Group.prototype.remove = function(eid){
	for(var i in this.set){
		if(this.set[i].id == eid){
			this.set.splice(i,1);
			break;
		}
	}
	this.redraw();
}

// function to redraw the group
Group.prototype.redraw = function(){
	this.elements.remove();
	//this.elements = this.paper.set();
	//console.log(this.set);
	this.yoffset = 25 + this.group_text.getBBox().height;
	for(i = 0; i < this.set.length; i++){
//		console.log(this.set[i]);
		var element = this.set[i].draw(this.paper, this.xoffset, this.yoffset, this.width);
		if(element){
			this.yoffset += element.getBBox().height;
			this.elements.push(element);
		}
	}
	
	if(stuck){
		highlight(stuckFocus,0,false, '#FFE87C');
		
		for(i in entities[stuckFocus].outLinks){
			highlight(entities[stuckFocus].outLinks[i],0,true, '#FFE87C');
		}
		for(i in entities[stuckFocus].inLinks){
			highlight(entities[stuckFocus].inLinks[i],0,true, '#FFE87C');
		}
	}
	if(searchActive){
		highlight(searchFocus,0,true,'#0000ff');
	}
}

// function to create a group given the raphael paper
Group.prototype.create = function(paper){
	
	this.paper = paper;
	
	var height = 2 * $('#canvas_container').height();
	var width = $('#canvas_container').width();
	this.width = width/5;
	this.group_shape =  paper.rect(this.offset * width/5, 0, width/5, height, 0).attr({fill: "white", stroke: "black", opacity: 1});
	this.group_text = 	paper.text(width/10 + this.offset * width/5, 20, this.name);
	this.group_text.attr({"font-size": 16});
	this.xoffset = this.offset*width/5;
	this.yoffset = 25 + this.group_text.getBBox().height;
	
	this.elements = paper.set();

}

// function to resize the group
Group.prototype.resize = function(){
	var height = $('#canvas_container').height();
	var width = $('#canvas_container').width();
	
	this.group_shape.attr({"x": this.offset*width/5, "width": width/5, "height": height});
	this.group_text.attr({"x": width/10 + this.offset * width/5});
	
	this.width = width/5;
	this.xoffset = this.offset * width/5;
	
	this.redraw();
}