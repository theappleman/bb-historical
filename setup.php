<?
/*
	This is just a layout for the config file: change this now!
	Rename this file userconf.php as well
*/

/* PDO settings */
// $connstr = "mysql:host=$host;dbname=$database"; $db_user ""; $db_pass =""; // MySQL example
$connstr = "sqlite:db.db"; // SQLite; but be in a world-writable folder
$db_prefix = ""; // prefix of database tables

$hurl = ""; // URI to bb installation. Requires protocol (e.g. http://) or abs. path.
$link = false; // Set true to use mod_rewrite

$sitename = "Sitename - $cat"; // string to use at the top of each page
$datefmt = "Y-m-d H:i:s"; // Another valid format is "c".
$fuzzy = true; // turn on fuzzy dates. not an expensive operation, there is little reason to have this off
$style = "style"; // stylesheet name
$out = 15; // The number of items to take out of the database

$menu = array(/*'Link1'=>'http://url.to/link/1'*/); // array containing links for the main menu
$snapcode = ""; // Your snap shots, just the number

$nochat = array('comments'); // categories that will not be easily postable to.

$uploaddir = getcwd()."/uploaded/"; // world writable folder for uploaded images. System absolute path - past the $hurl. Needs trailing slash
// default is a directory below the current source directory

// thumbnail max sizes DO NOT SET $height TO 0 (ZERO)
$width = 320;
$height = 240;

// text replacements
$patterns = array(/*''=>''*/);

// the following variables
// are used for header bits and the RSS feed
$email = "";
$meta_desc = NULL;
$meta_author = NULL;
$meta_copyright = NULL;
$meta_keywords = NULL;

/*
INSTALLATION
Create the world-writeable uploaded folder. It must be named `uploaded`
Create the world-writeable cache folder.
You may want to make a .htaccess rule for Indexes, if wanted.

-- MySQL instructions to install the data table
-- If using $db_prefix, change data and transactions to `${db_prefix}data` and `${db_prefix}transactions` respectively.

CREATE TABLE IF NOT EXISTS `data` (
  `id` bigint(4) unsigned NOT NULL auto_increment COMMENT 'Unique ID for the entry',
  `title` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'Headline, title or name',
  `date` datetime NOT NULL COMMENT 'Visible date of the entry',
  `lastupd` datetime NOT NULL COMMENT 'Date of last comment',
  `intro` text collate utf8_unicode_ci NOT NULL COMMENT 'Brief introduction of the entry',
  `image` varchar(256) collate utf8_unicode_ci default NULL COMMENT 'Filename of attached image',
  `section` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'Section which the entry falls under',
  `commentable` tinyint(1) NOT NULL COMMENT 'Commentable flag, allows items to have comments',
  `commentref` bigint(4) unsigned default NULL COMMENT 'ID of entry that the comment belongs to',
  `sticky` tinyint(1) NOT NULL default '1' COMMENT 'sticky flag, shows at the top',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Main table for the website system';

CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_key` varchar(24) default NULL,
  UNIQUE KEY `uki` (`transaction_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT="Transaction key table";

*/

?>
