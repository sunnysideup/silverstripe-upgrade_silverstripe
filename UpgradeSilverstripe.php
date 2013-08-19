<?php

require_once('ReplacementData.php');
/*
	TODO:

		Add colouring:
		http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/

		**Test output on browser
*/

define("__FROM_COMMAND_LINE__", PHP_SAPI === 'cli');

class UpgradeSilverstripe {

	private $marker = "### @@@@ UPGRADE REQUIRED @@@@ ###";

	private $endMarker = "### @@@@ ########### @@@@ ###";

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
		$style = "BASIC";
		if($markStickingPoints) {
			$style = "COMPLICATED";
		}
		if($doBasicReplacement) {
			$this->addToOutput("\n#################################### \n    REAL $style REPLACEMENTS \n####################################\n ");
		}
		else {
			$this->addToOutput("\n#################################### \n    TEST ALL REPLACEMENTS ONLY \n#################################### \n ");
			$logFileLocation = null;
		}


		//get replacements
		$replacementDataObject = new ReplacementData();

		$previousTos = $replacementDataObject->getTos();
		$previousMigrationsDone = true;
		$migrationChecksDone = false;
		$this->numberOfStraightReplacements = 0;
		$this->numberOfAllReplacements = 0;
		foreach($previousTos as $previousTo) {
			$totalForOneVersion = 0;
			$this->addToOutput("\n------------------------------------\nUpgrade to Silverstripe: $previousTo \n------------------------------------");
			if($to == $previousTo) {
				$migrationChecksDone = true;
				if(!$previousMigrationsDone) {
					die("\nError: Your code is not ready to migrate to $to (see above)");
				}
			}
			$numberToAdd = $this->numberOfReplacements($pathLocation, $previousTo,$ignoreFolderArray, true);
			$totalForOneVersion += $numberToAdd;
			$this->numberOfStraightReplacements += $numberToAdd;
			if($this->numberOfStraightReplacements == 0) {
				$this->addToOutput("\n[OK] migration to $previousTo for basic replacements completed.");
			}
			else {
				$this->addToOutput( "\n[TO DO] migration to $previousTo for basic replacements NOT completed yet ($numberToAdd items to do).");
				$previousMigrationsDone = false;
			}
			$numberToAdd = $this->numberOfReplacements($pathLocation, $previousTo,$ignoreFolderArray, false);
			$totalForOneVersion += $numberToAdd;
			$this->numberOfAllReplacements += $numberToAdd;
			if($this->numberOfAllReplacements == 0) {
				$this->addToOutput("\n[OK] migration to $previousTo for complicated items completed.");
			}
			else {
				$this->addToOutput( "\n[TO DO] migration to $previousTo for complicated items NOT completed yet ($numberToAdd items to do).");
			}
			$this->addToOutput("\n------------------------------------\n$totalForOneVersion items to do for $previousTo \n------------------------------------\n");
			$totalForOneVersion = 0;
			$this->addToOutput( "\n\n");
			if($migrationChecksDone) {
				break;
			}
		}
		$textSearchMachine = new TextSearch();

