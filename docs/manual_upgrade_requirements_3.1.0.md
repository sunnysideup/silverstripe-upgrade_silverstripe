

### default_cast is now Text

In order to reduce the chance of accidentally allowing XSS attacks, the value of default_cast
has been changed in 3.1 from HTMLText to Text. This means that any values used in a template
that haven't been explicitly cast as safe will be escaped (`<` replaced with `&lt;` etc).

When upgrading, if methods return HTML fragments they need to explicitly cast them
as such. This can either be done by returning an HTMLText object, like:

	:::php
	return DBField::create_field('HTMLText', '<div></div>');

or by defining the casting of the accessor method, like:

	:::php
	class Page extends SiteTree {
		private static $casting = array(
			'MyDiv' => 'HTMLText'
		)

		function MyDiv() {
			return '<div></div>';
		}
	}

SSViewer#process (and as a result ViewableData#renderWith) have been changed to already return
explicitly cast HTMLText instances, so functions that return the result of these methods won't
have to do any additional casting.

Note that this change means that if code was testing the result via is_string, that is no longer
reliable.



### Static properties are immutable and private, you must use Config API.

A common SilverStripe pattern is to use a static variable on a class to define a configuration parameter.
The configuration system added in SilverStripe 3.0 builds on this by using this static variable as a way
of defining the default value.

In SilverStripe 3.0, it was possible to edit this value at run-time and have the change propagate into the
configuration system. This is no longer the case, for performance reasons. We've marked all "configurable"
statics as `private`, so you can't set or retrieve their value directly.
When using static setters or getters, the system throws a deprecation warning.
Notable exceptions to this rule are all static setters which accept objects, such as `SS_Cache::add_backend()`.

Please change all run-time manipulation of configuration to use `Config::inst()->update()` or
`$this->config()->update()`. You can keep using procedural configuration through `_config.php`
through this new notation, although its encouraged to use the (faster) YAML config wherever possible.
For this purpose, we have added a `mysite/_config/config.yml` file.

Here's an example on how to rewrite a common `_config.php` configuration:

	:::php
	<?php
	global $project;
	$project = 'mysite';

	global $database;
	$database = 'SS_mydb';

	require_once('conf/ConfigureFromEnv.php');
	SSViewer::set_theme('simple');

	if(class_exists('SiteTree')) SiteTree::enable_nested_urls();

	if(Director::isLive()) Email::setAdminEmail('support@mydomain.com');

	if(is_defined('MY_REDIRECT_EMAILS')) Email::send_all_emails_to('developer@mydomain.com');

	SS_Log::add_writer(new SS_LogFileWriter(BASE_PATH . '/mylog.log'), SS_Log::WARN);

	if(strpos('Internet Explorer', $_SERVER['HTTP_USER_AGENT']) !== false) {
		SSViewer::set_theme('basic');
	}

	Object::add_extension('Member', 'MyMemberExtension');

The upgraded `_config.php`:

	:::php
	<?php
	global $project;
	$project = 'mysite';

	global $database;
	$database = 'SS_mydb';

	require_once('conf/ConfigureFromEnv.php');

	// Removed SiteTree::enable_nested_urls() since its configured by default

	// Requires PHP objects, keep in PHP config
	SS_Log::add_writer(new SS_LogFileWriter(BASE_PATH . '/mylog.log'), SS_Log::WARN);
	// Non-trivial conditional, keep in PHP config
	if(strpos('Internet Explorer', $_SERVER['HTTP_USER_AGENT']) !== false) {
		// Overwrites any earlier YAML config
		Config::inst()->update('SSViewer'. 'theme', 'basic');
	}

The upgraded `config.yml`:

	:::yml
	---
	Name: mysite
	After: 'framework/*','cms/*'
	---
	SSViewer:
	  theme: 'simple'
	Member:
	  extensions:
	    - MyMemberExtension
	---
	Only:
	  environment: 'live'
	---
	Email:
	  admin_email: 'support@mydomain.com'

