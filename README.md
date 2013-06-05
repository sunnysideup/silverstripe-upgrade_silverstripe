silverstripe-upgrade_silverstripe
=================================

some rough-and-ready tools to help you upgrade to the next version of Silverstripe

 
HEAVILY BASED ON WORK BY: phptek/ss-upgrade.sh (https://gist.github.com/phptek/3902357)
AMONG OTHERS
 
USAGE
=================================
 
1. copy this file to the root folder of a project (althought it can live anywhere)
2. change the main call in the php file (upgrade()) to something that you need - examples below
3. either open the file in your web-browser OR from the command line.
   from the command line, you type: php upgrade-silverstripe.php
 

TEST (FIND) IN CURRENT DIRECTORY (RECURSIVE) FROM 2.4 TO 3.1

upgrade(".". "log.txt", "2.4", "3.0", false);
 

TEST (FIND) IN CURRENT DIRECTORY (RECURSIVE) FROM 3.0 TO 3.1

upgrade(".". "log.txt", "3.0", "3.1", false);
 

TEST (FIND) IN code DIRECTORY (RECURSIVE) FROM 3.0 TO 3.1

upgrade("code". "log.txt", "3.0", "3.1", false);


REPLACE IN CURRENT DIRECTORY (RECURSIVE) FROM 2.4 TO 3.1

upgrade(".". "log.txt", "2.4", "3.0", true);


REPLACE IN CURRENT DIRECTORY (RECURSIVE) FROM 3.0 TO 3.1

upgrade(".". "log.txt", "3.0", "3.1", true);


REPLACE IN code DIRECTORY (RECURSIVE) FROM 3.0 TO 3.1

upgrade("code". "log.txt", "3.0", "3.1", true);