		//set basics
		$textSearchMachine->addIgnoreFolderArray($ignoreFolderArray); //setting extensions to search files within
		$textSearchMachine->setBasePath($pathLocation);
		if($logFileLocation) {
			$textSearchMachine->setLogFileLocation($logFileLocation);
		}
		$array = $replacementDataObject->getReplacementArrays($to);
		foreach($array as $extension => $extensionArray) {
			$this->addToOutput("\n\n\n\n++++++++++++++++++++++++++++++++++++ \n    CHECKING $extension FILES \n++++++++++++++++++++++++++++++++++++ \n\n\n\n");
			$textSearchMachine->setExtensions(array($extension)); //setting extensions to search files within
			foreach($extensionArray as $replaceArray) {
				$find = $replaceArray[0];
				//$replace = $replaceArray[1]; unset($replaceArray[1]);
				//$fullReplacement = (isset($replaceArray[2]) ? "/* ".$replaceArray[2]." */\n" : "").$replaceArray[1];
				$fullReplacement = "";
				$isStraightReplace = true;
				if(isset($replaceArray[2])) {// Has comment
					$isStraightReplace = false;
					$fullReplacement = "/*\n".$this->marker."\nFIND: ".$replaceArray[0]."\nNOTE: ".$replaceArray[2]." \n".$this->endMarker."\n*/".$replaceArray[1];
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
					//$textSearchMachine->writeLogToFile($logFileLocation);
				}
				else {
					$textSearchMachine->setSearchKey($find);
					$textSearchMachine->setFutureReplacementKey($codeReplacement);
					$textSearchMachine->startSearching();//starting search
					//output - only write to log for real replacements!
				}
				//$textSearchMachine->showLog();//showing log
			}
			$replacements = $textSearchMachine->showFormattedSearchTotals(false);
			if($replacements) {
				$this->addToOutput($textSearchMachine->getOutput());
			}
			else {
				//flush output anyway!
				$textSearchMachine->getOutput();
				$this->addToOutput("\n No replacements for  $extension \n------------------------------------\n");
			}
		}

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
		$total = 0;
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
				$textSearchMachine->setSearchKey($find);
				$textSearchMachine->setFutureReplacementKey("TEST ONLY");
				$textSearchMachine->startSearching();//starting search
			}
			//IMPORTANT!
			$total += $textSearchMachine->showFormattedSearchTotals(true);
		}
		//flush output anyway!
		$textSearchMachine->getOutput();
		return $total;
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

	private $basePath                  = '.';

	private $logFileLocation           = '';

	private $defaultIgnoreFolderArray  = array("cms", "assets", "sapphire", "framework", "upgrade_silverstripe", ".svn", ".git");

	private $ignoreFolderArray         = array();

	private $extensions                = array("php", "ss", "yml", "yaml", "json", "js");

	private $findAllExts               = 0;

	private $searchKey                 = '';

	private $replacementKey            = '';

	private $futureReplacementKey      = '';

	private $isReplacingEnabled        = 0;

	private $caseSensitive             = 0;

	private $logString                 = ''; //details of one search

	private $errorText                 = ''; //details of one search

	private $totalFound                = 0; //total matches in one search

	private $output                    = ''; //buffer of output, until it is retrieved

	private static $search_key_totals  = array();

	private static $folder_totals      = array();

	private static $total_total        = 0;

	public function __construct() {
		$this->ignoreFolderArray = $this->defaultIgnoreFolderArray;
	}



	//================================================
	// Setters Before Run
	//================================================


	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function setIgnoreFolderArray($ignoreFolderArray = array()) {
		$this->ignoreFolderArray = $ignoreFolderArray;
		$this->resetFileCache();
	}

	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function addIgnoreFolderArray($ignoreFolderArray = array()) {
		$this->ignoreFolderArray = $ignoreFolderArray;
		$this->ignoreFolderArray = array_unique(array_merge($this->ignoreFolderArray, $this->defaultIgnoreFolderArray));
		$this->resetFileCache();
	}

	/**
	 * remove a root folder that is avoided by default
	 * @param String $nameOfFolder
	 */
	public function unsetIgnoreFolderArray($nameOfFolder) {
		unset($this->ignoreFolderArray[$nameOfFolder]);
		$this->resetFileCache();
	}


	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function setBasePath($pathLocation) {
		$this->basePath = $pathLocation;
		$this->resetFileCache();
	}

	/**
	 * Sets location for the log file
	 * logs are only written for real replacements
	 *   @param String
	 *   @return none
	 */
	public function setLogFileLocation($logFileLocation) {
		$this->logFileLocation = $logFileLocation;
	}

	/**
	 *   Sets extensions to look
	 *   @param Array extensions
	 */
	public function setExtensions($extensions = array()) {
		$this->extensions = $extensions;
		if(count($this->extensions)){
			 $this->findAllExts = 0; //not all extensions
		}
		$this->resetFileCache();
	}


	//================================================
	// Setters Before Every Search
	//================================================


	/**
	 * Sets search key and case sensitivity
	 * @param String $searchKey,
	 * @param Boolean $caseSensitivity
	 */
	public function setSearchKey($searchKey, $caseSensitive = 0) {
		$this->searchKey =      $searchKey;
		$this->caseSensitive =  $caseSensitive;
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


	//================================================
	// Get FINAL output
	//================================================


	/**
	 * returns full output
	 * and clears it.
	 * @return string
	 */
	public function getOutput(){
		$output = $this->output;
		$this->output = "";
		return $output;
	}


	/**
	 * returns the TOTAL TOTAL number of
	 * found replacements
	 */
	public function getTotalTotalSearches() {
		return self::$total_total;
	}



	//================================================
	// Write to log while doing the searches
	//================================================

	/**
	 * should be run at the end of an extension.
	 */
	public function showFormattedSearchTotals($returnTotalFoundOnly = false) {
		$totalSearches = 0;
		foreach(self::$search_key_totals as $searchKey => $total) {
			$totalSearches += $total;
		}
		if($returnTotalFoundOnly) {
			//do nothing
		}
		else {
			$flatArray = $this->getFlatFileArray();
			$this->addToOutput("\n------------------------------------\nFiles Searched\n------------------------------------\n");
			foreach($flatArray as $file) {
				$strippedFile = str_replace($this->basePath, "", $file);
				$this->addToOutput($strippedFile."\n");
			}
			$folderSimpleTotals = array();
			$realBase = realpath($this->basePath);
			$this->addToOutput("\n------------------------------------\nSummary: by search key\n------------------------------------\n");
			arsort(self::$search_key_totals);
			foreach(self::$search_key_totals as $searchKey => $total) {
				$this->addToOutput(sprintf("%d:\t %s\n", $total, $searchKey));
			}
			$this->addToOutput("\n------------------------------------\nSummary: by directory\n------------------------------------\n");
			arsort(self::$folder_totals);
			foreach(self::$folder_totals as $folder => $total) {
				$path = str_replace($realBase, "", realpath($folder));
				$pathArr = explode("/", $path);
				$folderName = $pathArr[1]."/";
				if(!isset($folderSimpleTotals[$folderName])) {
					$folderSimpleTotals[$folderName] = 0;
				}
				$folderSimpleTotals[$folderName] += $total;
				$strippedFolder = str_replace($this->basePath, "", $folder);
				$this->addToOutput(sprintf("%d:\t %s\n", $total, $strippedFolder));
			}
			$strippedRealBase = "/";
			$this->addToOutput(sprintf("\n------------------------------------\nSummary: by root directory (%s)\n------------------------------------\n", $strippedRealBase));
			arsort($folderSimpleTotals);
			foreach($folderSimpleTotals as $folder => $total) {
				$strippedFolder = str_replace($this->basePath, "", $folder);
				$this->addToOutput(sprintf("%d:\t %s\n", $total, $strippedFolder));
			}
			$this->addToOutput( sprintf("\n------------------------------------\nTotal replacements: %d\n------------------------------------\n", $totalSearches));
		}
		//add to total total
		self::$total_total += $totalSearches;
		//return total
		return $totalSearches;
	}


	//================================================
	// Doers
	//================================================


	/**
	 * Searches all the files and creates the logs
	 * @param $path to search
	 * @return none
	 */
	public function startSearching(){
		$flatArray = $this->getFlatFileArray();
		foreach($flatArray as $location) {
			$this->searchFileData("$location");
		}
		if($this->totalFound) {
			$this->addToOutput("".$this->totalFound." matches for: ".$this->logString);
		}
		if($this->errorText!= '' ) {
			$this->addToOutput("\t Error-----".$this->errorText);
		}
		$this->logString = "";
		$this->errorText = "";
		$this->totalFound = 0;
	}

	private function resetFileCache(){
		self::$file_array = null;
		self::$file_array = array();
		self::$flat_file_array = null;
		self::$flat_file_array = array();
		//cleanup other data
		self::$search_key_totals = null;
		self::$search_key_totals = array();
		self::$folder_totals = null;
		self::$folder_totals = array();
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
	 * Flattened array of files.
	 * @var Array
	 */
	private static $flat_file_array = array();

	private function getFlatFileArray(){
		if(!count(self::$flat_file_array)) {
			$array = $this->getFileArray($this->basePath, false);
			$multiDimensionalArray = $this->getFileArray($this->basePath,false);
			//flatten it!
			self::$flat_file_array = new RecursiveIteratorIterator(new RecursiveArrayIterator($multiDimensionalArray));
		}
		return self::$flat_file_array;
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
					$foundStr = "-- Replaced in $found places";
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
			if(!isset(self::$search_key_totals[$this->searchKey])) {
				self::$search_key_totals[$this->searchKey] = 0;
			}
			self::$search_key_totals[$this->searchKey] += $found;

			if(!isset(self::$folder_totals[dirname($file)])) {
				self::$folder_totals[dirname($file)] = 0;
			}
			self::$folder_totals[dirname($file)] += $found;
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
		$file = basename($file);
		$this->logString .= "   $matchStr IN $file\n";
	}

	/**
	 *
	 * @param String $text
	 */
	private function addToOutput($output) {
		if($this->logFileLocation && $this->isReplacingEnabled) {
			$handle = fopen($this->logFileLocation, "a");
			fwrite($handle, $output);
			fclose($handle);
		}
		$this->output .= $output;
	}

}
