silverstripe-upgrade_silverstripe
=================================

Some rough-and-ready tools to help you upgrade to the next version of
[SilverStripe](http://www.silverstripe.org).

 This tool does not do all
the upgrading for you (it does do the easy bits), but it helps you
 (a) get an idea of how much is involved and
 (b) to make sure you are not missing anything.
By using this tool you can have a more structured approach to upgrading your
Silverstripe websites to 3.0 and 3.1 (and beyond).


BASED ON WORK BY:
[phptek/ss-upgrade.sh](https://gist.github.com/phptek/3902357), AMONG
OTHERS

USAGE
=================================

The upgrade tool can be used over a web browser or from the command
line. To use it via your web browser:

1. Copy this folder to the root folder of a project (this is the best
   place, in theory it can live anywhere).

2. Change the top bit of upgrade-silverstre.php as you see fit (e.g.
   path location). You can set the path, to "migrate to" e.g. 3.0 and
   the folders to ignore

3. Open index.php  in your web-browser

From the command line use the `index.php` file with positional
arguments:

    $ php index.php /var/www/mywebsite.com/ 3.0 no no

This will run an analysis and give you a summary of changes that will be
made.

Next, to make the replacements in file (including replacements that'll
have to be manually fixed):

    $ php index.php /var/www/mywebsite.com/ 3.0 yes yes

(The `$` character above denotes command line, don't add it to your
command!)

The command line arguments are as follows (by positional order):

1. Path to codebase
2. Targeted upgrade version
3. Make Basic Changes (straight find and replace)
4. Make Advanced Changes (mark areas that need human intervention)
5. Path to log file
6. List of folders to ignore (comma separated)

VERSION OPTIONS
=================================

Right now, you can upgrade from 2.4 to 3.0 and from 3.0 to 3.1

REPLACE OPTIONS
=================================

There are basically three replacements modes:

* view proposed changes only
* make basic replacements (e.g. Root.Content becomes Root. )
* make basic replacements and mark areas that need to be changed
  manually

By default it is set to viewing proposed changes only.


EXCLUDE / INCLUDE FOLDER OPTIONS
=================================

Once you have set the bath path, you
can also set excluded folders.

By default, any folders with a file called `_manifest_exclude` will be
excluded (as well as its children).

Other ones excluded by default are:

- assets
- sapphire
- framework
- cms
- .svn
- .git

In setting the exclusion folders, you can set (a) the name or (b) full
path.

For any folders with just the name set (e.g. `mysite` rather than
`/var/www/mysite/`), it will only be ignored if it is in the base folder
(e.g. `/var/www/themes/css/mysite` will not be skipped if the base
folder is `/var/www/`). The base folder is set when you run the code.
