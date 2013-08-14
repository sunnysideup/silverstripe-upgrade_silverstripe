<?php

require_once('UpgradeSilverstripe.php');

if(!__FROM_COMMAND_LINE__) {
	die("web interface has been disabled by default, please remove this line to enable it");
}


if(!isset($_POST["path"]) && !__FROM_COMMAND_LINE__) {

	echo '
	<form method="post" action="index.php">
		<div>
			<label for="path">path: (e.g. /var/www/mysite.com or ..)</label>
			<input name="path" />
		</div>
		<div>
			<label for="to">to:</label>
			<select name="to" >
				<option name="">-- please select --</option>
				<option name="3.0">3.0</option>
				<option name="3.1">3.1</option>
			</select>
		</div>
		<div>
			<label for="reallyreplace">make basic changes:</label>
			<select name="reallyreplace" >
				<option name="no">no</option>
				<option name="yes">yes</option>
			</select>
		</div>
		<div>
			<label for="stickpoints">also make complex changess:</label>
			<select name="stickpoints">
				<option name="no">no</option>
				<option name="yes">yes</option>
			</select>
		</div>
		<div>
			<label for="logfilelocation">log file location</label>
			<input name="logfilelocation" value="./ss_upgrade_log.txt" />
		</div>
		<div>
			<label for="ignorefolderarray">folders to ignore (comma separated - e.g. myfolderA,myFolderB)</label>
			<input name="ignorefolderarray" value="" />
		</div>
		<input type="submit" name="DO IT NOW" />
	</form>


	';

}

//PATH
if(isset($_POST["path"])) {
	$argv[1] = $_POST["path"];
}
$argv[1] = (isset($argv[1])) ? $argv[1] : ".";


//TO
if(isset($_POST["to"])) {
	$argv[2] = $_POST["to"];
}
$argv[2] = (isset($argv[2]) && strlen($argv[2]) == 3) ? $argv[2] : "3.1";


//do basic
if(isset($_POST["reallyreplace"])) {
	$argv[3] = $_POST["reallyreplace"];
}
$argv[3] = (isset($argv[3]) && $argv[3] == "yes") ? true : false;


//do advanced
if(isset($_POST["stickpoints"])) {
	$argv[4] = $_POST["stickpoints"];
}
$argv[4] = (isset($argv[4]) && $argv[4] == "yes") ? true : false;

//log file location
if(isset($_POST["logfilelocation"])) {
	$argv[5] = $_POST["logfilelocation"];
}
if(empty($argv[5])) {
	$argv[5] = "./ss_upgrade_log.txt";
}


//ignore array
if(isset($_POST["ignorefolderarray"])) {
	$argv[6] = $_POST["ignorefolderarray"];
}
if(empty($argv[6])) {
	$argv[6] = array();
}
if($argv[6] && !is_array($argv[6])) {
	$argv[6] = explode(",", $argv[6]);
}

if(!isset($argv[1])) {
	die("you must select a valid path!");
}

if(__FROM_COMMAND_LINE__ || isset($_POST["path"])) {

	$obj = new UpgradeSilverstripe();
	$outcome = $obj->run(
		$argv[1], //path
		$argv[5], //log file location
		$argv[2], //to
		$argv[3], //basic
		$argv[4], //advanced
		$argv[6] //ignore folder array
	);
	if(!__FROM_COMMAND_LINE__) {
		echo $outcome;
	}

}

