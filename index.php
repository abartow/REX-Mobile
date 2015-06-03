<html class="initialPage">
<head>
	<title>Guide42</title>
	
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.js"></script>
	<script src="sha.js" type="text/javascript"></script>
	<script src="js.js"></script>
	
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	<link rel="shortcut icon" type="image/x-icon" href="apple-touch-icon.png" />
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.3/jquery.mobile-1.4.3.min.css" />
	<style type="text/css">
	#options{
		padding: 25px;
		padding-top: 0px;
		background-color: #ededed;
		padding-bottom: 15px;
		border-top: solid 1px #bbb;
	}
	.ui-input-search{
		display: none;
		margin:17px;
	}
	.selected{
		border: 5px solid #ffffff !important;
	}
	</style>
	<style type="text/css">
		.timebadge {
			font-size:10px;
			padding:2px;
			border-radius: 3px;
		}
		
		.upcoming {
			background-color: green; color: lightgreen; border: thin solid lightgreen;
		}
		
		.inprog {
			background-color: yellow; color: orange; border: thin solid orange;
		}
		
		.over {
			background-color: red; color: pink; border: thin solid pink;
		}
		#topBar{
			opacity: 0.9;
			font-weight:bold;
		}
		@media only screen and (min-device-width : 320px) and (max-device-width : 640px) and (orientation : portrait) {
			#dorm_img{
				float:none;
				text-align:center;
			}
		}
	</style>
	<script>
	$(document).ready(function(){
		<? 
			if(isset($_GET['redir'])){
				echo "var redir = ".$_GET['redir'].";";
			}else{
				echo "var redir = -1;";
			}
		?>
		if(redir > -1 && document.referrer !== ""){
			$.mobile.changePage("item.php?id=" + redir);
		}
	});
	</script>
