<?php

class ReplacementData {

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

	function __construct(){
		$this->fullArray = $this->getData(null);
		$count = 0;
		foreach($this->fullArray as $to => $subArray) {
			$this->tos[$to] = $to;
			foreach($subArray as $language => $subSubArray) {
				$this->languages[$language] = $language;
				foreach($subSubArray as $replaceArray) {
					$this->flatFindArray[$language][$language."_".$to."_".$count] = $replaceArray[0];
					$this->flatReplacedArray[$language][$language."_".$to."_".$count] = $replaceArray[1];
					$count++;
				}
			}
		}
	}

	public function getReplacementArrays($to){
		return $this->fullArray[$to];
	}

	private $fullArray = array();
	function getFullArray(){ return $this->fullArray;}

	private $tos = array();
	function getTos(){ return $this->tos;}

	private $languages = array();
	function getLanguages(){ return $this->languages;}

	private $flatFindArray = array();
	function getFlatFindArray(){ return $this->flatFindArray;}

	private $flatReplacedArray = array();
	function getFlatReplacedArray(){ return $this->flatReplacedArray;}

	private function getData($to) {
		$array = array();
		$array["3.0"]["yaml"] = array();
		$array["3.0"]["yml"] = array();
		$array["3.0"]["js"] = array();
		$array["3.0"]["ss"] = array(

			array('sapphire\/',
			      'framework\/'),

			array('<% control ',
			      '<% with/loop'),

			array('<% end_control ',
			      '<% end_with/loop'),
		);
		$array["3.0"]["php"] = array(

			array('Folder::findOrMake',
			      'Folder::find_or_make'),

			array('Director::currentPage(',
			      'Director::get_current_page('),

			array('Member::currentMember(',
			      'Member::currentUser('),

			array('new DataObjectSet',
			      'new ArrayList'),

			array('new FieldSet',
			      'new FieldList'),

			array('DBField::create(',
			      'DBField::create_field('),

			array('Database::alteration_message(',
			      'DB::alteration_message('),

			array('Director::isSSL()',
			      '(Director::protocol()===\'https://\')'),

			array('extends SSReport',
			      'extends SS_Report'),

			array('function getFrontEndFields()',
			      'function getFrontEndFields($params = null)'),

			array('function Breadcrumbs()',
			      'function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false)'),

			array('extends DataObjectDecorator',
			      'extends DataExtension'),

			array('extends SiteTreeDecorator',
			      'extends SiteTreeExtension'),

			array('function updateCMSFields(FieldSet &$field)',
			      'function updateCMSFields(FieldList $fields'),

			array('function updateCMSFields(FieldSet',
			      'function updateCMSFields(FieldList'),

			array('function updateCMSFields(&$fields',
			      'function updateCMSFields(FieldList $fields'),

			array('function updateCMSFields( $fields',
			      'function updateCMSFields( FieldList $fields'),

			array('function updateCMSFields($fields',
			      'function updateCMSFields(FieldList $fields'),

			array('function updateCMSFields( FieldSet',
			      'function updateCMSFields( FieldList'),

			array('function updateCMSFields( &$fields',
			      'function updateCMSFields( FieldList $fields'),

			array('function updateCMSFields( FieldSet &$field)',
			      'function updateCMSFields( FieldList $fields'),

			array('function canEdit()',
			      'function canEdit($member = null)'),

			array('function canView()',
			      'function canView($member = null)'),

			array('function canCreate()',
			      'function canCreate($member = null)'),

			array('function canDelete()',
			      'function canDelete($member = null)'),

			array('function Field()',
			      'function Field($properties = array())'),

			array('function sendPlain()',
			      'function sendPlain($messageID = null)'),

			array('function send()',
			      'function send($messageID = null)'),

			array('function apply(SQLQuery',
			      'function apply(DataQuery'),

			array('Form::disable_all_security_tokens',
			      'SecurityToken::disable'),

			array('Root.Content.Main',
			      'Root.Main'),

			array('Root.Content.',
			      'Root.'),

			array('SAPPHIRE_DIR',
			      'FRAMEWORK_DIR'),

			array('SAPPHIRE_PATH',
			      'FRAMEWORK_PATH'),

			array('SAPPHIRE_ADMIN_DIR',
			      'FRAMEWORK_ADMIN_DIR'),

			array('SAPPHIRE_ADMIN_PATH',
			      'FRAMEWORK_ADMIN_PATH'),

			array('Convert::json2array(',
			      'json_decode('),

			array('Root.Content.Metadata',
			      'Root.Main'),

			array('->fieldByName(\'Content\')->fieldByName(\'Main\')',
			      '->fieldByName(\'Main\')'),

			array('->fieldByName("Content")->fieldByName("Main")',
			      '->fieldByName("Main")'),

			array('->fieldByName(\'Content\')->fieldByName(\'Metadata\')',
			      '->fieldByName(\'Main\')'),

			array('->fieldByName("Content")->fieldByName("Metadata")',
			      '->fieldByName("Main")'),

			array('CMSMainMarkingFilter',
			      'CMSSiteTreeFilter_Search'),

			array('MySQLFulltextSearchable',
			      'FulltextSearchable'),

			array('new LeftAndMainDecorator',
			      'new LeftAndMainExtension'),

			array('ClassInfo::is_subclass_of(',
			      'is_cublass_of('),

			array('getClassFile(',
			      'SS_ClassManifest::getItemPath('),

			array('->TreeTitle(',
			      '->getTreeTitle('),

			array('new SubstringFilter',
			      'new PartialMatchFilter'),

			array('$ParagraphSummary',
			      '$Content'),

			array('$ParsedContent',
			      '$Content'),

			# This is dangerous because custom code might call the old statics from a non page/page-controller

			array('->FieldSet(',
			      '->FieldList(',
			      'For CompositeField only.'),

			array('Director::redirect(',
			      '$this->redirect(',
			      'this should be a controller class, otherwise use Controller::curr()->redirect'),

			array('Director::redirectBack(',
			      '$this->redirectBack(',
			      ' this should be a controller class, otherwise use Controller::curr()->redirectBack '),

			array('Director::redirected_to(',
			      '$this->redirectBack(',
			      ' this should be a controller class? '),

			array('Director::set_status_code(',
			      '$this->setStatusCode(',
			      ' this should be a controller class? '),

			array('Director::URLParam(',
			      '$this->getRequest()->param(',
			      ' is this in a controller class?'),

			array('Director::URLParams(',
			      '$this->getRequest()->params(',
			      ' is this in a controller class?'),

			array('Member::map(',
			      'DataList::("Member")->map(',
			      ' check filter = "", sort = "", blank=""  '),

			array('new HasManyComplexTableField',
			      'new GridField(',
			      ' check syntax  '),

			array('new ManyManyComplexTableField',
			      'new GridField(',
			      ' check syntax '),

			array('new ComplexTableField',
			      'new GridField(',
			      ' check syntax '),

			array('new TableListField',
			      'new GridField(',
			      ' check syntax  '),

			array('new ImageField(',
			      'new UploadField(',
			      ' Check Syntax'),

			array('DataObjectDecorator',
			      'DataExtension',
			      ' check syntax'),

			array('->getComponentSet(',
			      '->getComponentSet(',
			      ' - check new syntax '),

			array('DataObject::get(',
			      'DataObject::get(',
			      ' - replace with ClassName::get( '),

			array('DataObject::get_one(',
			      'DataObject::get_one(',
			      ' - replace with ClassName::get()->First() '),

			array('DataObject::get_by_id(',
			      'DataObject::get_by_id(',
			      ' - replace with ClassName::get()->byID($id) '),

			array('DB::query("SELECT COUNT(*)',
			      'DB::query("SELECT COUNT(*)',
			      ' replace with MyClass::get()->count() '),

			array('sapphire',
			      'FRAMEWORK_DIR',
			      ' - changed from sapphire/ to framework/ - using constant preferred.  '),

			array('::set_static(',
			      'Config::inst()->update(',
			      ' `Object::set_static(\'MyClass\', \'myvar\')` becomes `Config::inst()->update(\'MyClass\', \'myvar\', \'myval\')` instead.  '),

			array('::addStaticVars(',
			      'Config::inst()->update(',
			      ' Object::addStaticVars(\'MyClass\', array(\'myvar\' => \myval\'))` should be replaced with individual calls to `Config::inst()->update()` instead.  '),

			array('::add_static_var(',
			      'Config::inst()->update(',
			      ' Object::add_static_var(\'MyClass\', \'myvar\', \'myval\')` becomes `Config::inst()->update(\'MyClass\', \'myvar\', \'myval\')` '),

			array('::set_uninherited(',
			      'Config::inst()->update(',
			      ' * `Object::set_uninherited(\'MyClass\', \'myvar\', \'myval\')` becomes `Config::inst()->update(\'MyClass\', \'myvar\', \'myval\')` instead. '),

			array('::get_static(',
			      'Config::inst()->get(',
			      ' `Object::get_static(\'MyClass\', \'myvar\')` becomes `Config::inst()->get(\'MyClass\', \'myvar\', Config::FIRST_SET)` '),

			array('::uninherited_static(',
			      'Config::inst()->get(',
			      ' Object::uninherited_static(\'MyClass\', \'myvar\')` becomes `Config::inst()->get(\'MyClass\', \'myvar\', Config::UNINHERITED)` '),

			array('::combined_static(',
			      'Config::inst()->get(',
			      ' `Object::combined_static(\'MyClass\', \'myvar\')` becomes `Config::inst()->get(\'MyClass\', \'myvar\')` (no option as third argument) '),

			array('function extraStatics',
			      'function extraStatics',
			      ' Remove me: simply define static vars on extension directly, or use add_to_class()  '),

			array('extendedSQL(',
			      'extendedSQL(',
			      ' - Use ->dataQuery()->query() on DataList if access is needed to SQLQuery (see syntax) '),

			array('->buildSQL(',
			      '->buildSQL(',
			      ' - Use ->dataQuery()->query() on DataList if access is needed to SQLQuery (see syntax) '),

			array('new SQLQuery(',
			      'new SQLQuery(',
			      ' Internal properties: ($from, $select, $where, $orderby, $groupby, $having, $limit, $distinct, $delete, $connective) now use getters, setters and adders. e.g. getFrom(), setFrom(), addFrom(), getLimit(), setLimit().\n innerJoin() has been renamed to addInnerJoin(), leftJoin() renamed to addLeftJoin() '),

			array('DataObject::Aggregate(',
			      'DataObject::Aggregate(',
			      '`DataObject::Aggregate()` and `DataObject::RelationshipAggregate()` are now deprecated. To replace your deprecated aggregate calls
			        in PHP code, you should query with something like `Member::get()->max(\'LastEdited\')`, that is, calling the aggregate on the `DataList` directly.
			        The same concept applies for replacing `RelationshipAggregate()`, just call the aggregate method on the relationship instead,
			        so something like `Member::get()->Groups()->max(\'LastEdited\')`.
			        For partial caching in templates, the syntax `<% cached Aggregate(Page).Max(LastEdited) %>` has been deprecated. The new syntax is similar,
			        except you use `List()` instead of `Aggregate()`, and the aggregate call `Max()` is now lowercase, as in `max()`.
			        An example of the new syntax is `<% cached List(Page).max(LastEdited) %>`. Check `DataList` class for more aggregate methods to use.'),

			array('DataObject::RelationshipAggregate(',
			      'DataObject::RelationshipAggregate(',
			      '`DataObject::Aggregate()` and `DataObject::RelationshipAggregate()` are now deprecated. To replace your deprecated aggregate calls
			        in PHP code, you should query with something like `Member::get()->max(\'LastEdited\')`, that is, calling the aggregate on the `DataList` directly.
			        The same concept applies for replacing `RelationshipAggregate()`, just call the aggregate method on the relationship instead,
			        so something like `Member::get()->Groups()->max(\'LastEdited\')`.
			        For partial caching in templates, the syntax `<% cached Aggregate(Page).Max(LastEdited) %>` has been deprecated. The new syntax is similar,
			        except you use `List()` instead of `Aggregate()`, and the aggregate call `Max()` is now lowercase, as in `max()`.
			        An example of the new syntax is `<% cached List(Page).max(LastEdited) %>`. Check `DataList` class for more aggregate methods to use.'),

			array('->CurrentMember(',
			      '->CurrentMember(',
			      ' Replace with Member::currentUser() '),

			array('->getSecurityID(',
			      '->getSecurityID(',
			      ' Replace with SecurityToken::inst()->getValue() '),

			array('->HasPerm(',
			      '->HasPerm(',
			      ' Replace with Permission::check($code) '),

			array('->BaseHref(',
			      '->BaseHref(',
			      ' Replace with Director::absoluteBaseURL() '),

			array('->AbsoluteBaseURL(',
			      '->AbsoluteBaseURL(',
			      ' Replace with Director::absoluteBaseURL() '),

			array('->IsAjax',
			      '->IsAjax',
			      ' Replace with Director::is_ajax() '),

			array('->i18nLocale(',
			      '->i18nLocale(',
			      ' Replace with i18n::get_locale() '),

			array('->CurrentPage(',
			      '->CurrentPage(',
			      ' Replace with Controller::curr() '),

			array('->getCMSFields(array',
			      '->getCMSFields(array',
			      ' Remove parameters: Need to customize FormScaffolder directly'),

			array('->getCMSFields($',
			      '->getCMSFields($',
			      ' Remove parameters: Need to customize FormScaffolder directly'),

			array('->getCMSFields( $',
			      '->getCMSFields( $',
			      ' Remove parameters: Need to customize FormScaffolder directly'),

			array('->getCMSFields( array',
			      '->getCMSFields( array',
			      ' Remove parameters: Need to customize FormScaffolder directly'),

			array('root.Behaviour',
			      'root.Behaviour',
			      ' Custom fields in the behaviour and access tabs should now be added using getSettingsFields and updateSettingsFields (in DataExtensions) '),

			array('root.Access',
			      'root.Access',
			      ' Custom fields in the behaviour and access tabs should now be added using getSettingsFields and updateSettingsFields (in DataExtensions) '),

			array('extends ModelAdmin',
			      'extends ModelAdmin',
			      ' Review docs for new ModelAdmin usage '),

			array('->addExtraClass(',
			      '->addExtraClass(',
			      ' CHECK FOR PREVIOUS USE OF INCONSISTENCIES: CSS class names applied through FormField->addExtraClass()
			        and the "type" class are now consistently added to the container `<div>`
			        as well as the HTML form element itself. '),

			array('extends Validator',
			      'extends Validator',
			      ' Note that javascript client-side validation is no longer supported. Specifically the javascript() method will no longer be of any use. '),

			array('Validator::set_javascript_validation_handler(',
			      'Validator::set_javascript_validation_handler(',
			      ' Deprecated. No longer available. '),

			array('new TextareaField',
			      'new TextareaField',
			      ' $form, $maxLength, $rightTitle, $rows/$cols optional constructor arguments must now be set using setters on the instance of the field. '),

			array('new HtmlEditorField',
			      'new HtmlEditorField',
			      ' $form, $maxLength, $rightTitle, $rows/$cols optional constructor arguments must now be set using setters on the instance of the field. '),

			array('extends TextareaField',
			      'extends TextareaField',
			      ' Note: $form, $maxLength, $rightTitle, $rows/$cols optional constructor arguments must now be set using setters on the instance of the field. '),

			array('extends HtmlEditorField',
			      'extends HtmlEditorField',
			      ' Note: $form, $maxLength, $rightTitle, $rows/$cols optional constructor arguments must now be set using setters on the instance of the field. '),

			array('new FileField',
			      'new FileField',
			      ' $folderName optional constructor argument must now be set using a setter on the instance of the field.'),

			array('extends FileField',
			      'extends FileField',
			      ' Note: $folderName optional constructor argument must now be set using a setter on the instance of the field.'),

			array('new SimpleImageField',
			      'new FileIframeField',
			      ' Use UploadField instead. Note: $folderName optional constructor argument must now be set using a setter on the instance of the field.\nAlso recommended to use UploadField with setAllowedExtensions instead.'),

			array('new FileIframeField',
			      'new FileIframeField',
			      ' Use UploadField instead. '),

			array('extends Widget',
			      'extends Widget',
			      ' Make sure silverstripe-widgets module is installed '),

			array('new Widget',
			      'new Widget',
			      ' Make sure silverstripe-widgets module is installed '),

			array('extends NZGovtPasswordValidator',
			      'extends NZGovtPasswordValidator',
			      ' Make sure silverstripe-securityextras module is installed '),

			array('new NZGovtPasswordValidator',
			      'new NZGovtPasswordValidator',
			      ' Make sure silverstripe-securityextras module is installed '),

			array('extends GeoIP',
			      'extends GeoIP',
			      ' Make sure silverstripe-geoip module is installed '),

			array('new GeoIP',
			      'new GeoIP',
			      ' Make sure silverstripe-geoip module is installed '),

			array('static $api_access =',
			      'static $api_access =',
			      ' Make sure silverstripe-restfulserver and silverstripe-soapserver are installed. '),

			array('$Comments',
			      '$Comments',
			      ' Make sure silverstripe-comments module is installed.'),

			array('->Comments',
			      '->Comments',
			      ' Make sure silverstripe-comments module is installed.'),

			array('$lang[',
			      '$lang[',
			      ' Move translations to YAML translation file. See: https://github.com/chillu/i18n_yml_converter'),

			array('extends SS_Report',
			      'extends SS_Report',
			      ' No longer need to ::register reports. Silverstripe does this automatically. Reports can be excluded using SS_Report::add_excluded_reports()\nSQLQuery\'s are also unavailable. Use DataLists instead. '),

			array('extends SapphireTest',
			      'extends SapphireTest',
			      ' Note: Unit tests require definition of used `DataObject` and `Extension` classes using SapphireTest->extraDataObjects and SapphireTest->requiredExtensions '),

			array('static $breadcrumbs_delimiter',
			      'static $breadcrumbs_delimiter',
			      ' Need to remove this and create a template to customize breadcrumbs now. '),

			array('new AdvancedSearchForm',
			      'new AdvancedSearchForm',
			      ' Removed. Can extend SearchForm to get similar functionality '),

			array('new Archive',
			      'new Archive',
			      ' To continue use of this, you will need to copy the class from 2.4.'),

			array('new TarballArchive',
			      'new TarballArchive',
			      ' To continue use of this, you will need to copy the class from 2.4.'),

			array('new AssetTableField',
			      'new GridField',
			      ' Use GridFieldConfig_RelationEditor & see syntax'),

			array('new ComponentSet',
			      'new ComponentSet',
			      ' Replace with ManyManyList or HasManyList '),

			array('new CustomRequiredFields',
			      'new RequiredFields',
			      ' See syntax '),

			array('new DataObjectLog',
			      'new DataObjectLog',
			      ' Removed: no replacement.'),

			array('new MemberTableField',
			      'new GridField',
			      ' check syntax and use GridFieldConfig_RelationEditor '),

			array('new Notifications',
			      'new Notifications',
			      ' To continue use of this, you will need to copy the class from 2.4.'),

			array('new QueuedEmail',
			      'new QueuedEmail',
			      ' To continue use of this, you will need to copy the class from 2.4.'),

			array('new RestrictedTextField',
			      'new RestrictedTextField',
			      ' Removed: use custom fields instead.'),

			array('new UniqueTextField',
			      'new UniqueTextField',
			      ' Removed: use custom fields instead.'),

			array('new UniqueRestrictedTextField',
			      'new UniqueRestrictedTextField',
			      ' Removed: use custom fields instead.'),

			array('new AutocompleteTextField',
			      'new AutocompleteTextField',
			      ' Removed: use custom fields instead.'),

			array('new ConfirmedFormAction',
			      'new ConfirmedFormAction',
			      ' Removed: use custom fields instead.'),

			array('new TreeSelectorField',
			      'new TreeDropdownField',
			      ' check syntax'),

			array('new SQLMap',
			      'new SS_Map',
			      ' check syntax'),

			array('new XML',
			      'new XML',
			      ' Removed: Use PHP\'s built-in SimpleXML instead '),

			array('Director::set_dev_servers(',
			      'Director::set_dev_servers(',
			      'Use Director::set_environment_type() or an _ss_environment.php instead.'),

			array('Director::set_test_servers(',
			      'Director::set_test_servers(',
			      'Use Director::set_environment_type() or an _ss_environment.php instead.'),

			array('->getPageLimits(',
			      '->getPageLimits(',
			      'Use getPageStart, getPageLength, or getTotalItems instead.'),
			/*
			array('->dataFieldByName(',
			      '->dataFieldByName(',
			      'Use Fields() and FieldList API instead.'),
			*/
			array('->unsetDataFieldByName(',
			      '->unsetDataFieldByName(',
			      'Use Fields() and FieldList API instead.'),

			array('->unsetFieldFromTab(',
			      '->unsetFieldFromTab(',
			      'Use Fields() and FieldList API instead.'),

			array('->resetField(',
			      '->resetField(',
			      'Use Fields() and FieldList API instead.'),

			array('->unsetActionByName(',
			      '->unsetActionByName(',
			      'Use Actions() and FieldList API instead.'),

			array('->FormEncType(',
			      '->FormEncType(',
			      'Please use Form->getEncType() instead.'),

			array('->Name(',
			      '->getName(',
			      'Use getName() for FormField '),

			array('->setTabIndex(',
			      '->setAttribute(',
			      'Use setAttribute("tabindex") instead'),

			array('->getTabIndex(',
			      '->getAttribute(',
			      'Use getAttribute("tabindex") instead'),

			array('->createTag(',
			      '->createTag(',
			      '(FormField) Please define your own FormField template using setFieldTemplate() '),

			array('->describe(',
			      '->setDescription(',
			      '(FormField) Use setDescription()'),

			array('new ImageFormAction',
			      'new ImageFormAction',
			      'Use FormAction wtih setAttribute("src", "myimage.png") and custom JavaScript to achieve hover effect'),

			array('extends ImageFormAction',
			      'extends ImageFormAction',
			      'Use FormAction wtih setAttribute("src", "myimage.png") and custom JavaScript to achieve hover effect'),

			array('->startClosed(',
			      '->setStartClosed(',
			      '(ToggleCompositeField)'),

			array('->join(',
			      '->join(',
			      '(DataList/DataQuery) use innerJoin() or leftJoin() instead'),

			array('->setComponent(',
			      '->setComponent(',
			      '(DataObject) No longer in use (no replacement)'),

			array('->instance_get(',
			      '->instance_get(',
			      '(DataObject) Use DataList::create and DataList to do your querying instead.'),

			array('->instance_get_one(',
			      '->instance_get_one(',
			      '(DataObject) Use DataList::create($this->class)->where($filter)->sort($orderby)->First() instead.'),

			array('->buildDataObjectSet(',
			      '->buildDataObjectSet(',
			      '(DataObject) Use DataList to do your querying instead.'),

			array('->databaseFields(',
			      '->databaseFields(',
			      '(DataObject) Use DataObject::database_fields() instead.'),

			array('->customDatabaseFields(',
			      '->customDatabaseFields(',
			      '(DataObject) Use DataObject::custom_database_fields() instead.'),

			array('->Lower(',
			      '->LowerCase(',
			      '(StringField)'),

			array('->Upper(',
			      '->UpperCase(',
			      '(StringField)'),

			array('->EscapeXML(',
			      '->EscapeXML(',
			      '(Text) Use DBField->XML() instead.'),

			array('->getArray(',
			      '->getArray(',
			      '(ArrayData) Use ArrayData::toMap() instead.'),

			array('LeftAndMain::set_loading_image(',
			      'LeftAndMain::set_loading_image(',
			      'Removed (no explanation)'),

			array('->isAdmin(',
			      '->isAdmin(',
			      'Use ->inGroup("ADMIN") instead'),

			//MUST TO LAST
			array('->map(',
			      '->map(',
			      ' map returns SS_Map and not an Array use ->map->toArray to get Array '),

			array('->toDropDownMap(',
			      '->toDropDownMap(',
			      'Use ->map()->toArray() instead')

		);



		//http://doc.silverstripe.org/framework/en/3.1/changelogs/3.1.0

		$array["3.1"]["ss"] = array(

			array('MetaKeywords',
			      'MetaKeywords',
			      'Has been removed, as is irrelevant in terms of SEO.'),

			array('MetaTitle',
			      'Title',
			      'MetaTitle field has been replaced by simply \'Title\''),

		);



		$array["3.1"]["php"] = array(

			array('public static $',
			      'private static $'),

			array('protected static $',
			      'private static $'),

			array('->setContainerFieldSet(',
			      '->setContainerFieldList('),

			array('->rootFieldSet(',
			      '->rootFieldList('),

			array('SQLMap::mapInGroups(',
			      'Member::map_in_groups('),

			array('Group::map(',
			      'DataList::("Group")->map(',
			      'Double check'),

			array('SQLMap::map(',
			      'DataList::("Member")->map(',
			      'Double check'),

			array('static $allowed_actions = array(\'*',
			      'static $allowed_actions = array(\'*',
			      'Wildcard rules no longer allowed. Need to specify all allowed actions.'),

			array('static $allowed_actions = array("*',
			      'static $allowed_actions = array("*',
			      'Wildcard rules no longer allowed. Need to specify all allowed actions.'),

			array('static $allowed_actions = array()',
			      'static $allowed_actions = array()',
			      'Empty allowed_actions will result in no access being allowed to this URL/controller'),

			array('function getCMSActions(',
			      'function getCMSActions(',
			      'The CMS buttons are now grouped, in order to hide minor actions by default and declutter the interface.
			       This required changing the form field structure from a simple `FieldList`
			       to a `FieldList` which contains a `CompositeField` for all "major actions",
			       and a `TabSet` with a single tab for all "minor actions".
			       If you have previously added, removed or altered built-in CMS actions in any way,
			       you\'ll need to adjust your code.'),

			array('new GridFieldDetailForm',
			      'new GridFieldDetailForm',
			      'This gridfield form now checks for canEdit() and canDelete() permissions. By default, it requires the admin permissions.'),

			array('new GridFieldAddNewButton',
			      'new GridFieldAddNewButton',
			      'This gridfield form now checks for canCreate() permissions. By default, it requires the admin permissions.'),

			array('new TableField',
			      'new TableField',
			      'To continue using this, you will need to install the silverstripe-labs/legacytablefields module.'),

			array('new HasOneComplexTableField',
			      'new HasOneComplexTableField',
			      'To continue using this, you will need to install the silverstripe-labs/legacytablefields module.'),

			array('prototype.js',
			      'prototype.js',
			      'To continue using this, you will need to include ensure you\'ve included the file yourself as it has been removed from core.'),

			array('behaviour.js',
			      'behaviour.js',
			      'To continue using this, you will need to include ensure you\'ve included the file yourself as it has been removed from core.'),

			array('MetaKeywords',
			      'MetaKeywords',
			      'Has been removed, as is irrelevant in terms of SEO.'),

			array('MetaTitle',
			      'Title',
			      'MetaTitle field has been replaced by simply \'Title\''),

			array('new Profiler',
			      'new Profiler',
			      'Deprecated; use third-party solution like xhprof'),

			array('extends Profiler',
			      'extends Profiler',
			      'Deprecated; use third-party solution like xhprof'),

			array('debug_profile',
			      'debug_profile',
			      '$_GET["debug_profile"] removed.'),

			array('debug_memory',
			      'debug_memory',
			      '$_GET["debug_memory"] removed.'),

			array('profile_trace',
			      'profile_trace',
			      '$_GET["profile_trace"] removed.'),

			array('debug_javascript',
			      'debug_javascript',
			      '$_GET["debug_javascript"] removed.'),

			array('debug_behaviour',
			      'debug_behaviour',
			      '$_GET["debug_behaviour"] removed.'),

			array('new Member_ProfileForm',
			      'new CMSProfileController',
			      'check syntax'),

			array('extends Member_ProfileForm',
			      'extends CMSProfileController',
			      'check new class'),

			array('new Email_BounceHandler',
			      'new Email_BounceHandler',
			      'To continue using this class, please install the silverstripe-labs/silverstripe-emailbouncehandler module'),

			array('new Email_BounceRecord',
			      'new Email_BounceRecord',
			      'To continue using this class, please install the silverstripe-labs/silverstripe-emailbouncehandler module'),

			array('->Bounced',
			      '->Bounced',
			      'To continue using email bouncing handler/records, please install the silverstripe-labs/silverstripe-emailbouncehandler module'),

			array('htmlEmail(',
			      'htmlEmail(',
			      'Deprecated; use the Email or Mailer API (including all global email helper methods)'),

			array('plaintextEmail(',
			      'plaintextEmail(',
			      'Deprecated; use the Email or Mailer API (including all global email helper methods)'),

			array('encodeMultipart(',
			      'encodeMultipart(',
			      'Deprecated; use the Email or Mailer API (including all global email helper methods)'),

			array('$inlineImages',
			      '$inlineImages',
			      'Removed.'),

			array('->setDescription(',
			      '->setDescription(',
			      'Note: Now renders as a span, rather than a title attribute'),

			array('->generateAutologinHash(',
			      '->generateAutologinHash(',
			      'Use Member::generateAutologinTokenAndHash instead'),

			array('->sendInfo',
			      '->sendInfo',
			      'In instance of Member, use Member_ChangePasswordEmail or Member_ForgotPasswordEmail directly'),

			array('PasswordEncryptor::register(',
			      'PasswordEncryptor::register(',
			      'Use config system instead'),

			array('PasswordEncryptor::unregister(',
			      'PasswordEncryptor::unregister(',
			      'Use config system instead'),

			array('->reverse(',
			      '->reverse(',
			      'ArrayList and DataList reverse method no longer modifies current list; only returns a new version.'),

			array('->sort(',
			      '->sort(',
			      'ArrayList and DataList sort method no longer modifies current list; only returns a new version.'),

			array('->filter(',
			      '->filter(',
			      'ArrayList filter method no longer modifies current list; only returns a new version.'),

			array('->exclude(',
			      '->exclude(',
			      'ArrayList exclude method no longer modifies current list; only returns a new version.'),

			array('->where(',
			      '->where(',
			      'DataList where method no longer modifies current list; only returns a new version.'),

			array('->limit(',
			      '->limit(',
			      'DataList limit method no longer modifies current list; only returns a new version.'),

			array('->addFilter(',
			      '->addFilter(',
			      'DataList addFilter method no longer modifies current list; only returns a new version.'),

			array('->applyFilterContext(',
			      '->applyFilterContext(',
			      'DataList applyFilterContext method no longer modifies current list; only returns a new version.'),

			array('->innerJoin(',
			      '->innerJoin(',
			      'DataList innerJoin method no longer modifies current list; only returns a new version.'),

			array('->leftJoin(',
			      '->leftJoin(',
			      'DataList leftJoin method no longer modifies current list; only returns a new version.'),

			array('->find(',
			      '->find(',
			      'DataList find method no longer modifies current list; only returns a new version.'),

			array('->byIDs(',
			      '->byIDs(',
			      'DataList byIDs method no longer modifies current list; only returns a new version.'),

			array('->dataQuery(',
			      '->dataQuery(',
			      'DataList byIDs method returns clone of the query; can no longer be used to modify query directly. Use alterDataQuery for that instead'),

			array('extends ScheduledTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends QuarterHourlyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends HourlyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends DailyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends MonthlyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends WeeklyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('extends YearlyTask',
			      'extends BuildTask',
			      'Scheduled/periodic tasks are deprecated and must now extend from BuildTask or CliController, and require that you make your own cron jobs'),

			array('i18n::$common_locales',
			      'i18n::$common_locales',
			      'now accessed via the Config API, and contain associative rather than indexed arrays.'),

			array('i18n::$common_languages',
			      'i18n::$common_languages',
			      'now accessed via the Config API, and contain associative rather than indexed arrays.'),

			array('SSViewer::set_theme(',
			      'SSViewer::set_theme(',
			      'Deprecated, use config api'),

			array('SSViewer::current_custom_theme(',
			      'SSViewer::current_custom_theme(',
			      'Please use Config API with SSViewer.theme_enabled'),

			array('new DateField',
			      'new DateField',
			      'instances automatically include
			       formatting hints as placeholders and description text below the field itself.
			       If you change the date/time format of those fields, you need to adjust the hints.
			       To remove the hints, use setDescription(null) and setAttribute(\'placeholder\', null).'),

			array('new TimeField',
			      'new TimeField',
			      'instances automatically include
			       formatting hints as placeholders and description text below the field itself.
			       If you change the date/time format of those fields, you need to adjust the hints.
			       To remove the hints, use setDescription(null) and setAttribute(\'placeholder\', null).'),

			array('new DatetimeField',
			      'new DatetimeField',
			      'instances automatically include
			       formatting hints as placeholders and description text below the field itself.
			       If you change the date/time format of those fields, you need to adjust the hints.
			       To remove the hints, use setDescription(null) and setAttribute(\'placeholder\', null).'),

			array('ModelAdmin::set_page_length(',
			      'ModelAdmin::set_page_length(',
			      'Use ModelAdmin.page_length config setting'),

			array('ModelAdmin::get_page_length(',
			      'ModelAdmin::get_page_length(',
			      'Use ModelAdmin.page_length config setting'),

			array('SecurityAdmin::add_hidden_permission(',
			      'SecurityAdmin::add_hidden_permission(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('SecurityAdmin::remove_hidden_permission(',
			      'SecurityAdmin::remove_hidden_permission(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('SecurityAdmin::get_hidden_permissions(',
			      'SecurityAdmin::get_hidden_permissions(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('SecurityAdmin::clear_hidden_permissions(',
			      'SecurityAdmin::clear_hidden_permissions(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('extends YamlFixture',
			      'extends YamlFixture',
			      '(YamlFixture) deprecated; use writeInto() and FixtureFactory accessors instead.'),

			array('->idFromFixture(',
			      '->idFromFixture(',
			      '(YamlFixture) deprecated; use writeInto() and FixtureFactory accessors instead.'),

			array('->allFixtureIDs(',
			      '->allFixtureIDs(',
			      '(YamlFixture) deprecated; use writeInto() and FixtureFactory accessors instead.'),

			array('->objFromFixture(',
			      '->objFromFixture(',
			      '(YamlFixture) deprecated; use writeInto() and FixtureFactory accessors instead.'),

			array('->saveIntoDatabase(',
			      '->saveIntoDatabase(',
			      '(YamlFixture) deprecated; use writeInto() and FixtureFactory accessors instead.'),

			array('->wrapImagesInline(',
			      '->wrapImagesInline(',
			      '(Mailer) Functionality removed'),

			array('->wrapImagesInline_rewriter(',
			      '->wrapImagesInline_rewriter(',
			      '(Mailer) Functionality removed'),

			array('->processHeaders(',
			      '->processHeaders(',
			      '(Mailer) Use Email->addCustomHeader() instead'),

			array('->encodeFileForEmail(',
			      '->encodeFileForEmail(',
			      '(Mailer) Use Email->attachFile() instead'),

			array('->QuotedPrintable_encode(',
			      '->QuotedPrintable_encode(',
			      '(Mailer) No longer available, handled internally'),

			array('->validEmailAddr(',
			      '->validEmailAddr(',
			      '(Mailer) Use Email->validEmailAddr() instead'),

			array('new ToggleField',
			      'new ReadonlyField',
			      'Use custom javascript with a ReadonlyField.'),

			array('->Aggregate(',
			      '->Aggregate(',
			      'Call aggregate methods on a DataList directly instead.  To replace your deprecated aggregate calls
			       in PHP code, you should query with something like `Member::get()->max(\'LastEdited\')`'),

			array('->RelationshipAggregate(',
			      '->RelationshipAggregate(',
			      'Call aggregate methods on a DataList directly instead.  To replace your deprecated aggregate calls
			       in PHP code, you should query with something like `Member::get()->max(\'LastEdited\')`'),

			array('MySQLDatabase::set_connection_charset(',
			      'MySQLDatabase::set_connection_charset(',
			      'Use "MySQLDatabase.connection_charset" config setting instead'),

			array('new ExactMatchMultiFilter',
			      'new ExactMatchFilter',
			      'Use ExactMatchFilter instead.'),

			array('extends ExactMatchMultiFilter',
			      'extends ExactMatchFilter',
			      'Use ExactMatchFilter instead.'),

			array('new NegationFilter',
			      'new ExactMatchFilter',
			      'Use ExactMatchFilter:not instead. Check syntax'),

			array('extends NegationFilter',
			      'extends ExactMatchFilter',
			      'Use ExactMatchFilter:not instead. Check syntax'),

			array('new StartsWithMultiFilter',
			      'new StartsWithFilter',
			      'Use StartsWithFilter instead. Check syntax'),

			array('extends StartsWithMultiFilter',
			      'extends ExactMatchFilter',
			      'Use StartsWithFilter instead. Check syntax'),

			array('Permission::add_to_hidden_permissions(',
			      'Permission::add_to_hidden_permissions(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('Permission::remove_from_hidden_permissions(',
			      'Permission::remove_from_hidden_permissions(',
			      'Use "Permission.hidden_permissions" config setting instead'),

			array('->generateHash(',
			      '->randomToken(',
			      'generateHash is deprecated as it only returns a random string.'),
		);

		$array["3.2"]["php"] = array(

			array('RestfulService::set_default_curl_option(',
			      'RestfulService::set_default_curl_option(',
			      'Use the "RestfulService.default_curl_options" config setting instead'),

			array('RestfulService::set_default_curl_options(',
			      'RestfulService::set_default_curl_options(',
			      'Use the "RestfulService.default_curl_options" config setting instead'),

			array('RestfulService::set_default_proxy(',
			      'RestfulService::set_default_proxy(',
			      'Use the "RestfulService.default_curl_options" config setting instead with direct reference to the CURL_* options'),

			array('ContentNegotiator::set_encoding(',
			      'ContentNegotiator::set_encoding(',
			      'Use the "ContentNegotiator.encoding" config setting instead'),

			array('ContentNegotiator::get_encoding(',
			      'ContentNegotiator::get_encoding(',
			      'Use the "ContentNegotiator.encoding" config setting instead'),

			array('ContentNegotiator::enable(',
			      'ContentNegotiator::enable(',
			      'Use the "ContentNegotiator.enabled" config setting instead'),

			array('ContentNegotiator::disable(',
			      'ContentNegotiator::disable(',
			      'Use the "ContentNegotiator.enabled" config setting instead'),

			array('Cookie::set_report_errors(',
			      'Cookie::set_report_errors(',
			      'Use the "Cookie.report_errors" config setting instead'),

			array('Cookie::report_errors(',
			      'Cookie::report_errors(',
			      'Use the "Cookie.report_errors" config setting instead'),

			array('->inst_set_report_errors(',
			      'Cookie::inst_set_report_errors(',
			      'Use the "Cookie.report_errors" config setting instead'),

			array('->inst_report_errors(',
			      '->inst_report_errors(',
			      'Use the "Cookie.report_errors" config setting instead'),

			array('Director::addRules(',
			      'Director::addRules(',
			      'Use the "Director.rules" config setting instead'),

			array('Director::setBaseURL(',
			      'Director::setBaseURL(',
			      'Use the "Director.alternate_base_url" config setting instead'),

			array('Director::setBaseFolder(',
			      'Director::setBaseFolder(',
			      'Use the "Director.alternate_base_folder" config setting instead'),

			array('Director::set_environment_type(',
			      'Director::set_environment_type(',
			      'Use the "Director.environment_type" config setting instead'),

			array('Session::set_cookie_domain(',
			      'Session::set_cookie_domain(',
			      'Use the "Session.cookie_domain" config setting instead'),

			array('Session::get_cookie_domain(',
			      'Session::get_cookie_domain(',
			      'Use the "Session.cookie_domain" config setting instead'),

			array('Session::set_cookie_path(',
			      'Session::set_cookie_path(',
			      'Use the "Session.cookie_path" config setting instead'),

			array('Session::get_cookie_path(',
			      'Session::get_cookie_path(',
			      'Use the "Session.cookie_path" config setting instead'),

			array('Session::set_cookie_secure(',
			      'Session::set_cookie_secure(',
			      'Use the "Session.cookie_secure" config setting instead'),

			array('Session::get_cookie_secure(',
			      'Session::get_cookie_secure(',
			      'Use the "Session.cookie_secure" config setting instead'),

			array('Session::set_session_store_path(',
			      'Session::set_session_store_path(',
			      'Use the "Session.session_store_path" config setting instead'),

			array('Session::get_session_store_path(',
			      'Session::get_session_store_path(',
			      'Use the "Session.session_store_path" config setting instead'),

			array('Session::set_timeout_ips(',
			      'Session::set_timeout_ips(',
			      'Use the "Session.timeout_ips" config setting instead'),

			array('Session::set_timeout(',
			      'Session::set_timeout(',
			      'Use the "Session.timeout" config setting instead'),

			array('Session::get_timeout(',
			      'Session::get_timeout(',
			      'Use the "Session.timeout" config setting instead'),

			array('LogEmailWriter::set_send_from(',
			      'LogEmailWriter::set_send_from(',
			      'Use the "SS_LogEmailWriter.send_from" config setting instead'),

			array('LogEmailWriter::get_send_from(',
			      'LogEmailWriter::get_send_from(',
			      'Use the "SS_LogEmailWriter.send_from" config setting instead'),

			array('Email::setAdminEmail(',
			      'Email::setAdminEmail(',
			      'Use the "Email.admin_email" config setting instead'),

			array('Email::getAdminEmail(',
			      'Email::getAdminEmail(',
			      'Use the "Email.admin_email" config setting instead'),

			array('Email::send_all_emails_to(',
			      'Email::send_all_emails_to(',
			      'Use the "Email.send_all_emails_to" config setting instead'),

			array('Email::cc_all_emails_to(',
			      'Email::cc_all_emails_to(',
			      'Use the "Email.cc_all_emails_to" config setting instead'),

			array('Email::bcc_all_emails_to(',
			      'Email::bcc_all_emails_to(',
			      'Use the "Email.bcc_all_emails_to" config setting instead'),

			array('GDBackend::set_default_quality(',
			      'GDBackend::set_default_quality(',
			      'Use the "GDBackend.default_quality" config setting instead'),

			array('GD::set_default_quality(',
			      'GD::set_default_quality(',
			      'Use the "GDBackend.default_quality" config setting instead'),

			array('ImagickBackend::set_default_quality(',
			      'ImagickBackend::set_default_quality(',
			      'Use the "IMagickBackend.default_quality" config setting instead'),

			array('DateField::set_default_config(',
			      'DateField::set_default_config(',
			      'Use the "DateField.default_config" config setting instead'),

			array('->createTag(',
			      '->createTag(',
			      '(FormField) Use FormField::create_tag() instead.'),

			array('i18n::set_js_i18n(',
			      'i18n::set_js_i18n(',
			      'Use the "i18n.js_i18n" config setting instead'),

			array('i18n::get_js_i18n(',
			      'i18n::get_js_i18n(',
			      'Use the "i18n.js_i18n" config setting instead'),

			array('i18n::set_date_format(',
			      'i18n::set_date_format(',
			      'Use the "i18n.date_format" config setting instead'),

			array('i18n::get_date_format(',
			      'i18n::get_date_format(',
			      'Use the "i18n.date_format" config setting instead'),

			array('i18n::set_time_format(',
			      'i18n::set_time_format(',
			      'Use the "i18n.time_format" config setting instead'),

			array('i18n::get_time_format(',
			      'i18n::get_time_format(',
			      'Use the "i18n.time_format" config setting instead'),

			array('DataObject::get_validation_enabled(',
			      'DataObject::get_validation_enabled(',
			      'Use the "DataObject.validation_enabled" config setting instead'),

			array('DataObject::set_validation_enabled(',
			      'DataObject::set_validation_enabled(',
			      'Use the "DataObject.validation_enabled" config setting instead'),

			array('->loadUploadedImage(',
			      '->loadUploadedImage(',
			      'Use Upload::loadIntoFile()'),

			array('Currency::setCurrencySymbol(',
			      'Currency::setCurrencySymbol(',
			      'Use the "Currency.currency_symbol" config setting instead'),

			array('BBCodeParser::smilies_location(',
			      'BBCodeParser::smilies_location(',
			      'Use the "BBCodeParser.smilies_location" config setting instead'),

			array('BBCodeParser::set_icon_folder(',
			      'BBCodeParser::set_icon_folder(',
			      'Use the "BBCodeParser.smilies_location" config setting instead'),

			array('BBCodeParser::autolinkUrls(',
			      'BBCodeParser::autolinkUrls(',
			      'Use the "BBCodeParser.autolink_urls" config setting instead'),

			array('BBCodeParser::disable_autolink_urls(',
			      'BBCodeParser::disable_autolink_urls(',
			      'Use the "BBCodeParser.autolink_urls" config setting instead'),

			array('BBCodeParser::smiliesAllowed(',
			      'BBCodeParser::smiliesAllowed(',
			      'Use the "BBCodeParser.allow_smilies" config setting instead'),

			array('BBCodeParser::enable_smilies(',
			      'BBCodeParser::enable_smilies(',
			      'Use the "BBCodeParser.allow_smilies" config setting instead'),

			array('Member::set_session_regenerate_id(',
			      'Member::set_session_regenerate_id(',
			      'Use the "Member.session_regenerate_id" config setting instead'),

			array('Member::set_login_marker_cookie(',
			      'Member::set_login_marker_cookie(',
			      'Use the "Member.login_marker_cookie" config setting instead'),

			array('Member::get_unique_identifier_field(',
			      'Member::get_unique_identifier_field(',
			      'Use the "Member.unique_identifier_field" config setting instead'),

			array('Member::set_unique_identifier_field(',
			      'Member::set_unique_identifier_field(',
			      'Use the "Member.unique_identifier_field" config setting instead'),

			array('Member::set_password_expiry(',
			      'Member::set_password_expiry(',
			      'Use the "Member.password_expiry_days" config setting instead'),

			array('Member::lock_out_after_incorrect_logins(',
			      'Member::lock_out_after_incorrect_logins(',
			      'Use the "Member.lock_out_after_incorrect_logins" config setting instead'),

			array('Permission::declare_permissions(',
			      'Permission::declare_permissions(',
			      'Use the "Permission.declared_permissions" config setting instead'),

			array('Security::get_word_list(',
			      'Security::get_word_list(',
			      'Use the "Security.word_list" config setting instead'),

			array('Security::set_word_list(',
			      'Security::set_word_list(',
			      'Use the "Security.word_list" config setting instead'),

			array('Security::set_default_message_set(',
			      'Security::set_default_message_set(',
			      'Use the "Security.default_message_set" config setting instead'),

			array('Security::setStrictPathChecking(',
			      'Security::setStrictPathChecking(',
			      'Use the "Security.strict_path_checking" config setting instead'),

			array('Security::getStrictPathChecking(',
			      'Security::getStrictPathChecking(',
			      'Use the "Security.strict_path_checking" config setting instead'),

			array('Security::set_password_encryption_algorithm(',
			      'Security::set_password_encryption_algorithm(',
			      'Use the "Security.password_encryption_algorithm" config setting instead'),

			array('Security::get_password_encryption_algorithm(',
			      'Security::get_password_encryption_algorithm(',
			      'Use the "Security.password_encryption_algorithm" config setting instead'),

			array('Security::set_login_recording(',
			      'Security::set_login_recording(',
			      'Use the "Security.login_recording" config setting instead'),

			array('Security::login_recording(',
			      'Security::login_recording(',
			      'Use the "Security.login_recording" config setting instead'),

			array('Security::set_default_login_dest(',
			      'Security::set_default_login_dest(',
			      'Use the "Security.default_login_dest" config setting instead'),

			array('Security::default_login_dest(',
			      'Security::default_login_dest(',
			      'Use the "Security.default_login_dest" config setting instead'),

			array('SSViewer::set_source_file_comments(',
			      'SSViewer::set_source_file_comments(',
			      'Use the "SSViewer.source_file_comments" config setting instead'),

			array('SSViewer::get_source_file_comments(',
			      'SSViewer::get_source_file_comments(',
			      'Use the "SSViewer.source_file_comments" config setting instead'),

			array('SSViewer::set_theme(',
			      'SSViewer::set_theme(',
			      'Use the "SSViewer.theme" config setting instead'),

			array('SSViewer::current_theme(',
			      'SSViewer::current_theme(',
			      'Use the "SSViewer.theme" config setting instead'),

			array('SSViewer::setOption(',
			      'SSViewer::setOption(',
			      'Use the "SSViewer.rewrite_hash_links" or "SSViewer.<optionName>" config setting instead'),

			array('SSViewer::getOption(',
			      'SSViewer::getOption(',
			      'Use the "SSViewer.rewrite_hash_links" or "SSViewer.<optionName>" config setting instead'),

		);
		ksort($array);
		if(isset($array[$to])) {
			return $array[$to];
		}
		elseif(!$to) {
			return $array;
		}
		else {
			user_error("no data is available for this upgrade");
		}

	}
}
