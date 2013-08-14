<?php

require_once('UpgradeSilverstripe.php');

$pathLocation = ".";
if(isset($_GET["path"])) {
	$argv[1] = $_GET["path"];
}

if(isset($_GET["to"])) {
	$argv[2] = $_GET["to"];
}

if(isset($_GET["reallyreplace"])) {
	$argv[3] = $_GET["reallyreplace"];
}

if(isset($_GET["stickpoints"])) {
	$argv[4] = $_GET["stickpoints"];
}

if(isset($argv[1])) {
	$pathLocation = $argv[1];
}


define("__FROM_COMMAND_LINE__", PHP_SAPI === 'cli');
if(__FROM_COMMAND_LINE__) {
	$obj = new UpgradeSilverstripe();

	//***************************************************
	// START --- ADJUST AS NEEDED
	//***************************************************
	$obj->run(
		$pathLocation,
		$logFileLocation = "./ss_upgrade_log.txt",
		$to =  isset($argv[2]) && strlen($argv[2]) == 3 ? $argv[2] : "3.1",
		$doBasicReplacement = isset($argv[3]) && $argv[3] == "yes" ? true : false,
		$markStickingPoints = isset($argv[4]) && $argv[4] == "yes"? true : false,
		//Adds blog and userforms as additional folders to ignore.
		$ignoreFolderArray = array("blog", "userforms")
	);
	//***************************************************
	// END --- ADJUST AS NEEDED
	//***************************************************

}
else {
	define("__FROM_COMMAND_LINE__", false);
}
