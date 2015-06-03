<?

mysql_connect("localhost", "root", "abigfuckupinaugust");
mysql_select_db("rex");

$query = mysql_query("SELECT * FROM `events`");
while($row = mysql_fetch_array($query)){

	$date = $row['Day'];
	$start = $row['Start'];
	$end = $row['End'];
	$id = $row['ID'];

	$start_time = strtotime("$start EDT $date 2014");
	$end_time = strtotime("$end EDT $date 2014");

	if($end == "") $end_time = $start_time + (60*60*2);

	mysql_query("UPDATE `events` SET `Start Time`='$start_time', `End Time`='$end_time' WHERE `ID`='$id'");
	
}
?>