	<?php
	mysql_connect("localhost", "root", "abigfuckupinaugust");
	mysql_select_db("rex");

	$query = mysql_query("SELECT * FROM `saves` WHERE `saves` > 4 ORDER BY `saves` DESC");

	$i = 1;
	while($row = mysql_fetch_array($query)){
		$event['id'] = $row["id"];
		$event['saves'] = $row['saves'];

		$events[$i] = $event;
		$i++;
	}

	echo json_encode($events);
	?>