<?php


############### WHAT ARE WE UPGRADING ? #####################
$obj = new UpgradeSilverstripe();
$obj->run(
	$pathLocation = ".",
	$logFileLocation = "./ss_upgrade_log.txt",
	$to = "3.0",
	$doBasicReplacement = false,
	$markStickingPoints = false,
	$ignoreFolderArray = array("sapphire", "framework", "cms", ".svn")
);
###################################


class UpgradeSilverstripe {

	/**
	 *
	 * @param String $pathLocation - enter dot for anything in current directory.
	 * @param String $logFileLocation - where should the log file be saved. This file contains all the details about actual changes made.
	 * @param String $to - if you set this to, for example 3.0 then the code will be upgraded from 2.4 to 3.0.
	 * @param Boolean $doBasicReplacement - If set to false to show proposed changes on screen. If set to true, basic replacements (i.e. straight forward replace A with B scenarios will be made)
	 * @param Boolean $markStickingPoints - If set to false nothing happens, if set to true and $doBasicReplacement is set to true as well, any code that need changing manually will be marked in the code itself.
	 * @param Array $ignoreFolderArray - a list of folders that should not be searched (and replaced) - folders that are automatically ignore are: CMS, SAPPHIRE, FRAMEWORK (all in lowercase)
	 *
	 */
	public function run(
		$pathLocation = "code",
		$logFileLocation = "./ss_upgrade_log.txt",
		$to = "3.0",
		$doBasicReplacement = false,
		$markStickingPoints = false,
		$ignoreFolderArray = array()
	) {
		//basic checks
		if(!$doBasicReplacement && $markStickingPoints) {
			user_error("You have to set doBasicReplacement = TRUE before you can set markStickingPoints = TRUE");
		}
		if(!is_array($ignoreFolderArray)) {
			user_error("the ignoreFolderArray param should be an array");
		}
		$ignoreFolderArray += array("cms", "sapphire", "framework", "upgrade_silverstripe");

		$textSearchMachine = new TextSearch();

		//get replacements
		$array = $this->getReplacementArrays($to);

		//set basics
		$textSearchMachine->setIgnoreFolderArray($ignoreFolderArray); //setting extensions to search files within

		$textSearchMachine->startSearching($pathLocation);//starting search

		foreach($array as $extension => $extensionArray) {
			$textSearchMachine->setExtensions(array($extension)); //setting extensions to search files within
			foreach($extensionArray as $replaceArray) {
				$find = $replaceArray[0]; unset ($replaceArray[0]);
				$replace = $replaceArray[1]; unset($replaceArray[1]);
				if(!$find) {
					user_error("no replace is specified, replace is: $replace");
				}
				if(!$replace) {
					user_error("no replace is specified, find is: $find");
				}
				if($doBasicReplacement) {
					if(!$markStickingPoints) {
						if(strpos('#', $replace) !== false) {
							continue;
						}
					}
					$textSearchMachine->setSearchKey($find);
					$textSearchMachine->setReplacementKey($replace);
					$textSearchMachine->writeLogToFile($logFileLocation);
					$textSearchMachine->showLog();
				}
				else {
					$textSearchMachine->setSearchKey($find);
					$textSearchMachine->setFutureReplacementKey($replace);
					$textSearchMachine->showLog();//showing log
				}
				$textSearchMachine->clearCache();
			}
		}
	}

