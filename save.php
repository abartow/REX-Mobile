<?
	mysql_connect("localhost", "root", "abigfuckupinaugust");
	mysql_select_db("rex");
	
	$id = mysql_real_escape_string($_POST['id']);

	if($id<451){
		mysql_query("UPDATE `saves` SET `saves`=`saves`+1 WHERE `id`=$id;");
	}


?>