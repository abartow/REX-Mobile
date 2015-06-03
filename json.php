<?php

	mysql_connect("localhost", "root", "abigfuckupinaugust");
	mysql_select_db("rex");

	$i = 0;
	$query = mysql_query("SELECT * FROM events, locations WHERE events.Dorm=locations.place ORDER BY `Start Time` ASC") or die(mysql_error());
	
	while($row = mysql_fetch_array($query)){
	
		$event['name'] = $row['Event'];
		$event['description'] = $row['Description'];
		$event['location'] = $row['Location'];
		$event['start'] = $row['Start Time'];
		$event['end'] = $row['End Time'];
		if($row['End'] != ""){
			$event['friendlyTime'] = $row['Day'].": ".$row['Start']." - ".$row['End'];
		}else{
			$event['friendlyTime'] = $row['Day'].": ".$row['Start'];
		}

		$event['id'] = $row[0];
		$event['dorm'] = $row['Dorm'];
		$event['room'] = $row['Room'];
		$event['lat'] = $row['lat'];
		$event['lng'] = $row['lng'];
		$event['img'] = $row['img'];
		$event['building'] = $row['Building'];
		$event['address'] = $row['Address'];

		foreach($event as &$string){
			$string = utf8_encode($string);
		}
		$events[$i] = $event;
		$i++;
	}
	if($_GET['save']=="yes") file_put_contents("events2.json", json_encode($events));
	echo json_encode($events);
?>