Some examples of changed notations (not exhaustive, there's over a hundred in total):

 * `SSViewer::set_theme()`: Use `SSViewer.theme` instead
 * `SecurityAdmin::$hidden_permissions`: Use `Permission.hidden_permissions` instead
 * `Director::setBaseFolder`: Use `Director.alternate_base_folder` instead
 * `Director::setBaseURL`: Use `Director.alternate_base_url` instead
 * `SSViewer::setOption('rewriteHashlinks', ...)`: Use `SSViewer.rewrite_hashlinks` instead

<div class="warning" markdown='1'>
Please remember to upgrade the installer project as well, particularly
your `.htaccess` or `web.config` files. Web access to these sensitive YAML configuration files
needs to be explicitly denied through these configuration files (see the [3.0.5 security release](/changelogs/3.0.4))
for details.
</div>

For more information about how to use the config system, see the ["Configuration" topic](/topic/configuration).






### Deny URL access if `Controller::$allowed_actions` is undefined or empty array

In order to make controller access checks more consistent and easier to
understand, the routing will require definition of `$allowed_actions`
on your own `Controller` subclasses if they contain any actions accessible through URLs.

	:::php
	class MyController extends Controller {
		// This action is now denied because no $allowed_actions are specified
		public function myaction($request) {}
	}

You can overwrite the default behaviour on undefined `$allowed_actions` to allow all actions,
by setting the `RequestHandler.require_allowed_actions` config value to `false` (not recommended).

This applies to anything extending `RequestHandler`, so please check your `Form` and `FormField`
subclasses as well. Keep in mind, action methods as denoted through `FormAction` names should NOT
be mentioned in `$allowed_actions` to avoid CSRF issues.
Please review all rules governing allowed actions in the ["controller" topic](/topics/controller).







### Removed support for overriding rules on parent classes through `Controller::$allowed_actions`

Since 3.1, the `$allowed_actions` definitions only apply
to methods defined on the class they're also defined on.
Overriding inherited access definitions is no longer possible.

	:::php
	class MyController extends Controller {
		public static $allowed_actions = array('myaction' => 'ADMIN');
		public function myaction($request) {}
	}
	class MySubController extends MyController {
		// No longer works
		public static $allowed_actions = array('myaction' => 'CMS_ACCESS_CMSMAIN');
	}

This also applies for custom implementations of `handleAction()` and `handleRequest()`,
which now have to be listed in the `$allowed_actions` specifically.
It also restricts `Extension` classes applied to controllers, which now
can only grant or deny access or methods they define themselves.

New approach with the [Config API](/topics/configuration)

	:::php
	class MySubController extends MyController {
		public function init() {
			parent::init();

			Config::inst()->update('MyController', 'allowed_actions',
				array('myaction' => 'CMS_ACCESS_CMSMAIN')
			);
		}
	}

Please review all rules governing allowed actions in the
["controller" topic](/topics/controller).







### RestfulService verifies SSL peers by default

This makes the implementation "secure by default", by removing
the call to `curl_setopt(CURLOPT_SSL_VERIFYPEER, false)`.
Failing to validate SSL peers makes HTTP requests vulnerable to man in the middle attacks.
The underlying `curl` library relies on the operating system for the resulting CA certificate
verification. On some systems (mainly Windows), these certificates are not available on
a standard PHP installation, and need to be added manually through `CURLOPT_CAINFO`.
Although it is not recommended, you can restore the old insecure behaviour with
the following configuration: `RestfulService::set_default_curl_option(CURLOPT_SSL_VERIFYPEER, false)`.



### Other

 * `SiteTree::$nested_urls` enabled by default. To disable, call `SiteTree::disable_nested_urls()`.


 * Removed CMS permission checks from `File->canEdit()` and `File->canDelete()`. If you have unsecured
   controllers relying on these permissions, please override them through a `DataExtension`.


    * Removed support for keyed arrays in `SelectionGroup`, use new `SelectionGroup_Item` object
   to populate the list instead (see [API docs](api:SelectionGroup)).

* Removed `Form->Name()`: Use getName()





 * `i18n::$common_locales` and `i18n::$common_languages` are now accessed via the Config API, and contain
   associative rather than indexed arrays.
   Before: `array('de_DE' => array('German', 'Deutsch'))`,
   After: `array('de_DE' => array('name' => 'German', 'native' => 'Deutsch'))`.




 * Changed the way FreeStrings in `SSTemplateParser` are recognized, they will now also break on inequality
   operators (`<`, `>`). If you use inequality operators in free strings in comparisions like

   `<% if Some<String == Some>Other>String %>...<% end_if %>`

   you have to replace them with explicitly markes strings like

   `<% if "Some<String" == "Some>Other>String" %>...<% end_if %>`

   This change was necessary in order to support inequality operators in comparisons in templates





 * Hard limit displayed pages in the CMS tree to `500`, and the number of direct children to `250`,
   to avoid excessive resource usage. Configure through `Hierarchy.node_threshold_total` and `
   Hierarchy.node_threshold_leaf`.  Set to `0` to show tree unrestricted.



    * `Object` now has `beforeExtending` and `afterExtending` to inject behaviour around method extension.
  `DataObject` also has `beforeUpdateCMSFields` to insert fields between automatic scaffolding and extension
  by `updateCMSFields`. See the [DataExtension Reference](/reference/dataextension) for more information.
 * Magic quotes is now deprecated. Will trigger user_error on live sites, as well as an error on new installs





 * Forms created in the CMS should now be instances of a new `CMSForm` class,
   and have the CMS controller's response negotiator passed into them.
   Example: `$form = new CMSForm(...); $form->setResponseNegotiator($this->getResponseNegotiator());`
