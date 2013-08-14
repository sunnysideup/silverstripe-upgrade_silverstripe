silverstripe-upgrade_silverstripe
=================================

some rough-and-ready tools to help you upgrade to the next version of Silverstripe


BASED ON WORK BY: phptek/ss-upgrade.sh (https://gist.github.com/phptek/3902357)
AMONG OTHERS

USAGE
=================================

1. copy this folder to the root folder of a project (this is the best place, in theory it can live anywhere).

2. change the top bit of upgrade-silverstre.php as you see fit (e.g. path location).
You can set the path, to "migrate to" e.g. 3.0 and the folders to ignore

3. either open the file in your web-browser OR from the command line.
   from the command line, you type:

$ php index.php /var/www/mywebsite.com/ 3.0 no no
($ denotes command line, dont add it to your command!)

This will run an analysis and give you a summary of changes that will be made.

Next, to make the replacements in file (including replacements that'll have to be manually fixed):

$ php index.php /var/www/mywebsite.com/ 3.0 yes yes
($ denotes command line, dont add it to your command!)

VERSION OPTIONS
=================================
Right now, you can upgrade from 2.4 to 3.0 and from 3.0 to 3.1

REPLACE OPTIONS
=================================

* there are basically three replacements modes:
** view proposed changes only
** make basic replacements (e.g. Root.Content becomes Root. )
** make basic replacements and mark areas that need to be changed manually
By default it is set to viewing proposed changes only.


EXCLUDE / INCLUDE FOLDER OPTIONS
=================================

Once you have set the bath path, you
can also set excluded folders.

By default, any folders with a file called

_manifest_exclude

will be excluded (as well as its children)

Other ones excluded by default are:
- assets,
- sapphire,
- framework
- cms,
- .svn,
- .git

In setting the exclusion folders,you can set
a. the name
b. full path

For any folders with just the name set (e.g. mysite rather than /var/www/mysite/), it will only
be ignored if it is in the base folder (e.g. /var/www/themes/css/mysite will not be skipped
if the base folder is /var/www/). The base folder is set when you run the code.


