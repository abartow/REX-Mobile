/* This mostly just manages the localStorage and AJAX, and then wraps it in HTML to pass off to the
jQuery mobile listview. */
var searchHidden = true;
var eventsJSON = Array();
var savedEvents = Array();
var firstScreen = true;
var sortedJSON = Array();
var lastFilter;
var lastList;

//This hash ensures the freshness of the event database. Change it when the DB updates
var integrityHash = "9ac386beed4f3f7b5eb25bf35497893bd099c95d";

$(document).ready(function(){

	//Initialize JSON listview
	$("#eventList").listview();
	
	//Detect incognito modes that screw with localStorage
	try {
    	localStorage.setItem("incognitoDetect", "test");
    	localStorage.removeItem("incognitoDetect");
  	} catch(e){
    	alert("Please disable private browing mode to use REX Mobile.");
  	}

  	//Load saved events if set
	if(localStorage.getItem("savedEvents")){
		savedEvents = JSON.parse(localStorage.getItem("savedEvents"));
	}else{
		savedEvents.push(82, 116, 156, 228, 257, 407, 435);
		localStorage.setItem("savedEvents", JSON.stringify(savedEvents));
	}
	if(!localStorage.getItem("alertShown")){
		alert("Guide 42 has been updated! It now includes most events from the Hitchhiker's Guide, as well as the REX Events.");
		localStorage.setItem("alertShown", "guide42");
	}
	//Check to see if there's a cache of events, or if we need to download it
	if(localStorage.getItem("eventsJSON_cache") && Sha1.hash(localStorage.getItem("eventsJSON_cache")) == integrityHash){
	
		eventsJSON = JSON.parse(localStorage.getItem("eventsJSON_cache"));
		sortedJSON = eventsJSON;
		updateLists();
		
	}else{
		downloadJSON();
	}
	
});

//Fetch JSON from server and store it in localStorage to speed future load times
//This is especially needed on the sometimes spotty cell networks around MIT
//Or maybe I just need a new cell phone company. Either way, it's happening.
function downloadJSON(){
	$.ajax({
		url: "events2.json",
		success: function(data){

			console.log("JSON downloaded");

			localStorage.setItem("eventsJSON_cache", JSON.stringify(data));

			eventsJSON = data;
			sortedJSON = eventsJSON;


			updateLists();
		}
	});
}

function quickSearch(searchArgs){
	//Replaces the updateLists search function
	//Variables are perserved for legacy reasons
	$(".ui-input-search").find("input").val(searchArgs.q);
	$("#eventList").listview("refresh");
}
function toggleSearch(){
	//Check whether options dialog is shown
	if($("#options").hasClass("hidden")){
		$("#backBtn").show();
		$("#searchBtn").hide();
		$(".ui-input-search").show();
		$("#options").show();
		$("#options").removeClass("hidden");
		$('html, body').animate({scrollTop : 0},800);
	}else{
		$("#backBtn").hide();
		$("#searchBtn").show();
		$(".ui-input-search").hide();
		$("#options").hide();
		$("#options").addClass("hidden");
	}
}
//Hide search is used by the filters
function hideSearch(){
	$("#backBtn").hide();
	$("#searchBtn").show();
	$(".ui-input-search").hide();
	$("#options").hide();
	$("#options").addClass("hidden");
	$('html, body').animate({scrollTop : 0},500);
}

