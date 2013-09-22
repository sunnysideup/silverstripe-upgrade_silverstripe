<?php

require_once('UpgradeSilverstripe.php');

if(!__FROM_COMMAND_LINE__) {
	$whitelist = array('127.0.0.1');
	if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
		die("web interface has been disabled by default, please remove this line to enable it");
	}
	//path to directory to scan
	$directory = "../*";
	//get all text files with a .txt extension.
	$files = glob($directory, GLOB_ONLYDIR);
	//print each file name
	$optionList = "";
	foreach($files as $file){
		$fileName = str_replace('../', '', $file);
		$optionList .= '<option value="'.$file.'">'.$fileName.'</option>';
	}
}

if(isset($_POST["pathAlternative"]) && !empty($_POST["pathAlternative"])) {
	$_POST["path"] = $_POST["pathAlternative"];
}


if(!isset($_POST["path"]) && !__FROM_COMMAND_LINE__) {

	echo '
	<style>
		.gap {margin-bottom: 20px;}
		select, input, textarea, label {display: block;  width: 95%}
		form input[type=\'radio\'] {display: inline;  width: auto; margin-left: 30px;}
	</style>
	<form method="post" action="index.php" target="iframer" style="width: 25%; float: left;">
		<div class="gap">
			<label for="path">path:</label>
			<select name="path" size="10">
				'.$optionList.'
			</select>
			<label for="pathAlternative">or:</label>
			<input type="text" name="pathAlternative" />

		</div>
		<div class="gap">
			<label for="to">to:</label>
			<input type="radio" name="to" value="3.0">3.0
			<input type="radio" name="to" value="3.1">3.1
		</div>
		<div class="gap">
			<label for="reallyreplace">make basic changes:</label>
			<input type="radio" name="reallyreplace" value="no" checked="checked" />no
			<input type="radio" name="reallyreplace" value="yes">yes
		</div>
		<div class="gap">
			<label for="stickpoints">also make complex changes:</label>
			<input type="radio" name="stickpoints" value="no" checked="checked" />no
			<input type="radio" name="stickpoints" value="yes">yes
		</div>
		<div class="gap">
			<label for="logfilelocation">log file location (defaults to upgrade_path/ss_upgrade_log)</label>
			<input name="logfilelocation" value="./ss_upgrade_log" />
		</div>
		<div class="gap">
			<label for="ignorefolderarray">folders to ignore (comma separated - e.g. myfolderA,myFolderB)</label>
			<textarea name="ignorefolderarray" value="">cms,framework</textarea>
		</div>
		<input type="submit" name="DO IT NOW" />
	</form>
	<iframe name="iframer" src="" width="70%" height="99%" style="float: right;"></iframe>';

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
	$argv[5] = $argv[1]."ss_upgrade_log";
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

