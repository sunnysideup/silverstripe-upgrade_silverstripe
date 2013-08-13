### Common upgrade tasks

 * Change CMS tab paths from `Root.Content.Main` to `Root.Main`, move some field changes to new `SiteTree->getSettingsFields()` method ([more](/changelogs/3.0.0#tab-paths))

 * Add new modules if using specific core features like Widget, RestfulServer, PageComment or Translatable ([more](/changelogs/3.0.0#moved-widget-api-into-new-widgets-module-widgets))

### Object static functions replaced with new Config class {#new-config}

Any arrays you pass as values to `update()` will be automatically merged. To replace the variable, call `remove()` first, then call `update()`.


Note the different options for the third parameter of `get()`:

 * `Config::UNINHERITED` will only get the configuration set for the specific class, not any of it's parents.
 * `Config::FIRST_SET` will inherit configuration from parents, but stop on the first class that actually provides a value.
 * `Config::EXCLUDE_EXTRA_SOURCES` will not use additional static sources (such as those defined on extensions)

If you don't set an option, it will get all the values for the static, including inherited ones.
This was previously known as `Object::combined_static()`.




### New ORM: More flexible and expressive querying via `DataList` {#new-orm-datalist}

The new "fluent" syntax to retrieve ORM records allows for a more
expressive notation (instead of unnamed arguments).

	:::php
	// before
	DataObject::get('Member', '"FirstName" = \'Sam'\', '"Surname" ASC");
	// after
	Member::get()->filter(array('FirstName' => 'Sam'))->sort('Surname');

The underlying record retrieval and management is rewritten from scratch, and features
lazy loading which fetches only the records it needs, as late as possible.
In order to retrieve all ORM records manually (as the previous ORM would've done),
please use `DataList->toArray()`.

The old getters (`DataObject::get()`, `DataObject:;get_one()`, `DataObject::get_by_id()`)
are now deprecated, but continue to operate. Instead of a `DataObjectSet`, they'll
now return a `DataList`.

	:::php
	// before
	DataObject::get_one('Member', '"Email" = \'someone@example.com\'');
	// after
	Member::get()->filter('Email', 'someone@example.com')->First();

	:::php
	// before
	DataObject::get_by_id('Member', 5);
	// after
	Member::get()->byID(5);

Note that they will return a `DataList` even if they're empty, so if you want to check
for the presence of records, please call the count() method on the `DataList`:

	:::php
	// before
	if(!DataObject::get('SiteTree', '"ParentID" = 5')) echo "Page 5 has no children";
	// after
	if(!DataObject::get('SiteTree', '"ParentID" = 5')->count()) echo "Page 5 has no children";




### InnoDB driver for existing and new tables on MySQL (instead of MyISAM) [innodb]###

SilverStripe has traditionally created all MySQL tables with the MyISAM storage driver,
mainly to ensure a fulltext search based on MySQL works out of the box.
Since then, the framework has gained many alternatives for fulltext search
([sphinx](https://github.com/silverstripe/silverstripe-sphinx), [solr](https://github.com/nyeholt/silverstripe-solr), etc.), and relies more on database transactions and other features not available in MyISAM.

This change convert tables on existing databases when `dev/build` is called,
unless the `FullTextSearch` feature is enabled. In order to disable this behaviour,
you have to add the following code to your `_config.php` BEFORE running a `dev/build`:

	:::php
	DataObject::$create_table_options['MySQLDatabase'] = 'ENGINE=MyISAM';

As with any SilverStripe upgrade, we recommend database backups before calling `dev/build`.
See [mysql.com](http://dev.mysql.com/doc/refman/5.5/en/converting-tables-to-innodb.html) for details on the conversion.
Note: MySQL has made InnoDB the default engine in its [5.5 release](http://dev.mysql.com/doc/refman/5.5/en/innodb-storage-engine.html).



### New template engine [templates]###

The template engine has been completely rewritten, and although it is generally backward compatible, there are new features
and some features have been deprecated. See the [template upgrading guide](/reference/templates-upgrading-guide) and the
[template reference](/reference/templates) for more information.



Most aspects of the interface have been redesigned, which necessitated a substantial
redevelopment of the underlying logic and presentation.
If you have customized the admin interface in any way, please review
the detailed changelog for this release. Many interface components have changed completely,
unfortunately there is no clear upgrade path for every interface detail.
As a starting point, have a look at the new templates in `cms/templates`
and `framework/admin/templates`, as well as the new [jQuery.entwine](https://github.com/hafriedlander/jquery.entwine)
based JavaScript logic. Have a look at the new ["Extending the CMS" guide](../howto/extend-cms-interface),
["CSS" guide](../topics/css), ["JavaScript" guide](../topics/javascript) and
["CMS Architecture" guide](/reference/cms-architecture) to get you started.

### New tree library [tree]###

The page tree moved from a bespoke tree library to [JSTree](http://jstree.com),
which required changes to markup of the tree and its JavaScript architecture.
This includes changes to `TreeDropdownField` and `TreeMultiSelectField`.


### TinyMCE upgraded to 3.5 ###

TinyMCE has been upgraded to version 3.5.

This change should be transparent to most people upgrading, but if you're using custom plugins for TinyMCE,
please ensure they are still working correctly with the new version.

If you're upgrading from an SS 3.0 beta, TinyMCE HTML source editor and other popups might be blank.
This is caused by the TinyMCE compressor leaving stale cache files in the system temp folder from an earlier
version. To resolve this problem, simply delete the `{hash}.gz` files within your temp location (defined by `sys_get_temp_dir()` in PHP.)
These cache files will be regenerated next time the CMS is opened.



### Stylesheet preprocessing via SCSS and the "compass" module [scss]###

CSS files in the `cms` and `framework/admin` modules are now generated through
the ["compass" SilverStripe module](http://silverstripe.org/compass-module), which uses
the ["Compass" framework](http://compass-style.org/) and the ["SCSS" language](http://sass-lang.com/).
This allows us to build more flexible and expressive stylesheets as a foundation for any
extensions to the CMS interface.

The "compass" module is only required if core stylesheets are modified,
not when simply using the CMS or developing other CMS functionality.
If you want to extend the CMS stylesheets for your own projects without SCSS,
please create a new CSS file and link it into the CMS via `[api:LeftAndMain::require_css()]`.





### EmailField now uses type "email" instead of type "text" {#email-form-field}

EmailField now uses "email" for the `type` attribute, which integrates better with HTML5 features like
form validation in the browser. If you want to change this back to "text", use `setAttribute()` when constructing the field:

	:::php
	$field = new EmailField('Email');
	$field->setAttribute('type', 'text');




### Restructured files and folders [file-restructure]###

In order to make the SilverStripe framework useable without the `cms` module,
we've moved some files around.
CMS base functionality which is not directly related to content pages (`SiteTree`)
has been moved from the `cms` module into a new "sub-module" located in `framework/admin`.
This includes generic management interfaces like "Files & Images" (`AssetAdmin`),
"Security" (`SecurityAdmin`) and the `ModelAdmin` class.
On the other hand, `SiteTree` related features were moved from `framework` to the `cms` module.

Due to the built-in PHP class autoloader,
this usually won't have any effect on your own code (unless you're including direct file paths).
For any other files (CSS files, templates, images, JavaScript) which might
be referenced by their path, please doublecheck that their path is still valid.



### Removed prototype.js and and behaviour.js dependencies from most core components [prototype-behaviour]

This will only affect you if you used either of those libraries,
or by extension on the globals set in `prototype_improvements.js` and `jquery_improvements.js`.
The `$$()` shorthand for `document.getElementsBySelector()` is no longer globally bound,
but rather just defined when used through other components. The `$()` shorthand
had two meanings, based on context: Either `document.getElementsById()` through prototype.js,
or as an alias for the `jQuery()` method. In general, we recommend not to rely on
the `$()` global in SilverStripe, as we unset it via `[jQuery.noConflict()](http://api.jquery.com/jQuery.noConflict/)`.
Use a [custom alias via function scope](http://api.jquery.com/jQuery.noConflict/#example-1) if possible.




### Moved `Widget` API into new 'widgets' module [widgets]###

See [module on github](https://github.com/silverstripe/silverstripe-widgets).



### Moved `Translatable` extension into new 'translatable' module ###

If you are translating your `SiteTree` or `DataObject` classes with the `Translatable`
extension, please install the new module from `http://silverstripe.org/translatable-module`.
The following settings can be removed from your own `_config.php`, as they're automatically
included through `translatable/_config.php`:

	Object::add_extension('SiteTree', 'Translatable');
	Object::add_extension('SiteConfig', 'Translatable');





### Moved Group->IPRestrictions into a new 'securityextras' [securityextras]module

IP restrictions for group memberships in the "Security" section were a rarely used feature,
and cluttered up the interface. We've decided to move it to a separate module
called [securityextras](https://github.com/silverstripe-labs/silverstripe-securityextras).
To continue using these restrictions, just install the module - no data migration required.





### Moved SiteTree->HomepageForDomain into a new 'homepagefordomain' module [homepagefordomain]

The setting determines difference homepages at arbitrary locations in the page tree,
and was rarely used in practice - so we moved it to a "[homepagefordomain](https://github.com/silverstripe-labs/silverstripe-homepagefordomain)" module.





### Removed "auto-merging" of member records from `Member->onBeforeWrite()` [member-merging]

Due to security reasons. Please use `DataObject->merge()` explicitly if this is desired behaviour.