//Main meat of the app
function updateLists(filter_obj){
		
		if(filter_obj == undefined) {
			filter_obj = {mode: "off", q: ""};

		}

		//Last filter mode is only used by saveEvent();
		if(filter_obj.mode !== "last"){
			lastFilter = filter_obj;

		}else{
			filter_obj = lastFilter;
			var sQ = $(".ui-input-search").find("input").val();
		}
		
		//Clear search fields and selected filters
		quickSearch("");
		$("#options").find("button").removeClass("selected");
		$("#btn_" + filter_obj.mode).addClass("selected");

		if(filter_obj.mode == "off") {
			//By default, show events that aren't over
			
			$("#eventList").html('<li data-role="list-divider">All Ongoing and Upcoming Events</li>');
			
			var now = new Date();
			
			$.each(eventsJSON, function(index, value) {
				////console.log(value);
				var enddate = new Date(value.end*1000);
				if(enddate.getTime() > now.getTime()) {
					////console.log("printed " + value.id);
					print_event(value);
				}
			});
		}

		if(filter_obj.mode == "all"){
			//Shows All Events

			$("#eventList").html('<li data-role="list-divider">All Events</li>');

			//Sorts events alphabetically
			sortedJSON.sort(function(a, b){ 
				////console.log(a + "," + b)
				if(a.name.toUpperCase() > b.name.toUpperCase()) return 1;
				if(b.name.toUpperCase() > a.name.toUpperCase()) return -1;
				return 0;
			});

			var indexLetter;
			$.each(sortedJSON, function(index, value){
				if(value.name.substr(0,1).toUpperCase() != indexLetter){
					indexLetter = value.name.substr(0,1).toUpperCase();
					$("#eventList").append("<li data-role='list-divider'>" + indexLetter + "</li>");
				}
				////console.log(value);
				print_event(value);
			});
			//Puts them back in order by start time.
			eventsJSON.sort(function(a, b){ 
				if(a.start > b.start) return 1;
				if(b.start > a.start) return -1;
				return 0;
			})
		}
		if(filter_obj.mode == "over") {
			//Events that are already over
			
			$("#eventList").html('<li data-role="list-divider">All Concluded Events</li>');
			
			var now = new Date();
			//If the user is looking for an event that finished, it probably finished recently.
			//Show the most recent events first.

			//Put them in descending order by end time
			eventsJSON.sort(function(a, b){ 
				if(a.end > b.end) return -1;
				if(b.end > a.end) return 1;
				return 0;
			})

			$.each(eventsJSON, function(index, value) {
				////console.log(value);
				var enddate = new Date(value.end*1000);
				if(enddate < now) {
					print_event(value);
				}
			});

			//Put them back in ascending order by start time
			eventsJSON.sort(function(a, b){ 
				if(a.start > b.start) return 1;
				if(b.start > a.start) return -1;
				return 0;
			})

		}
		
		//This isn't used now because jQuery lists search is more user friendly.
		//But really this would be the best way to search dorms if we ever customized jQuery in the future
		//the current implementation returns false positives sometimes. (Like baking events when searcing for
		// the Baker dorm)
		if(filter_obj.mode == "dorm") {
			//Filter by dorm
			
			$("#eventList").html('<li data-role="list-divider">Filter by Location: ' + filter_obj.q + '</li>');
			
			$.each(eventsJSON, function(index, value) {
				if(value.dorm.indexOf(filter_obj.q) == 0) {
					print_event(value);
				}
			});
		}
		
		if(filter_obj.mode == "nearby") {
			//Filter by location
			
			$("#eventList").html('<li data-role="list-divider">Filter: Nearby Now</li>');
			$("#eventList").append('<li><p style="float:left;margin-top:20px;margin-left:-15px;">Computing location...</p><img src="opt_spinner.gif" style="width:80px;height:60px;float:right;margin-right:10px;" /></li>');

			//We refresh list views often in this block because Geolocation can leave the user waiting
			//for long times in strange places.
			$("#eventList").listview("refresh");
			//Get geo access
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(pos) {
					$("#eventList").html('<li data-role="list-divider">Filter: Nearby Now</li>');
					////console.log(pos.coords.latitude + ", " + pos.coords.longitude);
					if(pos.coords.latitude < 42.35 || pos.coords.latitude > 42.365 || pos.coords.longitude < -71.113 || pos.coords.longitude > -71.075) {
						$("#eventList").append("<li>You are not near MIT. :(</li>");
						$("#eventList").listview("refresh");
					} else {
					
						var now = new Date();

						var foundEvent = false;
						var detectedEvents = Array();
						$.each(eventsJSON, function(index, value) {
							//Using Euclidean geometry on a sphere is wrong, but the area here is so small it'll be okay. 0.00835 degrees = 535m in Cambridge
							//Perhaps implement Haversine in the future?
							var start = new Date((new Date(value.start*1000)).getTime() - 30*60000); //Set start time back 30 min to allow events about to start to make the list
							//////console.log(start, value.start);
							var end = new Date(value.end*1000);

							//Both of these ought to be positive
							var started = (now.getTime() - start.getTime());
							var ended = (end.getTime() - now.getTime());


							var distLength = distance(parseFloat(value.lat), parseFloat(value.lng), pos.coords.latitude, pos.coords.longitude);
							if((distLength < 0.00835) && (ended > 0) && (started > 0)) {
								foundEvent = true;
								var closeEvent = {distLen: distLength, info: value};

								//Don't print anything yet. We'll need to order them in a useful manner.
								detectedEvents.push(closeEvent);

							}else{
								//console.log(distLength + ", started=" + started + ", ended=" + ended);
							}
						});

						//This just makes the function more useful when no events occur.
						//Really it just reimplements functionality found elsewhere
						//But it makes the user feel good. I like that.
						if(foundEvent == false){
							var eventsFound = 0;
							var i = 0;
							$("#eventList").append("<li>No events are currently in progress nearby. The next five events to start are displayed below.</li>");
							while(eventsFound < 5){
								
								//BE SURE TO DIVIDE/MULTIPLY BY 1000 WHEN USING PHP GENERATED TIMESTAMPS IN JS!!!
								if(eventsJSON[i].end > (now.getTime() / 1000)){
									print_event(eventsJSON[i]);
									eventsFound++;
								}
								i++;
							}

							$("#eventList").listview("refresh");
						}else{
							//console.log("found events, sorting by distance")
							detectedEvents.sort(function(a, b){ 
								if(a.distLen > b.distLen) return 1;
								if(b.distLen > a.distLen) return -1;
								return 0;
							});
							$.each(detectedEvents, function(index, value){
								print_event(value.info, value.distLen);
							});
							$("#eventList").listview("refresh");
						}
					}
				});

		$("#eventList").listview("refresh");
			} else {
				$("#eventList").html('<li data-role="list-divider">Filter: Nearby Now</li>');
				$("#eventList").append("<li>Geolocation is not supported by this browser.</li>");

				$("#eventList").listview("refresh");
			}
		}

		$("#eventList").listview("refresh");
		
		if(filter_obj.mode == "saved") {
			//Filter by saved status
			var now = new Date();
			$("#eventList").html('<li data-role="list-divider">Upcoming and Current Saved Events</li>');
			var finishedEvents = Array();
			$.each(eventsJSON, function(index, value) {
				
				if(isSaved(parseInt(value.id))) {
					//Only Print Events that haven't concluded for now
					if(parseInt(value.end) > (now.getTime()/1000)){
						print_event(value);
					}else{
						finishedEvents.push(value);
					}
				}
			});

			//Put them in descending order by end time
			finishedEvents.sort(function(a, b){ 
				if(a.end > b.end) return -1;
				if(b.end > a.end) return 1;
				return 0;
			})

			//Put finshed events at the bottom
			$("#eventList").append('<li data-role="list-divider">Concluded Saved Events</li>');
			$.each(finishedEvents, function(index, value){
				print_event(value);
			});
		}
		

		//This has been deprecated.
		if(filter_obj.mode == "search") {
			//Filter by saved status
			
			$("#eventList").html('<li data-role="list-divider">Filter by Search: ' + filter_obj.q + '</li>');
			
			$.each(eventsJSON, function(index, value) {
				
				if(value.name.toLowerCase().indexOf(filter_obj.q.toLowerCase()) >= 0 || value.description.toLowerCase().indexOf(filter_obj.q.toLowerCase()) >= 0 || value.location.toLowerCase().indexOf(filter_obj.q.toLowerCase()) >= 0) {
					print_event(value);
				}
			});
		}

		//Most saved events list

		if(filter_obj.mode == "top"){

			//This involves a network call, so show a pretty slider.
			$("#eventList").html("<li data-role='list-divider'>Most Saved Events</li>");
			$("#eventList").append('<li><p style="float:left;margin-top:20px;margin-left:-15px;">Loading list...</p><img src="opt_spinner.gif" style="width:80px;height:60px;float:right;margin-right:10px;" /></li>');
			$("#eventList").listview("refresh");

			$.ajax({
				url: 'top.php',
				success: function(data){

					//We need some way to fetch events by ID;
					var eventsByID = Array();

					$.each(eventsJSON, function(index, value){
						eventsByID[value.id] = value;
					});

					var topEvents = JSON.parse(data);

					//Loop through the most popular events and draw the list
					$("#eventList").html("<li data-role='list-divider'>Most Saved Events</li>");
					$("#eventList").append("<li><p>Most popular events sorted by number of saves.</p><p>At least five saves required to appear</p></li>");
					$.each(topEvents, function(index, value){
						print_event(eventsByID[value.id]);
					});
					$("#eventList").listview("refresh");


				}
			})

		}



		$("#eventList").listview('refresh');

		if(sQ !== "" && sQ !== undefined){
			console.log("attempting to readd q:" + sQ);
			$(".ui-input-search").find("input").val("");
			$(".ui-input-search").find("input").change();
			$(".ui-input-search").find("input").val(sQ);
			$(".ui-input-search").find("input").change();
		}

}

