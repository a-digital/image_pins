$(document).ready(function(){
	//start of json string
	var obj = "{";
	//loop through each marker
	$("#draggables").find("div").each(function(){
		//if it is draggable
		if ($(this).attr("data-draggable") === "yes"){
			//add the marker id and css positioning to our json string
			obj += '"'+$(this).attr("id")+'": {"top": "'+$(this).css("top")+'", "left": "'+$(this).css("left")+'"},';
		}
	});
	//remove trailing comma from our json string
	obj = obj.slice(0, -1);
	//add closing bracket
	obj += "}";
	//parse our json string into a json object
	obj = jQuery.parseJSON(obj);
	//if we have old values previously submitted
	if ($("#old_values").html().length !== 0) {
		//create object for these values from the string
		var oldobj = jQuery.parseJSON($("#old_values").html());
	}
	//grab our field_id
	var field_id = $("#pin_field_id").text();
	//loop through each marker
	$("#draggables").find("div").each(function(){
		//if it is draggable
		if ($(this).attr("data-draggable") === "yes"){
			//apply the draggable ui to this pin
			$(this).draggable({
				//at the start of movement
				start: function(event, ui){
					//hide the hover enabled label
					$(this).find(".pin-hover").hide();
				},
				//at the end of movement
				stop: function(event, ui){
					//clear the hover label inline display styles
					$(this).find(".pin-hover").css("display", "");
					//get the pin id
					var pin_id = $(this).attr("id");
					//calculate the css top position as a percentage
					newtop = $(this).position().top / $(this).parent().height() * 100;
					//calculate the css left position as a percentage
					newleft = $(this).position().left / $(this).parent().width() * 100;
					//update our json objects top value for this pin
					obj[pin_id].top = newtop + "%";
					//update our json objects left value for this pin
					obj[pin_id].left = newleft + "%";
					//if either the top or left positioning is outside of the parent div
					if ((newtop > 0 && newtop < 100) && (newleft > 0 && newleft < 100)) {} else {
						//update our json objects top value to a default
						obj[pin_id].top = "auto";
						//update our json objects left value to a default
						obj[pin_id].left = "auto";
					}
					//stringify our json object and place in our pins input
					$("input[name='pins_field_id_"+field_id+"']").val(JSON.stringify(obj));
				}
			});
			//get the pin id
			var pin_id = $(this).attr("id");
			//if we have an old position and it exists in our new object
			if (oldobj[pin_id] && obj[pin_id]) {
				//set the new object to inherit the old position
				obj[pin_id] = oldobj[pin_id];
			}
			//if the position is auto on either top of left
			if (obj[pin_id].top == "auto" && obj[pin_id].left == "auto") {
				//bring the pin to the top of the element
				$(this).css("top", "0");
				//and offset to the left as if in a sidebar
				$(this).css("left", "-4%");
			//if the pin has a position
			} else {
				//set the pins css top position to match the objects
				$(this).css("top", obj[pin_id].top);
				//set the pins css left position to match the objects
				$(this).css("left", obj[pin_id].left);
			}
		}
	});
	//stringify our json object and place in our pins input
	$("input[name='pins_field_id_"+field_id+"']").val(JSON.stringify(obj));
});