	/**
	 *
	 * @param String $to - e.g. 3.0 or 3.1
	 * @return array like this:
	 * 	array(
	 *	"	php" = array(
	 *			"A" => "B"
	 * 		)
	 * 	)
	 */
	private function getReplacementArrays($to){
		$array = array();
		$array["3.0"]["yaml"] = array();
		$array["3.0"]["yml"] = array();
		$array["3.0"]["js"] = array();
		$array["3.0"]["ss"] = array(
			array('sapphire\/','framework\/'),
			array('<% control ','<% loop|with ')
		);
		$array["3.0"]["php"] = array(
			array('Director::currentPage(','Director::get_current_page('),
			array('Member::currentMember(','Member::currentUser('),
			array('new DataObjectSet','new ArrayList'),
			array('new FieldSet','new FieldList'),
			array('DBField::create(','DBField::create_field('),
			array('Database::alteration_message(','DB::alteration_message('),
			array('Director::isSSL()','(Director::protocol()===\'https://\')'),
			array('extends SSReport','extends SS_Report'),
			array('function getFrontEndFields()','function getFrontEndFields($params = null)'),
			array('function updateCMSFields(&$fields)','function updateCMSFields($fields)'),
			array('function Breadcrumbs()','function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false)'),
			array('extends DataObjectDecorator','extends DataExtension'),
			array('extends SiteTreeDecorator','extends SiteTreeExtension'),
			array('function extraStatics()','function extraStatics($class = null, $extension = null)'),
			array('function updateCMSFields($fields)','function updateCMSFields(FieldList $fields)'),
			array('function updateCMSFields(&$fields)','function updateCMSFields(FieldList $fields)'),
			array('function updateCMSFields(FieldSet &$fields)','function updateCMSFields(FieldList $fields)'),
			array('function canEdit()','function canEdit($member = null)'),
			array('function canView()','function canView($member = null)'),
			array('function canCreate()','function canCreate($member = null)'),
			array('function canDelete()','function canDelete($member = null)'),
			array('function Field()','function Field($properties = array())'),
			array('function sendPlain()','function sendPlain($messageID = null)'),
			array('function send()','function send($messageID = null)'),
			array('function apply(SQLQuery','function apply(DataQuery'),
			array('function updateCMSFields(FieldSet','function updateCMSFields(FieldList'),
			array('function extraStatics()','function extraStatics($class = null, $extension = null)'),
			array('Form::disable_all_security_tokens','SecurityToken::disable'),
			array('Root.Content.','Root.'),
			array('SAPPHIRE_DIR','FRAMEWORK_DIR'),
			array('SAPPHIRE_PATH','FRAMEWORK_PATH'),
			array('SAPPHIRE_ADMIN_DIR','FRAMEWORK_ADMIN_DIR'),
			array('SAPPHIRE_ADMIN_PATH','FRAMEWORK_ADMIN_PATH'),
			array('new ImageField(','new UploadField('),
			# This is dangerous because custom code might call the old statics from a non page/page-controller
			array('Director::redirect(','$this->redirect(### UPGRADE: this should be a controller class, otherwise use Controller::curr()->redirect'),
			array('Director::redirectBack(','$this->redirectBack(### UPGRADE: this should be a controller class? ###'),
			array('Director::redirected_to(','$this->redirectBack(### UPGRADE: this should be a controller class? ###'),
			array('Director::set_status_code(','$this->setStatusCode(### UPGRADE: this should be a controller class? ###'),
			array('Director::URLParam(','$this->getRequest()->param(### UPGRADE: is this in a controller class?  ### '),
			array('Director::URLParams(','$this->getRequest()->params(### UPGRADE: is this in a controller class?  ### '),
			array('Member::map(','DataList::("Member")->map(### UPGRADE: check filter = "", sort = "", blank=""  ###'),
			array('new HasManyComplexTableField','new GridField(### UPGRADE: check syntax  ###'),
			array('new ManyManyComplexTableField','new GridField(### UPGRADE: check syntax ###'),
			array('new ComplexTableField','new GridField(### UPGRADE: check syntax ###'),
			//also needs attention
			array('->map(','->map(### UPGRADE: map returns SS_Map and not an Array use ->map->toArray to get Array ###'),
			array('->getComponentSet(','->getComponentSet(### NEEDS ATTENTION'),
		);

		//http://doc.silverstripe.org/framework/en/3.1/changelogs/3.1.0
		$array["3.1"]["php"] = array(
			array('public static $','private static $'),
			array('protected static $','private static $'),
		);

		if(isset($array[$to])) {
			return $array[$to];
		}
		else {
			user_error("no data is available for this upgrade");
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
* @version :  1.0
* Date     :  06/25/2006
* Purpose  :  Searching and replacing text within files of specified path
*/

class TextSearch {

	var $ignoreFolderArray    = array();

	var $extensions           = array();

	var $findAllExts          = 1; //by default all extensions

	var $searchKey            = '';

	var $replacementKey       = '';

	var $futureReplacementKey = '';

	var $isReplacingEnabled   = 0;

	var $caseSensitive        = 0; //by default case sensitivity is OFF

	var $logString            = '';

	var $errorText            = '';

	var $totalFound           = 0; //total matches

	/**
	 *   Sets folders to ignore
	 *   @param Array ignoreFolderArray
	 *   @return none
	 */
	public function setIgnoreFolderArray($ignoreFolderArray = array()) {
		$this->ignoreFolderArray = $ignoreFolderArray;
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
	public function startSearching($path){
		$this->findDirFiles($path);
	}

	/**
	 * Shows Log
	 * @return none
	 */
	public function showLog() {
		if($this->totalFound) {
			$this->dBug(nl2br("------ ".$this->totalFound." matches for: ".$this->logString));
		}
		if($this->errorText!='') {
			$this->dBug(nl2br("------Error-----".$this->errorText));
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
		fwrite($fp, "\n------ Total ".$this->totalFound." Matches Found -----\n");
		if($this->errorText){
			fwrite($fp, "\n------Error-----------Error-----------Error-----------Error-----\n");
			fwrite($fp, $this->errorText);
			fwrite($fp, "\n------Error-----------Error-----------Error-----------Error-----\n");
		}
		fclose($fp);
	}

	/**
	 *
	 * clears cache data
	 *
	 */
	public function clearCache() {
		$this->logString = '';
		$this->errorText = '';
		$this->totalFound = 0;
	}


	private static $file_array = array();

	/**
	 * loads all the applicable files
	 * @param String $path (e.g. "." or "/var/www/mysite.co.nz")
	 * @param Boolean $innerLoop - is the method calling itself???
	 */
	private function getFileArray($path, $innerLoop = false){
		if(!count(self::$file_array) || $innerLoop) {
			$dir = opendir ($path);
			while ($file = readdir ($dir)) {
				if (($file == ".") || ($file == "..") || ( __FILE__ == "$path/$file" ) || ($path == "." && basename(__FILE__) == $file)) {
					continue;
				}
				if (filetype ("$path/$file") == "dir") {
					if(in_array($file,$this->ignoreFolderArray) && $path == ".") {
						continue;
					}
					$this->getFileArray("$path/$file", $innerLoop = true); //recursive traversing here
				}
				elseif($this->matchedExtension($file)) { //checks extension if we need to search this file
					if(filesize("$path/$file")) {
						self::$file_array["$path/$file"] = "$path/$file"; //search file data
					}
				}
			} //End of while
			closedir($dir);
		}
		return self::$file_array;
	}

	/**
	 * Recursively traverses files of a specified path
	 * @param  path to execute
	 * @return  none
	 */
	private function findDirFiles($path) {
		$array = $this->getFileArray($path);
		foreach($array as $file) {
			$this->searchFileData("$path/$file");
		}
	}

	/**
	 * Finds extension of a file
	 * @param filename
	 * @return file extension
	 */
	private function findExtension($file) {
		return array_pop(explode(".",$file));
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

	/**
	 * Dumps data
	 * @param String data to be dumped
	 * @return none
	 */
	private function dBug($dump){
		echo "<pre>";
		print_r($dump);
		echo "</pre>";
	}

}