function print_event(value, dist) {
	//Display Distance to event if given
	if(dist !== undefined && dist !== null){
		//Converts distance in degrees to meters, and then rounds. 1 degree = 64000 meters
		var distString = " (" + (Math.round((dist * 64000 * 100))/100) + " meters away)"
	}else{
		var distString = "";
	}
	if(isSaved(value.id)){
		var highlightStyle = " style='background-color:lightyellow;'";
	}else{
		var highlightStyle = "";
	}
	$("#eventList").append("<li><a href='item.php?id=" + value.id + "'" + highlightStyle +  "><h1>" + value.name + "</h1><h1 style='font-style:oblique;font-weight:lighter;'>"+ value.friendlyTime + "</h1><p>" + value.description + "</p><p>"+ value.location + distString + "</p></a></li>");
}

function distance(x1, y1, x2, y2) {
	return Math.sqrt( Math.pow(x2-x1, 2) + Math.pow(y2-y1, 2) );
}

function isSaved(id) {
	for(var i = 0; i < savedEvents.length; i++) {
		if(savedEvents[i] == id) { return true; }
	}
	return false;
}

function saveEvent(id){
	var saved = false;
	if(savedEvents.indexOf(id) >= 0){
		savedEvents.splice(savedEvents.indexOf(id), 1); //Remove saved event
	}else{
		savedEvents.push(id); //put the ID into the saved events array
		saved = true;
	}
	$(".ui-input-btn").html("Done :)");
	localStorage.setItem("savedEvents", JSON.stringify(savedEvents));
	var searchString;
	if($(".ui-input-search").find("input").val() !== "" || $(".ui-input-search").find("input").val() == undefined){
		searchString = $(".ui-input-search").find("input").val();
	}else{
		searchString = "";
	}
	updateLists({mode: "last", q: searchString});

	if(saved == true){
		$.ajax({
			url: "save.php",
			method: "POST",
			data: {"id": id},
			success: function(data){
				console.log("signal 7")
			}
		})
	}
}