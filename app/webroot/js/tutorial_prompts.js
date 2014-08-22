$(document).ready(function(){
	tutorial_prompt('start');
});

function tutorial_prompt(step){
	$.get("../tutorial_prompts/"+step, function(d){
		$("#tutorial_prompt").html(d);
	});
}

function tutorial_switch(pid){
	var tutorial_on = 0;
	if($('#tutorial_switch').is(':checked')){
		tutorial_on = 1;
	}
	
	$.get("../tutorial_switch/"+pid+"/"+tutorial_on, function(d){});
	$('#prompt_container').slideToggle('slow');
}

