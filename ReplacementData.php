<?php

class ReplacementData {

	private $marker = " ### UPGRADE_REQUIRED  ";

	private $endMarker = "###";

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
	public function getReplacementArrays($to){
		$array = array();
		$array["3.0"]["yaml"] = array();
		$array["3.0"]["yml"] = array();
		$array["3.0"]["js"] = array();
		$array["3.0"]["ss"] = array(

			array('sapphire\/',
			      'framework\/'),

			array('<% control ',
			      '<% ',
			      ' use loop OR with '),

			array('<% end_control ',
			      '<% ',
			      ' use end_loop OR end_with '),
		);
		$array["3.0"]["php"] = array(

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

			array('function updateCMSFields(&$fields)',
			      'function updateCMSFields($fields)'),

			array('function Breadcrumbs()',
			      'function Breadcrumbs($maxDepth = 20, $unlinked = false, $stopAtPageType = false, $showHidden = false)'),

			array('extends DataObjectDecorator',
			      'extends DataExtension'),

			array('extends SiteTreeDecorator',
			      'extends SiteTreeExtension'),

			array('function updateCMSFields($fields)',
			      'function updateCMSFields(FieldList $fields)'),

			array('function updateCMSFields(&$fields)',
			      'function updateCMSFields(FieldList $fields)'),

			array('function updateCMSFields(FieldSet &$fields)',
			      'function updateCMSFields(FieldList $fields)'),

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

			array('function updateCMSFields(FieldSet',
			      'function updateCMSFields(FieldList'),

			array('Form::disable_all_security_tokens',
			      'SecurityToken::disable'),

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

			array('Convert::json2array(',
			      'json_decode('),

			array('Convert::json2array(',
			      'json_decode('),

			array('Root.Content.Main',
			      'Root.Main'),

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


			# This is dangerous because custom code might call the old statics from a non page/page-controller

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

			array('->map(',
			      '->map(',
			      ' map returns SS_Map and not an Array use ->map->toArray to get Array '),

			array('->getComponentSet(',
			      '->getComponentSet(',
			      ' - check new syntax '),

			array('DataObject::get(',
			      'DataObject::get(',
			      ' - replace with ClassName::get( '),

			array('DataObject::get_one(',
			      'DataObject::get(',
			      ' - replace with ClassName::get()->First() '),

			array('DataObject::get_by_id(',
			      'DataObject::get(',
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


			array('function extraStatics()',
			      'function extraStatics()',
			      ' Remove me: simply define static vars on extension directly, or use add_to_class()  '),


			array('extendedSQL(',
			      'extendedSQL(',
			      ' - Use ->dataQuery()->query() on DataList if access is needed to SQLQuery (see syntax) '),

			array('buildSQL(',
			      'buildSQL(',
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
			      '->IsAjax(',
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

			array('new SimpleImageField',
			      'new SimpleImageField',
			      ' $folderName optional constructor argument must now be set using a setter on the instance of the field.\nAlso recommended to use UploadField with setAllowedExtensions instead.'),

			array('extends FileField',
			      'extends FileField',
			      ' Note: $folderName optional constructor argument must now be set using a setter on the instance of the field.'),

			array('extends SimpleImageField',
			      'extends SimpleImageField',
			      ' Note: $folderName optional constructor argument must now be set using a setter on the instance of the field.\nAlso recommended to use UploadField with setAllowedExtensions instead.'),

			array('new FileIframeField',
			      'new FileIframeField',
			      ' Use UploadField instead. '),



			array('new SimpleImageField',
			      'new FileIframeField',
			      ' Use UploadField instead. '),

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

			array('new QueuedEmailDispatchTask',
			      'new QueuedEmailDispatchTask',
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
		);

		//http://doc.silverstripe.org/framework/en/3.1/changelogs/3.1.0
		$array["3.1"]["php"] = array(

			array('public static $',
			      'private static $'),

			array('protected static $',
			      'private static $'),
		);

		if(isset($array[$to])) {
			return $array[$to];
		}
		else {
			user_error("no data is available for this upgrade");
		}

	}
}