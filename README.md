silverstripe-upgrade_silverstripe
=================================

some rough-and-ready tools to help you upgrade to the next version of Silverstripe


HEAVILY BASED ON WORK BY: phptek/ss-upgrade.sh (https://gist.github.com/phptek/3902357)
AMONG OTHERS

USAGE
=================================

1. copy this file to the root folder of a project (although it can live anywhere)

2. change the top bit of upgrade-silverstre.php as you see fit

3. either open the file in your web-browser OR from the command line.
   from the command line, you type:

php upgrade-silverstripe.php /var/www/mywebsite.com/


PECULIARITIES
=================================

* there are basically three replacements modes:
# view proposed changes only
# make basic replacements (e.g. Root.Content becomes Root. )
# make basic replacements and mark problem areas
By default it is set to viewing proposed changes only.

* you set the root path in the URL as path=mypath or from the command line as the first argument.

* any folders including _manifest will be excluded
as well as other obvious folders, such as: assets, sapphire, cms, .svn, etc...



