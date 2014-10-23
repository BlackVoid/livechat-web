<?php
	header('Content-Type: text/html; charset=utf-8');
	require("class.db.php");
	require("config.php");
	
	$constring = "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'];
	$db = new db($constring, $config['username'], $config['password']);
	$showDate = isset($_GET['showDate']) && $_GET['showDate'] == '0' ? false : true;
	$showSteamID = isset($_GET['showSteamID']) && $_GET['showSteamID'] == '0' ? false : true;
	$showTeam = isset($_GET['showTeam']) && $_GET['showTeam'] == '0' ? false : true;
	$showAutoScroll = isset($_GET['showAutoScroll']) && $_GET['showAutoScroll'] == '0' ? false : true;
?>
<html>
	<head>
		<style type="text/css">
		body {
			font-family:Courier;
			color: #CCCCCC;
			background: #000000;
			padding: 5px;
		}
		.bottom-right {
			position: fixed;
			bottom: 0;
			right: 0;
		}
		</style>
	</head>
	<body>
		<div id="chat">
		<?php
			if (empty($_GET['serverID'])) {
		?>
			> ServerID missing...
		<?php
			}else{
		?>
				<script type="text/javascript">
					var lastFetch = '1';
					var busy = false;
					function httpGet(theUrl)
					{
						var xmlHttp = null;

						xmlHttp = new XMLHttpRequest();
						xmlHttp.open("GET", theUrl, false );
						xmlHttp.send( null );
						return xmlHttp.responseText;
					}
					setInterval(function(){
						if(busy == false){
							busy = true;
							var res = httpGet('ajax.php?serverID=<?=urlencode($_GET['serverID'])?>&showDate=<?=$showDate ? 1 : 0?>&showSteamID=<?=$showSteamID ? 1 : 0?>&showTeam=<?=$showTeam ? 1 : 0?>&lid=' + encodeURIComponent(lastFetch) );
							var resarray = res.split('\n');
							if(resarray[0] != -1){
								lastFetch = resarray[0];
							}
							resarray.splice(0, 1);
							var formatted = resarray.join('\n');
							var div = document.getElementById('chat');
							div.innerHTML += formatted;
							busy = false;
							if(document.getElementById('autoscroll').checked){
								window.scrollTo(0, document.body.scrollHeight);
							}
						}				
					},1000);

				</script>
		<?php
				$res = $db->run("SELECT * FROM (SELECT * FROM " . $config['table'] . " WHERE serverID = :id ORDER BY `id` DESC LIMIT 20) as p ORDER BY `id` ASC", array('id' => $_GET['serverID']));
				$lid = 1;
				foreach ($res as $key => $value) {
					$lid = $value['id'];
				}
		?>
				<script type="text/javascript">
					lastFetch = <?=$lid?>;
				</script>
		<?php
				foreach ($res as $key => $value) {
					include('chatrow.php');
				}
			}
		?>
		</div>
		<?php
			if($showAutoScroll){
		?>
			<div class="bottom-right"><input type="checkbox" id="autoscroll" checked>Auto-scroll</div>
		<?php
			}
		?>
	</body>
</html>