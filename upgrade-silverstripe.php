<?php

require_once('ReplacementData.php');
/*
	TODO:

		Add colouring:
		http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

		Add separate sections for automatic replacements and manual replacements in summary.

		Count number of replacements per folder

		**Test output on browser

		** make function that converts to html output to command line output (or create html and open)

*/


$pathLocation = ".";
if(isset($_GET["path"])) {
	$argv[1] = $_GET["path"];
}

if(isset($_GET["reallyreplace"])) {
	$argv[2] = $_GET["reallyreplace"];
}

if(isset($_GET["stickpoints"])) {
	$argv[3] = $_GET["stickpoints"];
}

if(isset($argv[1])) {
	$pathLocation = $argv[1];
}


if(isset($argv[0])) {
	define("__FROM_COMMAND_LINE__", true);
	$obj = new UpgradeSilverstripe();

	//***************************************************
	// START --- ADJUST AS NEEDED
	//***************************************************
	$obj->run(
		$pathLocation,
		$logFileLocation = "./ss_upgrade_log.txt",
		$to = "3.1",
		$doBasicReplacement = isset($argv[2]) && $argv[2] == "yes" ? true : false,
		$markStickingPoints = isset($argv[3]) && $argv[3] == "yes"? true : false,
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

class UpgradeSilverstripe {

	private $marker = " ### UPGRADE_REQUIRED  ";

	private $endMarker = "###";

	private $output = "";

	private $numberOfStraightReplacements = 0;
		public function getNumberOfStraightReplacements() {return intval($this->numberOfStraightReplacements);}

	private $numberOfAllReplacements = 0;
		public function getNumberOfAllReplacements() {return intval($this->numberOfAllReplacements);}

	private $checkReplacementIssues = false;
		public function setCheckReplacementIssues($b) {$this->checkReplacementIssues = $b;}


	/**
	 *
	 * @param String $pathLocation - enter dot for anything in current directory.
	 * @param String $logFileLocation - where should the log file be saved. This file contains all the details about actual changes made.
	 * @param String $to - if you set this to, for example 3.0 then the code will be upgraded from 2.4 to 3.0.
	 * @param Boolean $doBasicReplacement - If set to false to show proposed changes on screen. If set to true, basic replacements (i.e. straight forward replace A with B scenarios will be made)
	 * @param Boolean $markStickingPoints - If set to false nothing happens, if set to true  any code that need changing manually will be marked in the code itself.
	 * @param Array $ignoreFolderArray - a list of folders that should not be searched (and replaced) - folders that are automatically ignore are: CMS, SAPPHIRE, FRAMEWORK (all in lowercase)
	 * outputs to screen and/or to file
	 */
	public function run(
		$pathLocation = ".",
		$logFileLocation = "./ss_upgrade_log.txt",
		$to = "3.0",
		$doBasicReplacement = false,
		$markStickingPoints = false,
		$ignoreFolderArray = array()
	) {
		if(!file_exists($pathLocation)) {
			$this->addToOutput("\n\n");
			user_error("ERROR: could not find specified path: ".$pathLocation);
			$this->addToOutput("\n\n");
			$this->addToOutput("---END ---\n");
		}
		if($this->checkReplacementIssues) {
			$this->checkReplacementIssues();
			$this->addToOutput("---END ---\n");
		}
		//basic checks
		if(!$doBasicReplacement && $markStickingPoints) {
			user_error("You have to set doBasicReplacement = TRUE before you can set markStickingPoints = TRUE");
		}
		if(!is_array($ignoreFolderArray)) {
			user_error("the ignoreFolderArray param should be an array");
		}

		//get replacements
		$replacementData = new ReplacementData();


		$previousTos = $replacementData->getTos();
		$previousMigrationsDone = true;
		$migrationChecksDone = false;
		$this->numberOfStraightReplacements = 0;
		$this->numberOfAllReplacements = 0;
		foreach($previousTos as $previousTo) {
			$this->addToOutput("\n------------------------------------\nStart Checking for upgrade to Silverstripe: $previousTo \n------------------------------------");
			if($to == $previousTo) {
				$migrationChecksDone = true;
				if(!$previousMigrationsDone) {
					die("\nError: Your code is not ready to migrate to $to (see above)");
				}
			}
			$this->numberOfStraightReplacements += $this->numberOfReplacements($pathLocation, $previousTo,$ignoreFolderArray, true);
			if($this->numberOfStraightReplacements == 0) {
				$this->addToOutput("\n[OK] migration to $previousTo for basic replacements completed.");
			}
			else {
				$this->addToOutput( "\n[TO DO] migration to $previousTo for basic replacements NOT completed yet.");
				$previousMigrationsDone = false;
			}
			$this->numberOfAllReplacements += $this->numberOfReplacements($pathLocation, $previousTo,$ignoreFolderArray, false);
			if($this->numberOfAllReplacements == 0) {
				$this->addToOutput("\n[OK] migration to $previousTo for complicated items completed.");
			}
			else {
				$this->addToOutput( "\n[TO DO] migration to $previousTo for complicated items NOT completed yet.");
				$previousMigrationsDone = false;
			}
			$this->addToOutput("\n------------------------------------\nEnd $previousTo \n------------------------------------\n");
			$this->addToOutput( "\n\n");
			if($migrationChecksDone) {
				break;
			}
		}
		$textSearchMachine = new TextSearch();

		//set basics
		$textSearchMachine->addIgnoreFolderArray($ignoreFolderArray); //setting extensions to search files within
		$textSearchMachine->setBasePath($pathLocation);
		if(__FROM_COMMAND_LINE__) {
			$this->addToOutput("\n------------------------------------\nStart List of Files: \n------------------------------------\n");
			$textSearchMachine->showFilesToSearch();
		}

		$array = $replacementData->getReplacementArrays($to);
		foreach($array as $extension => $extensionArray) {
			$textSearchMachine->setExtensions(array($extension)); //setting extensions to search files within
			foreach($extensionArray as $replaceArray) {
				$find = $replaceArray[0];
				//$replace = $replaceArray[1]; unset($replaceArray[1]);
				//$fullReplacement = (isset($replaceArray[2]) ? "/* ".$replaceArray[2]." */\n" : "").$replaceArray[1];
				$fullReplacement = "";
				$isStraightReplace = true;
				if(isset($replaceArray[2])) {// Has comment
					$isStraightReplace = false;
					$fullReplacement = $this->marker."\n/* Replaced ".$replaceArray[0]."\nComment: ".$replaceArray[2]." \n".$this->endMarker."*/".$replaceArray[1];
				}
				else { // Straight replace
					$fullReplacement = $replaceArray[1];
				}
				$comment = isset($replaceArray[2]) ? $replaceArray[2] : "";
				$codeReplacement = $replaceArray[1];
				if(!$find) {
					user_error("no replace is specified, replace is: $replace");
				}
				if(!$fullReplacement) {
					user_error("no replace is specified, find is: $find");
				}
				if($doBasicReplacement) {
					if(!$markStickingPoints) {
						if(strpos($this->marker, $fullReplacement) !== false) {
							continue;
						}
					}
					$textSearchMachine->setSearchKey($find);
					$textSearchMachine->setReplacementKey($fullReplacement);
					$textSearchMachine->startSearching();//starting search
					//output - only write to log for real replacements!
					$textSearchMachine->writeLogToFile($logFileLocation);
				}
				else {
					$textSearchMachine->setSearchKey($find);
					$textSearchMachine->setFutureReplacementKey($codeReplacement);
					$textSearchMachine->startSearching();//starting search
					//output - only write to log for real replacements!
				}
				//$textSearchMachine->showLog();//showing log
				$textSearchMachine->clearSearchCache();
			}
		}
		$textSearchMachine->formatSearchTotalsFormatted();
		$this->addToOutput($textSearchMachine->getOutput());
		return $this->printItNow();
	}

	/**
	 *
	 * @var Int
	 */
	private function numberOfReplacements(
		$pathLocation = ".",
		$to = "3.0",
		$ignoreFolderArray = array(),
		$simpleOnly = true
	) {
		//basic checks
		$textSearchMachine = new TextSearch();

		//get replacements
		$replacementData = new ReplacementData();
		$array = $replacementData->getReplacementArrays($to);

		//set basics
		$textSearchMachine->addIgnoreFolderArray($ignoreFolderArray); //setting extensions to search files within
		$textSearchMachine->setBasePath($pathLocation);
		foreach($array as $extension => $extensionArray) {
			$textSearchMachine->setExtensions(array($extension)); //setting extensions to search files within
			foreach($extensionArray as $replaceArray) {
				$find = $replaceArray[0];
				if(isset($replaceArray[2]) && $simpleOnly) {// Has comment
					continue;
				}
				elseif(!isset($replaceArray[2]) && !$simpleOnly) {
					continue;
				}
				if(!$find) {
					user_error("no replace is specified, replace is: $replace");
				}
				$textSearchMachine->setSearchKey($find);
				$textSearchMachine->setFutureReplacementKey("TEST ONLY");
				$textSearchMachine->startSearching();//starting search
				$textSearchMachine->clearSearchCache();
			}
		}
		$number = $textSearchMachine->totalSearches();
		return $number;
	}

	/**
	 * 1. check that one find is not used twice:
	 * find can be found 2x
	 *
	 */
	private function checkReplacementIssues(){
		$r = new ReplacementData();
		$arr = $r->getReplacementArrays(null);
		$arrTos = array();
		$arrLanguages = $r->getLanguages();
		$fullFindArray = $r->getFlatFindArray();
		$fullReplaceArray = $r->getFlatReplacedArray();

		//1, check that one find may not stop another replacement.
		foreach($arrLanguages as $language) {
			if(!isset($fullFindArray[$language])) {
				continue;
			}
			unset($keyOuterDoneSoFar);
			$keyOuterDoneSoFar = array();
			foreach($fullFindArray[$language] as $keyOuter => $findStringOuter) {
				$keyOuterDoneSoFar[$keyOuter] = true;
				foreach($fullFindArray[$language] as $keyInner => $findStringInner) {
					if(!isset($keyOuterDoneSoFar[$keyInner])) {
						if($keyOuter != $keyInner) {
							$findStringOuterReplaced = str_replace($findStringInner, "...", $findStringOuter);
							if($findStringOuter == $findStringInner || $findStringOuterReplaced != $findStringOuter) {
								$this->addToOutput( "
ERROR in $language: \t\t we are trying to find the same thing twice (A and B)
---- A: ($keyOuter): \t\t $findStringOuter
---- B: ($keyInner): \t\t $findStringInner");
							}
						}
					}
				}
			}
		}
		$this->addToOutput( "\n");

		//2. check that a replacement is not mentioned before the it is being replaced
		foreach($arrLanguages as $language) {
			if(!isset($fullReplaceArray[$language])) {
				continue;
			}
			unset($keyOuterDoneSoFar);
			$keyOuterDoneSoFar = array();
			foreach($fullReplaceArray[$language] as $keyOuter => $findStringOuter) {
				$keyOuterDoneSoFar[$keyOuter] = true;
				foreach($fullFindArray[$language] as $keyInner => $findStringInner) {
					if(isset($keyOuterDoneSoFar[$keyInner])) {
						if($keyOuter != $keyInner) {
							$findStringOuterReplaced = str_replace($findStringInner, "...", $findStringOuter);
							if($findStringOuter == $findStringInner || $findStringOuterReplaced != $findStringOuter) {
								$this->addToOutput( "
ERROR in $language: \t\t there is a replacement (A) that was earlier tried to be found (B).
---- A: ($keyOuter): \t\t $findStringOuter
---- B: ($keyInner): \t\t $findStringInner");
							}
						}
					}
				}
			}
		}
		$this->addToOutput( "\n" );
	}

	private function addToOutput($text){
		$this->output .= $text;
	}

	private function printItNow(){
		$text = $this->output;
		$this->output = "";
		if(__FROM_COMMAND_LINE__) {
			echo $text."\n\n\n";
		}
		else {
			return "<pre>".$text."</pre>";
		}
	}

}

/**
* Class : TextSearch
*
* @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
*             Moderator, phpResource Group(http://groups.yahoo.com/group/phpresource/)
*             URL: http://rupom.wordpress.com
*
* HEAVILY MODIFIED BY SUNNY SIDE UP
*
* @version :  1.0
* Date     :  06/25/2006
* Purpose  :  Searching and replacing text within files of specified path
*/

class TextSearch {

	private $basePath                = '.';

	private $defIgnoreFolderArray    = array("cms", "sapphire", "framework", "upgrade_silverstripe", ".svn", ".git");

	private $ignoreFolderArray       = array();

	private $extensions              = array("php", "ss", "yml", "yaml", "json", "js");

	private $findAllExts             = 0; //by default all extensions

	private $searchKey               = '';

	private $replacementKey          = '';

	private $futureReplacementKey    = '';

	private $isReplacingEnabled      = 0;

	private $caseSensitive           = 0; //by default case sensitivity is OFF

	private $logString               = '';

	private $errorText               = '';

	private $output                  = '';

	private $totalFound              = 0; //total matches

	private $avoidByDefault          = array();

	private static $searchKeyTotals  = array();

	private static $folderTotals     = array();

	public function __construct() {
		$this->ignoreFolderArray = $this->defIgnoreFolderArray;
	}


	public function showFilesToSearch(){
		$multiDimensionalArray = $this->getFileArray($this->basePath,false);
		//flatten it!
		$flatArray = new RecursiveIteratorIterator(new RecursiveArrayIterator($multiDimensionalArray));
		foreach($flatArray as $file) {
			$this->addToOutput($file."\n\n");
		}
	}

	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function setIgnoreFolderArray($ignoreFolderArray = array()) {
		$this->ignoreFolderArray = $ignoreFolderArray;
	}

	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function addIgnoreFolderArray($ignoreFolderArray = array()) {
		$this->ignoreFolderArray = $ignoreFolderArray;
		$this->ignoreFolderArray = array_unique(array_merge($this->ignoreFolderArray, $this->defIgnoreFolderArray));
	}

	/**
	 * remove a root folder that is avoided by default
	 * @param String $nameOfFolder
	 */
	public function unsetIgnoreFolderArray($nameOfFolder) {
		unset($this->ignoreFolderArray[$nameOfFolder]);
	}


	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function setBasePath($pathLocation) {
		$this->basePath = $pathLocation;
	}

	/**
	 *   Sets extensions to look
	 *   @param Array extensions
	 */
	public function setExtensions($extensions = array()) {
		$this->extensions = $extensions;
		if(sizeof($this->extensions)){
			 $this->findAllExts = 0; //not all extensions
		}
	}

	/**
	 * Sets search key and case sensitivity
	 * @param String $searchKey,
	 * @param Boolean $caseSensitivity
	 */
	public function setSearchKey($searchKey, $caseSensitive = 0) {
		$this->searchKey = $searchKey;
		$this->caseSensitive = ($caseSensitive ? 1 : 0);
	}

	/**
	 *   Sets key to replace searchKey with
	 *   @param String $replacementKey
	 */
	public function setReplacementKey($replacementKey){
		$this->replacementKey     = $replacementKey;
		$this->isReplacingEnabled = 1;
	}

	/**
	 *   Sets key to replace searchKey with BUT only hypothetical
	 * (no replacement takes place!)
	 *   @param String $replacementKey
	 */
	public function setFutureReplacementKey($replacementKey){
		$this->futureReplacementKey = $replacementKey;
		$this->isReplacingEnabled   = 0;
	}

	/**
	 * Wrapper function around function findDirFiles()
	 * @param $path to search
	 * @return none
	 */
	public function startSearching(){
		$array = $this->getFileArray($this->basePath, false);
		$multiDimensionalArray = $this->getFileArray($this->basePath,false);
		//flatten it!
		$flatArray = new RecursiveIteratorIterator(new RecursiveArrayIterator($multiDimensionalArray));
		foreach($flatArray as $location) {
			$this->searchFileData("$location");
		}
	}

	/**
	 * Shows Log
	 * @return none
	 */
	public function showLog() {
		if($this->totalFound) {
			$this->addToOutput("------ ".$this->totalFound." matches for: ".$this->logString);
		}
		if($this->errorText!='') {
			$this->addToOutput("------Error-----".$this->errorText);
		}
	}

	/**
	 * Writes log to file
	 * @param String log filename
	 */
	public function writeLogToFile($file) {
		$fp = fopen($file, "a") OR user_error("Can not open file <b>$file</b>");
		fwrite($fp, "\n\n================================================");
		fwrite($fp, $this->logString);
		fwrite($fp, "\n------ Total ".$this->totalFound." Matches Found `".$this->searchKey."` -----\n");
		if($this->errorText){
			fwrite($fp, "\n------Error-----------Error-----------Error-----------Error-----\n");
			fwrite($fp, $this->errorText);
			fwrite($fp, "\n------Error-----------Error-----------Error-----------Error-----\n");
		}
		fclose($fp);
	}

	/**
	 * returns full output
	 * and clears it.
	 * @return string
	 */
	public function getOutput(){
		$text = $this->output;
		$this->output = "";
		return $text;
	}

	/**
	 *
	 * clears cache data
	 *
	 */
	public function clearSearchCache() {
		return ;
		$this->logString = '';
		$this->errorText = '';
		$this->totalFound = 0;
	}

	public function getSearchTotals() {
		return self::$searchKeyTotals;
	}

	public function formatSearchTotalsFormatted() {

		$folderSimpleTotals = array();
		$realBase = realpath($this->basePath);
		$this->addToOutput("------------------------------------\nSummary: by search key\n------------------------------------\n");
		arsort(self::$searchKeyTotals);
		foreach($this->getSearchTotals() as $searchKey => $total) {
			$this->addToOutput(sprintf("%d:\t %s\n", $total, $searchKey));
		}
		$this->addToOutput( sprintf("------------------------------------\nTotal replacements: %d\n------------------------------------\n\n", $this->totalSearches()));

		if(__FROM_COMMAND_LINE__) {
			$this->addToOutput("------------------------------------\nSummary: by directory\n------------------------------------\n");
			arsort(self::$folderTotals);
			foreach(self::$folderTotals as $folder => $total) {
				$path = str_replace($realBase, "", realpath($folder));
				$pathArr = explode("/", $path);
				$folderName = $pathArr[1]."/";
				if(!isset($folderSimpleTotals[$folderName])) {
					$folderSimpleTotals[$folderName] = 0;
				}
				$folderSimpleTotals[$folderName] += $total;
				$this->addToOutput(sprintf("%d:\t %s\n", $total, $folder));
			}
			$this->addToOutput(sprintf("\n------------------------------------\nSummary: by root directory (%s)\n------------------------------------\n", $realBase));
			arsort($folderSimpleTotals);
			foreach($folderSimpleTotals as $folder => $total) {
				$this->addToOutput(sprintf("%d:\t %s\n", $total, $folder));
			}
		}
	}

	public function totalSearches() {
		$completeTotal = 0;
		foreach($this->getSearchTotals() as $searchKey => $total) {
			$completeTotal += $total;
		}
		return $completeTotal;
	}


	/**
	 * array of all the files we are searching
	 * @var array
	 */
	private static $file_array = array();

	/**
	 * loads all the applicable files
	 * @param String $path (e.g. "." or "/var/www/mysite.co.nz")
	 * @param Boolean $innerLoop - is the method calling itself???
	 *
	 *
	 */
	private function getFileArray($path, $innerLoop = false){
		$key = str_replace(array("/"), "__", $path);
		if($innerLoop || !count(self::$file_array)) {
			$dir = opendir ($path);
			while ($file = readdir ($dir)) {
				if (($file == ".") || ($file == "..") || ( __FILE__ == "$path/$file" ) || ($path == "." && basename(__FILE__) == $file)) {
					continue;
				}
				//ignore hidden files and folders
				if(substr($file, 0, 1) == ".") {
					continue;
				}
				//ignore folders with _manifest_exclude in them!
				if($file == "_manifest_exclude") {
					$this->ignoreFolderArray[] = $path;
					unset (self::$file_array[$key]);
					break;
				}
				if (filetype ("$path/$file") == "dir") {
					if(
						(in_array($file,$this->ignoreFolderArray) && ($path == "."|| $path == $this->basePath)) ||
						(in_array($path, $this->ignoreFolderArray))) {
						continue;
					}
					$this->getFileArray("$path/$file", $innerLoop = true); //recursive traversing here
				}
				elseif($this->matchedExtension($file)) { //checks extension if we need to search this file
					if(filesize("$path/$file")) {
						self::$file_array[$key][] = "$path/$file"; //search file data
					}
				}
			} //End of while
			closedir($dir);
		}
		return self::$file_array;
	}

	/**
	 * Finds extension of a file
	 * @param filename
	 * @return file extension
	 */
	private function findExtension($file) {
		$fileArray = explode(".", $file);
		return array_pop($fileArray);
	}//End of function

	/**
	 * Checks if a file extension is one of the extensions we are going to search
	 * @param String $filename
	 * @return Boolean
	 */
	private function matchedExtension($file){
		if($this->findAllExts){
			return true;
		}
		elseif(sizeof(array_keys($this->extensions, $this->findExtension($file)))==1){
			return true;
		}
		return false;
	}

	/**
	 * THE KEY METHOD!
	 * Searches data, replaces (if enabled) with given key, prepares log
	 * @param String $file - e.g. /var/www/mysite.co.nz/mysite/code/Page.php
	 */
	private function searchFileData($file) {
		$searchKey  = preg_quote($this->searchKey, '/');
		if($this->caseSensitive){
			$pattern    = "/$searchKey/U";
		}
		else{
			$pattern    = "/$searchKey/Ui";
		}
		$subject = file_get_contents($file);
		$found = 0;
		$found = preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);
		$this->totalFound +=$found;
		if($found){
			$foundStr = " x $found";
			if($this->isReplacingEnabled) {
				if($this->replacementKey){
					$outputStr = preg_replace($pattern, $this->replacementKey, $subject);
					$foundStr = "Replaced in $found places";
					$this->writeToFile($file, $outputStr);
					$this->appendToLog($file, $foundStr, $this->replacementKey);
				}
				else {
					$this->errorText .= "********** ERROR: Replacement Text is not defined\n";
					$this->appendToLog($file, "********** ERROR: Replacement Text is not defined", $this->replacementKey);
				}
			}
			else{
				if($this->futureReplacementKey) {
					$this->appendToLog($file, $foundStr, $this->futureReplacementKey);
				}
				else {
					$this->errorText .= "********** ERROR: FUTURE Replacement Text is not defined\n";
					$this->appendToLog($file, "********** ERROR: FUTURE Replacement Text is not defined");
				}
			}
			if(!isset(self::$searchKeyTotals[$this->searchKey])) {
				self::$searchKeyTotals[$this->searchKey] = 0;
			}
			self::$searchKeyTotals[$this->searchKey] += $found;

			if(!isset(self::$folderTotals[dirname($file)])) {
						self::$folderTotals[dirname($file)] = 0;
					}
					self::$folderTotals[dirname($file)] += $found;

		}
		else{
			//$this->appendToLog($file, "No matching Found", $this->replacementKey);
		}
	}

	/**
	 * Writes new data (after the replacement) to file
	 * @param $file, $data
	 * @return none
	 */
	private function writeToFile($file, $data) {
		if(is_writable($file)){
			 $fp = fopen($file, "w");
			 fwrite($fp, $data);
			 fclose($fp);
		}
		else{
			 $this->errorText .= "********** ERROR: Can not replace text. File $file is not writable. \nPlease make it writable\n";
		}
	}

/**
 * Appends log data to previous log data
 * @param filename, match string, replacement key if any
 * @return none
 */
 private function appendToLog($file, $matchStr, $replacementKey = null){
		if($this->logString == ''){
			 $this->logString = "'".$this->searchKey."'\n";
		}
		$this->logString .= "------ ------ $matchStr In $file ... '$replacementKey'\n";
	}

	private function addToOutput($text) {
		$this->output .= $text;
	}

}
