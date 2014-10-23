<?php
	header('Content-Type: text/html; charset=utf-8');
	require("class.db.php");
	require("config.php");

	$constring = "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'];
	$db = new db($constring, $config['username'], $config['password']);

	if (!empty($_GET['serverID']) and !empty($_GET['lid'])) {
		$res = $db->select($config['table'], "serverID = :id AND id > :lid ORDER BY `id` ASC", array('id' => $_GET['serverID'], 'lid' => $_GET['lid']));
		$lid = -1;
		foreach ($res as $key => $value) {
			$lid = $value['id'];
		}
		print($lid . "\n");
		foreach ($res as $key => $value) {
			include('chatrow.php');
		}
	}elseif (empty($_GET['serverID'])) {
		print("-1\n");
?>
	> ServerID missing...<br />
<?php
	}elseif (empty($_GET['lid'])) {
		print("-1\n");
?>
	> Latest ID missing...<br />
<?php
	}
?>