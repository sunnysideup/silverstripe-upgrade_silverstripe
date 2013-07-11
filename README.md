silverstripe-upgrade_silverstripe
=================================

some rough-and-ready tools to help you upgrade to the next version of Silverstripe


HEAVILY BASED ON WORK BY: phptek/ss-upgrade.sh (https://gist.github.com/phptek/3902357)
AMONG OTHERS

USAGE
=================================

1. copy this file to the root folder of a project (although it can live anywhere).
If you like to just update on module then place it in the root of the module.

2. change the top bit of upgrade-silverstre.php as you see fit (e.g. path location).
You can set the path, to "migrate to" e.g. 3.0 and the folders to ignore

3. either open the file in your web-browser OR from the command line.
   from the command line, you type:

`php upgrade-silverstripe.php /var/www/mywebsite.com/`

This will run an analysis and give you a summary of changes that will be made.

Next, to make the replacements in file (including replacements that'll have to be manually fixed):

`php upgrade-silverstripe.php /var/www/mywebsite.com/ yes yes`


REPLACE OPTIONS
=================================

* there are basically three replacements modes:
** view proposed changes only
** make basic replacements (e.g. Root.Content becomes Root. )
** make basic replacements and mark problem areas
By default it is set to viewing proposed changes only.


EXCLUDE / INCLUDE OPTIONS
=================================

* you set the root path in the URL as get variable path=mypath
or from the command line as the first argument.
* any folders with a folder

_manifest_exclude

will be excluded as well as other obvious ones, such as:
assets,
sapphire,
cms,
.svn,
etc...

In setting the exclusion folders,you can a
a. set the name
b. the full path

for any folders with just the name set (e.g. mysite rather than /var/www/mysite/), it will only
be ignored if it is in the base folder (e.g. /var/www/themes/css/mysite will not be skipped
if the base folder is /var/www/). The base folder is set when you run the code.


