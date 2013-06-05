<?php


################### WHAT ARE WE UPGRADING ? ###########################
upgrade(
	$pathLocation = "code", 
	$logFileLocation = "./ss_upgrade_log.txt", 
	$from = "2.4", 
	$to = "3.0", 
	$doReplacement = false
);
##############################################


function upgrade($pathLocation = "code", $logFileLocation = "./ss_upgrade_log.txt", $from = "2.4", $to = "3.0", $doReplacement = false) {
	$array = getReplacementArrays("php", $from, $to);
	foreach($array as $replaceArray) {
		$obj = new TextSearch();
		$obj->setExtensions(array('php')); //setting extensions to search files within
		//$obj->addExtension('php');//adding an extension to search within
		$obj->setSearchKey($replaceArray[0]);
		if($doReplacement) {
			$obj->setReplacementKey($replaceArray[1]);//setting replacement text if you want to replace matches with that
		}
		$obj->startSearching($pathLocation);//starting search
		$obj->showLog();//showing log
		$obj->writeLogToFile($logFileLocation); //writting result to log file
	}
	$array = getReplacementArrays("php", $from, $to);
	foreach($array as $replaceArray) {
		$obj = new TextSearch();
		$obj->setExtensions(array('ss')); //setting extensions to search files within
		//$obj->addExtension('php');//adding an extension to search within
		$obj->setSearchKey($replaceArray[0]);
		if($doReplacement) {
			$obj->setReplacementKey($replaceArray[1]);//setting replacement text if you want to replace matches with that
		}
		$obj->startSearching($pathLocation);//starting search
		$obj->showLog();//showing log
		$obj->writeLogToFile($logFileLocation); //writting result to log file
	}
}

