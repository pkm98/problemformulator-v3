$( document ).ready(function() {
    // var htmlstr = $("textarea#objtree").val();
	// $("div#objtree_form").html(htmlstr);
	// $("textarea#objtree").remove();
	init_objtree_form();
});

function init_objtree_form(){
	// edit button
	$("#edit_weight").button();
	$("#edit_weight").click(function() { 
		$("select").each(function(){
			$(this).removeAttr("disabled");
		});
		$('#edit_weight, #print_objtree').css("display", "none");
		$('#calculate_weight, #cancel_edit').removeAttr("style");
	});
	// print button
	$("#print_objtree").button();
	$("#print_objtree").click(function() { 
		var url = window.location.pathname;
		var url_arr = url.split("/");
		var id = url_arr[url_arr.length - 1];
		window.open("../../problem_maps/print_objtree/"+id,'_newtab');
	});
	// calculate button
	$("#calculate_weight").button();
	$("#calculate_weight").click(function() { 
		var flag = calculate_weight();
		if(!flag) 
			return false;
		$('#submit_weight').removeAttr("style");
	});
	// submit button
	$("#submit_weight").button();
	$("#submit_weight").click(function() { 
		submit_weight();
		reset_buttons();
	});
	// cancel button
	$("#cancel_edit").button();
	$("#cancel_edit").click(function() { 
		location.reload();
	});
	// to automatically calculates and saves weight when entity is added/modified
	$("#calculate_weight").click();
	$("#submit_weight").click();
}

function reset_buttons(){
	$('#edit_weight, #print_objtree').removeAttr("style");
	$('#calculate_weight, #submit_weight, #cancel_edit').css("display", "none");
	$("select").each(function(){
		$(this).attr("disabled","disabled");
	});
}

function calculate_weight(){
	var flag = 0;
	$("table").each(function(){
		var table = $(this);
		var select = table.find("select");
		
		var total = 0;
		var values = [];
		var select_ids = [];
		
		$(select).each(function(){
			var value = $(this).val();
			value = parseInt(value);
			values.push(value);
			select_ids.push($(this).attr("id"));
			total += parseInt(value);
		});
		if(flag) return false;
		
		for(var i = 0; i < values.length; i++){
			$("span#"+select_ids[i]).text(parseFloat(values[i]/total).toFixed(3));
		}
	});
	if(flag)
		return false;
	else
		return true;
}

function submit_weight(){
	$("select").each(function(){
		var id = $(this).attr("id");
		var weight = $("span#"+id).text();
		var weight_option = $(this).val();
		$.get("../../problem_maps/save_objtree_weights/"+id+"/"+weight+"/"+weight_option, function(d){});
	});
}