</head>
<body>
	<div id="topBar" style="width:100%;background-color:#f9f9f9;border-bottom: solid 1px #bbb;position:fixed;z-index:100;">
		<p style="text-align:center;width:100%;margin-top:15px;">Guide42</p>
		<img id="backBtn" src="close.png" style="display:none;width:40px;height:40px;float:right;margin-top:-46px;margin-right:10px;" onclick="toggleSearch();" />
		<img id="searchBtn" src="search.png" style="width:50px;height:50px;float:right;margin-top:-49px;" onclick="toggleSearch();" />
	</div>
	<div id="pageContent" style="margin-top:50px;">
	<div id="options" style="display:none;" class="hidden">
			<h4>Event Categories</h4>
			<button id="btn_off" onClick="updateLists({mode:'off', q:''});hideSearch();" style="background-color:lightgreen;">Ongoing and Upcoming</button>
			<button id="btn_nearby" onClick="updateLists({mode:'nearby', q:''});hideSearch();" style="background-color:lightblue;">Now and Nearby</button>
			<button id="btn_saved" onClick="updateLists({mode:'saved', q:''});hideSearch();" style="background-color:yellow;">Saved</button>
			<button id="btn_top" onClick="updateLists({mode:'top', q:''});hideSearch();" style="background-color:lightsalmon;">Most Saved</button>
			<button id="btn_over" onClick="updateLists({mode:'over', q:''});hideSearch();" style="background-color:lightpink;">Concluded</button>
			<button id="btn_all" onClick="updateLists({mode:'all', q:''});hideSearch();">All Events</button>
			<div data-role="collapsible">
				<h2>Location Quick Search</h2>
				<button onClick="quickSearch({mode:'dorm', q:'Baker'});hideSearch();">Baker</button>
				<button onClick="quickSearch({mode:'dorm', q:'Burton-Conner'});hideSearch();">Burton-Conner</button>
				<button onClick="quickSearch({mode:'dorm', q:'East Campus'});hideSearch();">East Campus</button>
				<button onClick="quickSearch({mode:'dorm', q:'Kresge Auditorium'});hideSearch();">Kresge Auditorium</button>
				<button onClick="quickSearch({mode:'dorm', q:'Kresge Oval'});hideSearch();">Kresge Oval</button>
				<button onClick="quickSearch({mode:'dorm', q:'MacGregor'});hideSearch();">MacGregor</button>
				<button onClick="quickSearch({mode:'dorm', q:'Maseeh hall'});hideSearch();">Maseeh Hall</button>
				<button onClick="quickSearch({mode:'dorm', q:'McCormick'});hideSearch();">McCormick</button>
				<button onClick="quickSearch({mode:'dorm', q:'McDermott Dot'});hideSearch();">McDermott Dot</button>
				<button onClick="quickSearch({mode:'dorm', q:'New House'});hideSearch();">New House</button>
				<button onClick="quickSearch({mode: 'dorm', q:'Next House'});hideSearch()">Next House</button>
				<button onClick="quickSearch({mode:'dorm', q:'Random Hall'});hideSearch();">Random Hall</button>
				<button onClick="quickSearch({mode:'dorm', q:'Senior Haus'});hideSearch();">Senior Haus</button>
				<button onClick="quickSearch({mode:'dorm', q:'Simmons'});hideSearch();">Simmons</button>
				<button onClick="quickSearch({mode:'dorm', q:'Z-Center'});hideSearch();">Z-Center</button>
				<button onClick="quickSearch({mode:'dorm', q:'MIT Chapel'});hideSearch();">MIT Chapel</button>
				<button onClick="quickSearch({mode:'dorm', q:'Johnson Athletics Center'});hideSearch();">Johnson Athletics Center</button>
				<button onClick="quickSearch({mode:'dorm', q:'Stratton Student Center'});hideSearch();">Stratton Student Center</button>
				<button onClick="quickSearch({mode:'dorm', q:'Weisner Building'});hideSearch();">Weisner Building</button>
				<button onClick="quickSearch({mode:'dorm', q:'Walker Memorial'});hideSearch();">Walker Memorial</button>

			</div>
			<div data-role="collapsible">
				<h2>Date Quick Search</h2>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 21'});hideSearch();">Thursday 8/21</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 22'});hideSearch();">Friday 8/22</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 23'});hideSearch();">Saturday 8/23</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 24'});hideSearch();">Sunday 8/24</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 25'});hideSearch();">Monday 8/25</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 26'});hideSearch();">Tuesday 8/26</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 27'});hideSearch();">Wednesday 8/27</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 28'});hideSearch();">Thursday 8/28</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 30'});hideSearch();">Saturday 8/30</button>
				<button onClick="quickSearch({mode:'dorm', q:'Aug. 31'});hideSearch();">Sunday 8/31</button>
				<button onClick="quickSearch({mode:'dorm', q:'Sept. 2'});hideSearch();">Tuesday 9/02</button>
				<button onClick="quickSearch({mode:'dorm', q:'Sept. 3'});hideSearch();">Wednesday 9/03</button>
			</div>
		</div>
	<div id="currentList">
		<ul data-filter="true" data-filter-placeholder="Search this list..." data-role="listview" data-transition="slidefade" id="eventList">
			<li><p style="float:left;margin-top:20px;margin-left:-15px;">Initializing event database...</p><img src="opt_spinner.gif" style="width:80px;height:60px;float:right;margin-right:10px;" /></li>
		</ul>
	</div>
	<div id="footer" style="text-align:center;font-size:12px;font-style:oblique;">
		<p>Developed by <a href="https://twitter.com/tHonscheid">Tristan Honscheid</a> and <a href="https://twitter.com/andybaretoes">Andrew Bartow</a>, with</p>
		<p>contributions by Douglas Chen, for the MIT Class of 2018.</p>
		<p>Data Copyright MIT DORMCOM. All other rights reserved.</p>
		<p>This product is not endorsed by nor affiliated with MIT.</p>
	</div>
	
</body>
</html>