function getReplacementArrays($fileExtension, $from, $to){
	$array = array();

	$array["ss"]["2.4"]["3.0"] = array(
		array('sapphire\/','framework\/'),
		array('<% control ','<% loop|with ')
	);

	$array["php"]["2.4"]["3.0"] = array(
		array('Director::currentPage(','Director::get_current_page('),
		array('Member::currentMember(','Member::currentUser('),
		array('new DataObjectSet','new ArrayList'),
		array('new FieldSet','new FieldList'),
		array('DBField::create(','DBField::create_field('),
		array('Director::URLParam(','Controller::curr()->getRequest()->param('),
		array('Director::urlParam(','Controller::curr()->getRequest()->param('),
		array('Database::alteration_message(','DB::alteration_message('),
		array('Director::isSSL()',"(Director::protocol()===\'https:\/\/\')"),
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
		# This is dangerous because custom code might call the old statics from a non page/page-controller
		array('Director::redirect(','$this->redirect('),
		array('Director::redirectBack(','$this->redirectBack(')
	);

	if(isset($array[$fileExtension][$from][$to])) {
		return $array[$fileExtension][$from][$to];
	}
	else {
		user_error("no data is available for this upgrade");
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

class TextSearch
{
	 var $extensions         = array();
	 var $searchKey          = '';
	 var $replacementKey     = '';
	 var $caseSensitive      = 0; //by default case sensitivity is OFF
	 var $findAllExts        = 1; //by default all extensions
	 var $isReplacingEnabled = 0;
	 var $logString          = '';
	 var $errorText          = '';
	 var $totalFound         = 0; //total matches

	 /**
	 *   Sets extensions to look
	 *   @param Array extensions
	 *   @return none
	 */
	 function setExtensions($extensions = array())
	 {
			$this->extensions = $extensions;

			if(sizeof($this->extensions))
			{
				 $this->findAllExts = 0; //not all extensions
			}
	 }//End of Method

	 /**
	 * Adds a search extension
	 * @param  file extension
	 * @return none
	 */
	 function addExtension($extension)
	 {

			array_push($this->extensions, $extension);
			$this->findAllExts = 0; //not all extensions

	 }//End of function


	 /**
	 * Sets search key and case sensitivity
	 * @param search key, case sensitivity
	 * @return none
	 */
	 function setSearchKey($searchKey, $caseSensitive = 0)
	 {
			$this->searchKey = $searchKey;

			if($caseSensitive)
			{
				 $this->caseSensitive	= 1; //yeah, case sensitive
			}
	 }//End of function

	 /**
	 *   Sets key to replace searchKey with
	 *   @param : replacement key
	 *   @return none
	 */
	 function setReplacementKey($replacementKey)
	 {

			$this->replacementKey     = $replacementKey;
			$this->isReplacingEnabled = 1;

	 }//End of function

	 /**
	 * Wrapper function around function findDirFiles()
	 * @param $path to search
	 * @return none
	 */
	 function startSearching($path)
	 {
			$this->findDirFiles($path);
	 }//EO Method

	 /**
	 * Recursively traverses files of a specified path
	 * @param  path to execute
	 * @return  none
	 */
	 function findDirFiles($path) {
			$dir = opendir ($path);
			while ($file = readdir ($dir)) {
				 if (($file == ".") || ($file == "..") || (__FILE__ == $path/$file)) {
						continue;
				 }

				 if (filetype ("$path/$file") == "dir") {
						$this->findDirFiles("$path/$file"); //recursive traversing here
				 }
				 elseif($this->matchedExtension($file)) { //checks extension if we need to search this file
					 if(filesize("$path/$file")) {
							 $this->searchFileData("$path/$file"); //search file data
					 }
				 }
			} //End of while
			closedir($dir);
	 }//EO Method

	 /**
	 * Finds extension of a file
	 * @param filename
	 * @return file extension
	 */
	 function findExtension($file)
	 {
		 return array_pop(explode(".",$file));
	 }//End of function

	 /**
	 * Checks if a file extension is one the extensions we are going to search
	 * @param filename
	 * @return true in success, false otherwise
	 */
	 function matchedExtension($file)
	 {
			if($this->findAllExts) //checks if all extensions are to be searched
			{
				 return true;
			}
			elseif(sizeof(array_keys($this->extensions, $this->findExtension($file)))==1)
			{
				 return true;
			}

			return false;

	 }//EO Method

	 /**
	 * Searches data, replaces (if enabled) with given key, prepares log
	 * @param $file
	 * @return none
	 */
	 function searchFileData($file)
	 {
			$searchKey  = preg_quote($this->searchKey, '/');

			if($this->caseSensitive)
			{
				 $pattern    = "/$searchKey/U";
			}
			else
			{
				 $pattern    = "/$searchKey/Ui";
			}

			$subject       = file_get_contents($file);

			$found = 0;

			$found = preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);

			$this->totalFound +=$found;

			if($found)
			{
				 $foundStr = "Found in $found places";
				 $this->appendToLog($file, $foundStr);
			}


			if($this->isReplacingEnabled && $this->replacementKey && $found)
			{
				 $outputStr = preg_replace($pattern, $this->replacementKey, $subject);
				 $foundStr = "Found in $found places";
				 $this->writeToFile($file, $outputStr);
				 $this->appendToLog($file, $foundStr, $this->replacementKey);

			}
			elseif($this->isReplacingEnabled && $this->replacementKey == '')
			{
				 $this->errorText .= "Replacement Text is not defined\n";
				 $this->appendToLog($file, "Replacement Text is not defined", $this->replacementKey);
			}
			elseif(!$found)
			{
				 //$this->appendToLog($file, "No matching Found", $this->replacementKey);
			}

	 }//EO Method

	 /**
	 * Writes new data (after the replacement) to file
	 * @param $file, $data
	 * @return none
	 */
	 function writeToFile($file, $data)
	 {
			if(is_writable($file))
			{
				 $fp = fopen($file, "w");
				 fwrite($fp, $data);
				 fclose($fp);
			}
			else
			{
				 $this->errorText .= "Can not replace text. File $file is not writable. \nPlease make it writable\n";
			}

	 }//EO Method

	 /**
	 * Appends log data to previous log data
	 * @param filename, match string, replacement key if any
	 * @return none
	 */
	 function appendToLog($file, $matchStr, $replacementKey = null)
	 {
			if($this->logString == '')
			{
				 $this->logString = " --- Searching for '".$this->searchKey."' --- \n";
			}

			if($replacementKey == null)
			{
				 $this->logString .= "Searching File $file : " . $matchStr."\n";
			}
			else
			{
				 $this->logString .= "Searching File $file : " . $matchStr.". Replaced by '$replacementKey'\n";
			}

	 }//EO Method

	 /**
	 * Shows Log
	 * @param none
	 * @return none
	 */
	 function showLog() {
			if($this->totalFound) {
				$this->dBug("------ Total ".$this->totalFound." Matches Found -----");
				$this->dBug(nl2br($this->logString));
			}
			if($this->errorText!='') {
				 $this->dBug("------Error-----");
				 $this->dBug(nl2br($this->errorText));
			}
	 }//EO Method

	 /**
	 * Writes log to file
	 * @param log filename
	 * @return none
	 */
	 function writeLogToFile($file)
	 {
			$fp = fopen($file, "a") OR user_error("Can not open file <b>$file</b>");
			fwrite($fp, "\n\n================================================");
			fwrite($fp, $this->logString);
			fwrite($fp, "\n------ Total ".$this->totalFound." Matches Found -----\n");
			if($this->errorText!='')
			{
				 fwrite($fp, "\n------Error-----\n");
				 fwrite($fp, $this->errorText);
			}

			fclose($fp);
	 }//EO Method

	 /**
	 * Dumps data
	 * @param data to be dumped
	 * @return none
	 */
	 function dBug($dump)
	 {
			echo "<pre>";
			print_r($dump);
			echo "</pre>";
	 }//EO Method

} //End of class
