 <?php
 	/*error_reporting('-1');
 	ini_set("display_errors", "true");*/

	mysql_connect("localhost", "root", "abigfuckupinaugust");
	mysql_select_db("rex");
	$id = mysql_real_escape_string($_GET['id']);
	
?>
 <head>
	<title>Guide42 - Event Listing</title>
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.css" />
	<script src="https://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
	<script src="js.js" type="text/javascript"></script>
	<script src="sha.js" type="text/javascript"></script>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	
</head>
<body>
	<div id="topBar" style="z-index:100;position:fixed;width:100%;background-color:#f9f9f9;height: 51px;border-bottom: solid 1px #bbb;"><img id="backBtn" src="back.png" style="width:40px;height:26px;float:left;margin-top:10px;margin-left:20px;" onclick="window.history.back();" /><p style="margin-left:-60px;margin-top:15px;text-align:center;width:100%">Guide42</p></div>
	<div id="item" style="margin-top:50px;">
		<?php
				$query = mysql_query("SELECT e.ID, e.Day, e.`Event`, e.Location, e.Dorm, e.Room, l.lat, l.lng, l.Building, l.Address, l.img, e.Description, e.`Start Time`, e.`End Time`, e.Start, e.End FROM events AS e LEFT JOIN locations as l ON e.Dorm=l.place WHERE e.ID = ". $id .";");
				$row = mysql_fetch_array($query);
				
			?>

		<script>
		$(document).ready(function(){
			var id = <?php echo $id; ?>;
			if(!$("html").hasClass("initialPage")){
				window.location.replace("index.php?redir=" + id);
			}
			console.log("attempting save detection");
			if(savedEvents.indexOf(id) > -1){
				$("#saveButton").val("Remove from Saved Events List");

			}else{
				$("#saveButton").val("Add to Saved Events List");
			}
			var now = new Date();
			var startUnix = new Date(<? echo $row['Start Time'];?>*1000);
			var endUnix = new Date(<? echo $row['End Time'];?>*1000);
			if(startUnix.getTime() > now.getTime()){
				$(".timebadge").addClass("upcoming");
				if((startUnix.getTime() - now.getTime()) < (91 * 60 * 1000)){
					$(".timebadge").html("Starts in " + Math.floor((startUnix.getTime() - now.getTime())/60000) + " Minutes");
				}else{
					$(".timebadge").html("Upcoming");
				}
			}
			if((startUnix.getTime() < now.getTime()) && (endUnix.getTime() > now.getTime())){
				$(".timebadge").addClass("inprog");
				$(".timebadge").html("In Progress");
			}
			if((endUnix.getTime() < now.getTime())){
				$(".timebadge").addClass("over");
				$(".timebadge").html("Ended");
			}
		})
		</script>
		<ul data-role="listview" id="listItem" style="padding:10px;">
				<li>
					<h1 style="font-weight:bold;"><?php echo $row['Event']; ?></h1>
					
					<?php 
					if($row['End'] != ""){
						$happening = $row['Day'].": ".$row['Start']." - ".$row['End'];
					}else{
						$happening = $row['Day'].": ".$row['Start'];
					}
					echo $happening; ?>
					<span class="timebadge"></span>
					
					<br />
					<?php echo ($row['Location'] == "NULL") ? "" : ("<p><strong>".$row["Dorm"].( $row["Room"]=="" ? "" : (" &gt; ".$row["Room"]))."</strong></p>"); ?>
					<br />
					
					<?php if(isset($row['Building']) && $row['Building'] != "" && $row['Building'] != "NULL") { ?>
						<p id="dorm_img" style='float:none;text-align:center;padding:25px;padding-top:0px;'><img src='http://web.mit.edu/campus-map/objimgs/object-<?php echo $row['Building']; ?>.jpg' alt="View of Dorm" /></p>
					<? } ?>
					<ul data-role="listview">
					<li style="white-space:normal !important"><?php echo $row['Description']; ?></li>
					<li data-role="list-divider">Share</li>
					<li><a onclick="window.prompt('Copy this Link', 'https://www.abartow.com/rex/item.php?id=<? echo $id; ?>')">Share Link</a></li>
					<li data-role="list-divider">Export to Calendar (Opens New Window)</li>
					<li><a target="_blank" href="ics.php?id=<? echo $id; ?>">Export To iCal</a></li>
					<li><a href="http://www.google.com/calendar/event?action=TEMPLATE&text=<? echo $row['Event']?>&dates=<? echo date('Ymd\\THi00\\Z', $row['Start Time']); ?>/<? echo date('Ymd\\THi00\\Z', $row['End Time']); ?>&details=<? echo $row['Description']; ?>&location=<? echo $row['Address']; ?>&trp=false&sprop=&sprop=name:" target="_blank" rel="nofollow">Add to Google Calendar</a></li>
					<? if(isset($row['Building']) && $row['Building'] != "" && $row['Building'] != "NULL"){ ?>
					<li data-role="list-divider">Directions (Opens New Window)</li>
					<li><a href='https://www.google.com/maps/dir//<?php echo urlencode($row['Address']); ?>' target="_blank"><?php echo $row['Address']; ?></a></li>
					<li><a href="http://whereis.mit.edu/?go=<?php echo $row["Building"]; ?>" target="_blank">View Building <?php echo $row["Building"]; ?> on MIT Where&#8201;Is...</a></li>
					<? } ?>
					</ul>
					
					
					<input type='button' id='saveButton' style='float:right' value='Save This Event' onclick='saveEvent(<?php echo $id; ?>)' />
				</li>
			
		</ul>
	</div>
</body>
</html>
