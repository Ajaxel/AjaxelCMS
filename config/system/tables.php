<?php

	$sql = array();
	
	
	$sql['abuse'] = 'CREATE TABLE `[[:DB_PREFIX:]]abuse` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`userid` int(8) unsigned NOT NULL,
`ip` int(8) NOT NULL,
`to` int(8) unsigned DEFAULT NULL,
`reason` varchar(50) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`added` int(10) unsigned NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
	
	$sql['ai'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]ai` (
`id` int(8) unsigned NOT NULL auto_increment,
`question` varchar(255) NOT NULL,
`anc` varchar(50) default NULL,
`catref` varchar(160) default NULL,
`tags` varchar(255) default NULL,
`edited` int(10) unsigned NOT NULL,
`userid` int(8) NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
PRIMARY KEY  (`id`),
KEY `question` (`question`),
KEY `anc` (`anc`),
FULLTEXT KEY `question_2` (`question`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';	

	$sql['ai_answers'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]ai_answers` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(8) unsigned NOT NULL,
`answer` text,
`emotion` tinyint(2) unsigned NOT NULL,
`anc` varchar(150) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

	$sql['ai_talk'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]ai_talk` (
`id` int(8) NOT NULL auto_increment,
`question` varchar(255) NOT NULL,
`answer` varchar(255) NOT NULL,
`answerid` int(8) unsigned NOT NULL,
`setid` int(8) unsigned NOT NULL,
`userid` int(10) unsigned NOT NULL,
`ip` int(10) unsigned NOT NULL,
`added` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

	$sql['cache'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]cache` (
`name` varchar(200) NOT NULL,
`data` mediumtext,
`saved` int(10) DEFAULT \'0\' NOT NULL,
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['category_entries'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]category_entries` (
`catid` int(8) NOT NULL auto_increment,
`catname_en` varchar(255),
`name` varchar(200),
`catref` varchar(100) NOT NULL,
`cnt` int(5) unsigned DEFAULT \'0\' NOT NULL,
`sum` int(5) unsigned DEFAULT \'0\' NOT NULL,
`hidden` enum(\'0\',\'1\') DEFAULT \'0\',
`sort` int(4) DEFAULT \'0\',
`icon` varchar(255),
PRIMARY KEY (`catid`),
KEY `catref` (`catref`),
KEY `name` (`name`),
KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['category_gallery'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]category_gallery` (
`catid` int(8) NOT NULL auto_increment,
`catname_en` varchar(200),
`catref` varchar(100) NOT NULL,
`catalogue` varchar(100) NOT NULL,
`name` varchar(255) NOT NULL,
`cnt` int(5) DEFAULT \'0\',
`totalsum` int(5) DEFAULT \'0\',
`hidden` enum(\'0\',\'1\') DEFAULT \'0\',
`sort` int(4) DEFAULT \'0\',
`icon` varchar(255),
PRIMARY KEY (`catid`),
KEY `catref` (`catref`),
KEY `catalogue` (`catalogue`),
KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['category_product'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]category_product` (
`catid` int(8) NOT NULL auto_increment,
`catname_en` varchar(200),
`catref` varchar(100) NOT NULL,
`catalogue` varchar(100) NOT NULL,
`name` varchar(255) NOT NULL,
`cnt` int(5) DEFAULT \'0\' NOT NULL,
`sum` int(5) DEFAULT \'0\' NOT NULL,
`hidden` enum(\'0\',\'1\') DEFAULT \'0\',
`sort` int(4) DEFAULT \'0\',
`icon` varchar(255),
PRIMARY KEY (`catid`),
KEY `catref` (`catref`),
KEY `catalogue` (`catalogue`),
KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['changes'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]changes` (
`id` int(8) NOT NULL auto_increment,
`table` varchar(30) NOT NULL,
`col` varchar(20) NOT NULL,
`setid` int(8) DEFAULT \'0\' NOT NULL,
`val` varchar(200),
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `table` (`table`, `col`, `setid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['comments'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]comments` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`parentid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`subject` varchar(255) NOT NULL,
`name` varchar(50) NOT NULL,
`email` varchar(50) NOT NULL,
`url` varchar(200) NOT NULL,
`body` text NOT NULL,
`original` text NOT NULL,
`userid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`ip` int(8) DEFAULT \'0\' NOT NULL,
`active` enum(\'1\',\'0\') DEFAULT \'1\' NOT NULL,
`table` varchar(25),
`rate` int(6) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content` (
`id` int(8) unsigned NOT NULL auto_increment,
`menuid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`menuids` varchar(255),
`title_en` varchar(255),
`name` varchar(255) NOT NULL,
`inserts` text NOT NULL,
`cnt` int(5) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) DEFAULT \'0\' NOT NULL,
`sort` int(4) DEFAULT \'0\' NOT NULL,
`options` varchar(255),
`keywords` varchar(255) NOT NULL,
`dated` int(10) DEFAULT \'0\',
`added` int(10) DEFAULT \'0\' NOT NULL,
`comment` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
`comments` int(5) unsigned DEFAULT \'0\' NOT NULL,
`views` int(5) unsigned DEFAULT \'0\' NOT NULL,
`viewtime` decimal(10,2) unsigned NOT NULL,
`rate` int(6) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `menuid` (`menuid`),
KEY `name` (`name`),
FULLTEXT KEY keywords (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_article'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_article` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`body` mediumtext NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`main_photo` varchar(255) NOT NULL,
`views` int(6) unsigned DEFAULT \'0\' NOT NULL,
`comments` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
`bodylist` enum(\'1\',\'0\') DEFAULT \'0\',
`top_story` enum(\'1\',\'0\') DEFAULT \'0\',
`most_read` enum(\'1\',\'0\') DEFAULT \'0\',
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_banner'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_banner` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`body` mediumtext NOT NULL,
`url` varchar(255) NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`main_photo` varchar(255) NOT NULL,
`views` int(6) unsigned DEFAULT \'0\' NOT NULL,
`comments` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_catalogue'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_catalogue` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`catref` varchar(160) NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text,
`body` mediumblob NOT NULL,
`notes` text NOT NULL,
`price` double(10,2) NOT NULL,
`currency` varchar(3) NOT NULL,
`instock` int(8) DEFAULT \'0\' NOT NULL,
`price_old` double(10,2),
`code` varchar(20) NOT NULL,
`options` text,
`added` int(10) DEFAULT \'0\' NOT NULL,
`edited` int(10) DEFAULT \'0\',
`userid` int(8) DEFAULT \'0\',
`main_photo` varchar(255) NOT NULL,
`videos` text NOT NULL,
`views` int(6) unsigned DEFAULT \'0\' NOT NULL,
`comments` enum(\'1\',\'0\') DEFAULT \'0\',
`bestseller` enum(\'1\',\'0\') DEFAULT \'0\' NOT NULL,
`main_page` enum(\'1\',\'0\') DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `catref`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_form'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_form` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`data` mediumblob NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_gallery'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_gallery` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`catref` varchar(160) NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`body` mediumtext,
`url` varchar(255) NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`main_photo` varchar(255) NOT NULL,
`views` int(6) unsigned DEFAULT \'0\' NOT NULL,
`comments` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
`notes` text NOT NULL,
`options` varchar(255) NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
`bodylist` enum(\'1\',\'0\') DEFAULT \'0\' NOT NULL,
`sort` tinyint(3) unsigned NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_gallery_files'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_gallery_files` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`file` varchar(255) NOT NULL,
`width` int(4) unsigned DEFAULT \'0\' NOT NULL,
`height` int(4) unsigned DEFAULT \'0\' NOT NULL,
`media` enum(\'image\',\'audio\',\'flash\',\'video\',\'doc\'),
`mime` varchar(100) NOT NULL,
`size` int(10) DEFAULT \'0\' NOT NULL,
`title_en` varchar(255) NOT NULL,
`descr_en` text NOT NULL,
`copyright` varchar(255) NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` int(5) DEFAULT \'0\' NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `setid` (`setid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_html'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_html` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` mediumblob NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['content_product'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]content_product` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`catref` varchar(160) NOT NULL,
`lang` varchar(2) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text,
`body` mediumtext NOT NULL,
`notes` text NOT NULL,
`price` double(10,2) NOT NULL,
`currency` varchar(3) NOT NULL,
`instock` int(8) DEFAULT \'0\' NOT NULL,
`price_old` double(10,2),
`code` varchar(20) NOT NULL,
`options` text,
`added` int(10) DEFAULT \'0\' NOT NULL,
`edited` int(10) DEFAULT \'0\',
`userid` int(8) DEFAULT \'0\',
`main_photo` varchar(255) NOT NULL,
`views` int(6) unsigned DEFAULT \'0\' NOT NULL,
`comments` enum(\'1\',\'0\') DEFAULT \'0\',
`bestseller` enum(\'1\',\'0\') DEFAULT \'0\' NOT NULL,
`main_page` enum(\'1\',\'0\') DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `rid` (`rid`, `setid`, `catref`, `lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['counts'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]counts` (
`table` varchar(25) NOT NULL,
`query` varchar(32) NOT NULL,
`total` int(9) NOT NULL,
`saved` int(10) NOT NULL,
UNIQUE KEY `table` (`table`,`query`),
KEY `query` (`query`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['entries'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]entries` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`lang` varchar(2) NOT NULL,
`type` varchar(25) NOT NULL,
`menuid` int(6) unsigned DEFAULT \'0\' NOT NULL,
`menuids` varchar(255) NOT NULL,
`catref` varchar(160) NOT NULL,
`name` varchar(255) NOT NULL,
`title` varchar(255) NOT NULL,
`teaser` text NOT NULL,
`descr` text NOT NULL,
`body` mediumtext NOT NULL,
`keywords` varchar(255) NOT NULL,
`main_photo` varchar(255) NOT NULL,
`inserts` varchar(255) NOT NULL,
`dated` int(10) DEFAULT \'0\' NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
`bodylist` enum(\'0\',\'1\') NOT NULL,
`top_story` enum(\'1\',\'0\') DEFAULT \'0\',
`most_read` enum(\'1\',\'0\') DEFAULT \'0\',
`options` text NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
`views` int(6) DEFAULT \'0\' NOT NULL,
`viewtime` decimal(10,2) NOT NULL,
`rate` int(6) DEFAULT \'0\' NOT NULL,
`comment` enum(\'N\',\'Y\') DEFAULT \'N\' NOT NULL,
`comments` int(3) DEFAULT \'0\',
PRIMARY KEY (`id`),
KEY `name` (`name`),
KEY `menuid` (`menuid`),
KEY `rid` (`rid`, `lang`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['entries_files'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]entries_files` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`file` varchar(255) NOT NULL,
`width` int(4) unsigned DEFAULT \'0\' NOT NULL,
`height` int(4) unsigned DEFAULT \'0\' NOT NULL,
`media` enum(\'image\',\'audio\',\'flash\',\'video\',\'doc\'),
`mime` varchar(100) NOT NULL,
`size` int(10) DEFAULT \'0\' NOT NULL,
`title_en` varchar(255) NOT NULL,
`descr_en` text NOT NULL,
`copyright` varchar(255) NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` int(5) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `setid` (`setid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['forms'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]forms` (
`id` int(5) NOT NULL auto_increment,
`setid` int(5) DEFAULT \'0\' NOT NULL,
`name` varchar(50) NOT NULL,
`edited` int(10) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`userid` int(10) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['forum_categories'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]forum_categories` (
`id` int(5) NOT NULL AUTO_INCREMENT,
`position` varchar(200) NOT NULL,
`parentid` int(5) NOT NULL,
`title` varchar(255) DEFAULT NULL,
`descr` text,
`posts` int(8) NOT NULL,
`threads` int(8) NOT NULL,
`sort` int(4) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['forum_posts'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]forum_posts` (
`id` int(5) NOT NULL AUTO_INCREMENT,
`catid` int(6) DEFAULT NULL,
`setid` int(8) DEFAULT NULL,
`descr` mediumtext,
`url` varchar(255) DEFAULT NULL,
`userid` int(8) DEFAULT NULL,
`added` int(10) DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `catid` (`catid`),
KEY `setid` (`setid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['forum_threads'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]forum_threads` (
`id` int(5) NOT NULL AUTO_INCREMENT,
`catid` int(4) DEFAULT NULL,
`title` varchar(255) DEFAULT NULL,
`descr` mediumtext,
`userid` int(8) DEFAULT NULL,
`views` int(6) NOT NULL,
`posts` int(6) NOT NULL,
`added` int(10) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['grid_links'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]grid_links` (
`id` int(8) unsigned NOT NULL auto_increment,
`tags` text,
`url` varchar(255) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
`sort` tinyint(3) NOT NULL,
`is_admin` enum(\'0\',\'1\') DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['grid_articles'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]grid_articles` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`catref` varchar(160) NOT NULL,
`comnum` int(3) NOT NULL,
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`teaser` text,
`added` int(10) unsigned NOT NULL DEFAULT \'0\',
`userid` int(8) NOT NULL DEFAULT \'0\',
`main_photo` varchar(255) NOT NULL,
`body` text NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
`sort` tinyint(3) unsigned NOT NULL,
`is_admin` enum(\'0\',\'1\') NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id`),
KEY `catref` (`catref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			
	$sql['menu'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]menu` (
`id` int(5) NOT NULL auto_increment,
`parentid` int(5) unsigned default \'0\',
`url` varchar(255) default NULL,
`target` varchar(10) NOT NULL,
`display` tinyint(1) NOT NULL default \'0\',
`options` varchar(255) default NULL,
`position` varchar(100) default NULL,
`name` varchar(200) default NULL,
`icon` varchar(255) NOT NULL,
`cnt` int(3) unsigned NOT NULL default \'0\',
`cnt2` int(5) unsigned NOT NULL default \'0\',
`cnt3` int(3) unsigned NOT NULL default \'0\',
`sort` int(3) unsigned NOT NULL default \'0\',
`active` tinyint(1) unsigned NOT NULL,
`submenus` mediumblob NOT NULL,
`userid` int(10) unsigned NOT NULL default \'0\',
`edited` int(10) unsigned NOT NULL default \'0\',
`descr_en` varchar(255) default NULL,
`keywords_en` varchar(255) default NULL,
`title_en` varchar(50) NOT NULL,
`title2_en` varchar(255) NOT NULL,
PRIMARY KEY  (`id`),
KEY `name` (`name`),
KEY `parentid` (`parentid`),
KEY `display` (`display`),
KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['modules'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]modules` (
`id` int(3) unsigned NOT NULL auto_increment,
`table` varchar(20),
`type` varchar(10) NOT NULL,
`title` varchar(50) NOT NULL,
`descr` text,
`icon` varchar(255) NOT NULL,
`active` tinyint(1) unsigned,
`options` text,
`userid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`sort` int(4) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `table` (`table`, `type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['orders'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]orders` (
`id` int(8) NOT NULL auto_increment,
`table` varchar(60) NOT NULL,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`refid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`profile` text NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`email` varchar(200) NOT NULL,
`status` tinyint(1) unsigned NOT NULL,
`price` double(8,2) unsigned NOT NULL,
`active` tinyint(1) unsigned NOT NULL,
`notes` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['poll'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]poll` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned default NULL,
`quiz` varchar(50) NOT NULL,
`name` varchar(50) NOT NULL,
`lang` varchar(2) default NULL,
`main_photo` varchar(200) NOT NULL,
`type` enum(\'s\',\'m\') default \'s\',
`title` varchar(255) default NULL,
`descr` text,
`added` int(10) unsigned default NULL,
`userid` int(8) default NULL,
`passes` int(5) unsigned default NULL,
`answers` int(2) NOT NULL,
`active` tinyint(1) unsigned default NULL,
`sort` int(4) NOT NULL default \'0\',
PRIMARY KEY  (`id`),
KEY `rid` (`rid`,`lang`),
KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['poll_map'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]poll_map` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(8) unsigned NOT NULL,
`answer` text,
`score` int(5) unsigned NOT NULL,
`answers` int(5) unsigned default NULL,
`sort` tinyint(2) unsigned NOT NULL,
PRIMARY KEY  (`id`),
KEY `setid` (`setid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['poll_map'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]poll_results` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`userid` int(8) NOT NULL,
`SID` varchar(32) DEFAULT NULL,
`ip` int(15) NOT NULL,
`added` int(10) unsigned NOT NULL,
`setid` int(8) unsigned NOT NULL,
`result` text,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['product_options'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]product_options` (
`lang` varchar(2) NOT NULL,
`group` varchar(10) NOT NULL,
`name` varchar(255) NOT NULL,
`val` text NOT NULL,
UNIQUE KEY `lang` (`lang`, `group`, `name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['rates'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]rates` (
`id` int(10) unsigned NOT NULL auto_increment,
`setid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`rate` tinyint(2) unsigned NOT NULL,
`rated` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['views'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]views` (
`id` int(8) unsigned NOT NULL auto_increment,
`setid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`viewed` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`ip` int(8) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['visitor_keywords'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]visitor_keywords` (
`id` int(8) NOT NULL auto_increment,
`keyword` varchar(255) NOT NULL,
`engine` varchar(100) NOT NULL,
`url` varchar(255) NOT NULL,
`visited` int(10) DEFAULT \'0\' NOT NULL,
`cnt` int(5) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';


	$sql['visitor_clicks'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]visitor_clicks` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`visit_id` int(8) unsigned NOT NULL,
`location` varchar(255) NOT NULL,
`md5_location` varchar(32) NOT NULL,
`click` int(5) unsigned NOT NULL,
`duration` int(5) unsigned NOT NULL,
`clicked` datetime NOT NULL,
`added` int(10) unsigned NOT NULL,
PRIMARY KEY (`id`),
KEY `md5_location` (`md5_location`),
KEY `visit_id` (`visit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['visitor_referals'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]visitor_referals` (
`id` int(8) unsigned NOT NULL auto_increment,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`visit_id` int(8) unsigned DEFAULT \'0\',
`domain` varchar(255) NOT NULL,
`location` varchar(255) NOT NULL,
`added` int(10) unsigned DEFAULT \'0\',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';


	$sql['visitor_searches'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]visitor_searches` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`key_index` int(2) unsigned NOT NULL,
`value` varchar(50) NOT NULL,
`added` int(10) NOT NULL,
`visit_id` int(8) unsigned DEFAULT NULL,
`click_id` int(8) unsigned NOT NULL,
PRIMARY KEY (`id`),
KEY `key_index` (`key_index`,`visit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['visitor_stats'] = 'CREATE TABLE `[[:DB_PREFIX:]][[:PREFIX:]]visitor_stats` (
`id` int(20) unsigned NOT NULL auto_increment,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`ua` int(6) unsigned DEFAULT \'0\' NOT NULL,
`cameon` datetime DEFAULT \'2000-02-01 00:00:00\' NOT NULL,
`ip` varchar(16),
`os` char(3),
`browser` varchar(2),
`b_version` varchar(6) NOT NULL,
`referer` varchar(128),
`country` varchar(3),
`city` varchar(200) NOT NULL,
`cnt` int(9) unsigned DEFAULT \'0\',
`microtime` double(13,2) unsigned DEFAULT \'0.00\',
`duration` decimal(10,2) unsigned,
`clicks` int(4) unsigned DEFAULT \'0\',
`width` int(4) unsigned DEFAULT \'0\',
`height` int(4) unsigned DEFAULT \'0\',
`timezone` int(3) DEFAULT \'0\' NOT NULL,
`device` enum(\'pc\',\'mobile\',\'tablet\') DEFAULT \'pc\',
PRIMARY KEY (`id`),
KEY `microtime` (`microtime`),
KEY `cameon` (`cameon`),
KEY `ip` (`ip`),
KEY `ua` (`ua`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['countries'] = 'CREATE TABLE `[[:DB_PREFIX:]]countries` (
`code` varchar(2) NOT NULL,
`name` varchar(48),
PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['db'] = 'CREATE TABLE `[[:DB_PREFIX:]]db` (
`k` varchar(50) NOT NULL,
`v` varchar(255) NOT NULL,
`s` varchar(50) NOT NULL,
`t` tinyint(1) NOT NULL,
UNIQUE KEY `k` (`k`, `v`, `t`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;';
	
	$sql['emails'] = 'CREATE TABLE `[[:DB_PREFIX:]]emails` (
`email` varchar(200) NOT NULL,
`name` varchar(200) NOT NULL,
`group` varchar(40) NOT NULL,
`lang` varchar(2) NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`sent` int(10) unsigned DEFAULT \'0\' NOT NULL,
`cnt` int(4) unsigned DEFAULT \'0\',
`read` int(4) unsigned DEFAULT \'0\' NOT NULL,
`clicked` int(4) unsigned DEFAULT \'0\' NOT NULL,
`unsub` enum(\'0\',\'1\') DEFAULT \'0\',
UNIQUE KEY `email` (`email`, `group`),
KEY `group` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['emails_sent'] = 'CREATE TABLE `[[:DB_PREFIX:]]emails_sent` (
`email` varchar(200) NOT NULL,
`group` varchar(40) NOT NULL,
`sent` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) unsigned,
UNIQUE KEY `email` (`email`, `group`),
KEY `group` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['emails_camp'] = 'CREATE TABLE `[[:DB_PREFIX:]]emails_camp` (
`id` int(8) NOT NULL AUTO_INCREMENT,
`total` int(8) unsigned NOT NULL,
`read` int(8) unsigned NOT NULL,
`clicked` int(8) unsigned NOT NULL,
`campaign` varchar(50) NOT NULL,
`added` int(10) unsigned NOT NULL,
`cnt` int(8) unsigned NOT NULL,
`groups` varchar(255) NOT NULL,
`from_email` varchar(50) NOT NULL,
`from_name` varchar(50) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['emails_read'] = 'CREATE TABLE `[[:DB_PREFIX:]]emails_read` (
`email` varchar(50) NOT NULL,
`campaign` varchar(100) NOT NULL,
`read` int(10) unsigned DEFAULT \'0\' NOT NULL,
`clicked` int(10) unsigned DEFAULT \'0\' NOT NULL,
UNIQUE KEY `email` (`email`,`campaign`),
KEY `campaign` (`campaign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['help_board'] = 'CREATE TABLE `[[:DB_PREFIX:]]help_board` (
`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
`parentid` int(5) unsigned NOT NULL DEFAULT \'0\',
`title` varchar(255) NOT NULL,
`descr` mediumtext NOT NULL,
`userid` int(5) unsigned NOT NULL,
`added` int(10) unsigned NOT NULL,
`edited` int(10) unsigned DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['help_contents'] = 'CREATE TABLE `[[:DB_PREFIX:]]help_contents` (
`id` int(6) unsigned NOT NULL AUTO_INCREMENT,
`parentid` int(5) unsigned NOT NULL DEFAULT \'0\',
`title` varchar(255) DEFAULT NULL,
`descr` mediumtext,
`edited` int(10) unsigned DEFAULT NULL,
`sort` smallint(4) unsigned NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;';

	$sql['im'] = 'CREATE TABLE `[[:DB_PREFIX:]]im` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`from_id` int(8) unsigned DEFAULT \'0\',
`to_id` int(8) unsigned DEFAULT \'0\',
`from_ip` int(8) unsigned DEFAULT \'0\',
`to_ip` int(8) unsigned DEFAULT \'0\',
`folder` varchar(50) DEFAULT NULL,
`old_folder` varchar(50) DEFAULT NULL,
`anonym` blob,
`sent` int(10) unsigned DEFAULT \'0\',
`moved` int(10) unsigned DEFAULT \'0\',
`added` int(10) NOT NULL,
`total` int(7) unsigned DEFAULT \'0\',
`total_new` int(4) unsigned DEFAULT \'0\',
`typing` int(5) unsigned DEFAULT \'0\',
`win` enum(\'Y\',\'N\') DEFAULT \'N\',
`deleted` enum(\'Y\',\'N\') NOT NULL DEFAULT \'N\',
PRIMARY KEY (`id`),
KEY `from_id` (`from_id`,`to_id`,`from_ip`,`to_ip`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['im_set'] = 'CREATE TABLE `[[:DB_PREFIX:]]im_set` (
`userid` int(8) DEFAULT \'0\' NOT NULL,
`sound` enum(\'Y\',\'N\') DEFAULT \'Y\' NOT NULL,
`online` enum(\'Y\',\'N\') DEFAULT \'Y\' NOT NULL,
`on` enum(\'N\',\'Y\') DEFAULT NULL,
`expanded` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
`user_left` int(10) DEFAULT \'0\',
PRIMARY KEY (`userid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['im_sub'] = 'CREATE TABLE `[[:DB_PREFIX:]]im_sub` (
`id` int(8) NOT NULL auto_increment,
`setid` int(8) DEFAULT \'0\' NOT NULL,
`sent` int(10) DEFAULT \'0\',
`msg` text,
`read` enum(\'Y\',\'N\') DEFAULT \'N\' NOT NULL,
PRIMARY KEY (`id`),
KEY `setid` (`setid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['ipblocker'] = 'CREATE TABLE `[[:DB_PREFIX:]]ipblocker` (
`id` int(8) NOT NULL auto_increment,
`ip` varchar(20) NOT NULL,
`ip_from` int(8) DEFAULT \'0\' NOT NULL,
`ip_to` int(8) DEFAULT \'0\' NOT NULL,
`reason` varchar(255) NOT NULL,
`blocked` int(10) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['lang'] = 'CREATE TABLE `[[:DB_PREFIX:]]lang` (
`id` int(8) unsigned NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`template` varchar(20) NOT NULL,
`text_en` text NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['langs'] = 'CREATE TABLE `[[:DB_PREFIX:]]langs` (
`code` char(3) NOT NULL,
`name` char(20) NOT NULL,
`u_name` text,
`short` varchar(3) NOT NULL,
PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['log'] = 'CREATE TABLE `[[:DB_PREFIX:]]log` (
`id` int(8) NOT NULL auto_increment,
`setid` int(8) DEFAULT \'0\' NOT NULL,
`table` varchar(40),
`action` tinyint(1) unsigned NOT NULL,
`template` varchar(25) NOT NULL,
`title` varchar(255) NOT NULL,
`changes` int(2) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`data` mediumblob,
PRIMARY KEY (`id`),
KEY `action` (`action`),
KEY `table` (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['logins'] = 'CREATE TABLE `[[:DB_PREFIX:]]logins` (
`userid` int(8) NOT NULL default \'0\',
`login` varchar(50) default NULL,
`ip` int(8) NOT NULL default \'0\',
`logged` int(10) unsigned default \'0\',
`success` enum(\'0\',\'1\',\'2\',\'3\',\'4\') NOT NULL default \'0\',
KEY `ip` (`ip`),
KEY `success` (`success`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['mail'] = 'CREATE TABLE `[[:DB_PREFIX:]]mail` (
`id` int(8) unsigned NOT NULL auto_increment,
`rid` int(8) unsigned NOT NULL,
`from_id` int(10) DEFAULT \'0\' NOT NULL,
`to_id` int(10) DEFAULT \'0\' NOT NULL,
`subject` varchar(255) NOT NULL,
`body` text NOT NULL,
`body_conv` mediumblob NOT NULL,
`read` int(10) DEFAULT \'0\' NOT NULL,
`folder` enum(\'INBOX\',\'SENT\',\'ARCHIVE\') NOT NULL,
`hook` varchar(200) NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`replied` int(10) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
KEY `to_id` (`to_id`),
KEY `from_id` (`from_id`),
KEY `folder` (`folder`),
KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$sql['mail_templates'] = 'CREATE TABLE `[[:DB_PREFIX:]]mail_templates` (
`id` int(8) unsigned NOT NULL AUTO_INCREMENT,
`userid` int(8) NOT NULL,
`name` varchar(255) NOT NULL,
`subject` varchar(255) NOT NULL,
`body` mediumblob NOT NULL,
`type` enum(\'\',\'Q\',\'F\') NOT NULL,
`added` int(10) NOT NULL,
PRIMARY KEY (`id`),
KEY `userid` (`userid`),
KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['orders2'] = 'CREATE TABLE `[[:DB_PREFIX:]]orders2` (
`id` int(11) unsigned NOT NULL auto_increment,
`userid` int(8) DEFAULT \'0\',
`ip` int(10) DEFAULT \'0\' NOT NULL,
`sellerid` int(8) unsigned DEFAULT \'0\',
`price_total` double(10,2) unsigned DEFAULT \'0.00\',
`currency` varchar(3),
`quantity_total` int(5) unsigned DEFAULT \'0\',
`price_shipping` double(5,2) DEFAULT \'0.00\',
`price_tax` decimal(5,2),
`price_discount` decimal(5,2),
`price_basket` decimal(10,2),
`weight` int(6) unsigned DEFAULT \'0\',
`status` tinyint(1) DEFAULT \'0\',
`bank` varchar(30),
`paidby` varchar(255),
`paidto` varchar(255),
`use_shipping` enum(\'1\',\'0\') DEFAULT \'0\',
`shipping_method` varchar(20),
`ship_date` datetime DEFAULT \'0000-00-00 00:00:00\',
`homephone` varchar(30),
`cellphone` varchar(30),
`country` varchar(50),
`city` varchar(50),
`state` varchar(25) NOT NULL,
`zip` varchar(8),
`address` varchar(255),
`address2` varchar(255),
`email` varchar(50),
`salutation` varchar(4),
`firstname` varchar(50),
`lastname` varchar(100),
`company` varchar(255),
`reg_nr` varchar(30),
`vat_nr` varchar(30),
`export` enum(\'0\',\'1\'),
`info` text NOT NULL,
`notes` text NOT NULL,
`msg` varchar(255),
`ordered` int(10) unsigned DEFAULT \'0\',
`paid` int(10) unsigned DEFAULT \'0\' NOT NULL,
`accepted` int(10) unsigned DEFAULT \'0\' NOT NULL,
`sent` int(10) unsigned DEFAULT \'0\' NOT NULL,
`cancelled` int(10) unsigned DEFAULT \'0\' NOT NULL,
`refunded` int(10) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['orders2_basket'] = 'CREATE TABLE `[[:DB_PREFIX:]]orders2_basket` (
`SID` varchar(40),
`userid` int(8) DEFAULT \'0\',
`sellerid` int(8) unsigned DEFAULT \'0\',
`itemid` int(8) unsigned DEFAULT \'0\',
`table` varchar(25),
`title` varchar(100),
`quantity` int(5) unsigned DEFAULT \'0\',
`price` double(10,2) unsigned DEFAULT \'0.00\',
`currency` varchar(3),
`options` varchar(255),
`weight` varchar(6),
`type` tinyint(1) unsigned DEFAULT \'0\',
`added` int(10) unsigned DEFAULT \'0\',
UNIQUE KEY `SID` (`SID`, `itemid`, `table`, `options`, `type`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['orders2_map'] = 'CREATE TABLE `[[:DB_PREFIX:]]orders2_map` (
`id` int(22) unsigned NOT NULL auto_increment,
`userid` int(8) DEFAULT \'0\',
`sellerid` int(8) unsigned DEFAULT \'0\',
`orderid` int(11) unsigned DEFAULT \'0\',
`itemid` int(8) unsigned DEFAULT \'0\',
`table` varchar(25),
`quantity` int(6) unsigned DEFAULT \'0\',
`price` double(10,2) unsigned DEFAULT \'0.00\',
`currency` varchar(3),
`title` varchar(255),
`type` tinyint(1) unsigned,
`options` varchar(255),
`status` enum(\'0\',\'1\',\'2\') DEFAULT \'0\',
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['orders2_msg'] = 'CREATE TABLE `[[:DB_PREFIX:]]orders2_msg` (
`id` int(8) NOT NULL auto_increment,
`orderid` int(11) DEFAULT \'0\' NOT NULL,
`message` text NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`sent` int(10) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['sessions'] = 'CREATE TABLE `[[:DB_PREFIX:]]sessions` (
`SID` varchar(32) NOT NULL,
`expiration` int(10) DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`groupid` tinyint(2),
`host` varchar(20),
`location` varchar(255) NOT NULL,
`temp` varchar(200),
`views` text NOT NULL,
`viewtime` varchar(30) NOT NULL,
`clicks` int(6) DEFAULT \'0\' NOT NULL,
`cameon` decimal(13,2),
`visit_id` int(20) DEFAULT \'0\',
`template` varchar(20) DEFAULT \'\',
`sessvalue` text NOT NULL,
PRIMARY KEY (`SID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['settings'] = 'CREATE TABLE `[[:DB_PREFIX:]]settings` (
`template` varchar(30) NOT NULL,
`name` varchar(50),
`val` text NOT NULL,
UNIQUE KEY `template` (`template`, `name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['snippets'] = 'CREATE TABLE `[[:DB_PREFIX:]]snippets` (
`id` int(8) unsigned NOT NULL auto_increment,
`category` varchar(50) NOT NULL,
`name` varchar(50) NOT NULL,
`title` varchar(255) NOT NULL,
`source` text NOT NULL,
`added` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['templates'] = 'CREATE TABLE `[[:DB_PREFIX:]]templates` (
`name` varchar(30) NOT NULL,
`prefix` varchar(20) NOT NULL,
`engine` varchar(30) DEFAULT NULL,
`title` varchar(50) NOT NULL,
`descr` text,
`active` tinyint(1) DEFAULT \'0\',
`options` text NOT NULL,
`added` int(10) NOT NULL,
`sort` int(5) NOT NULL,
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['users'] = 'CREATE TABLE `[[:DB_PREFIX:]]users` (
`id` int(8) NOT NULL auto_increment,
`login` varchar(100) NOT NULL,
`groupid` int(2) unsigned DEFAULT \'0\',
`classid` tinyint(2) unsigned NOT NULL,
`email` varchar(255) NOT NULL,
`main_photo` varchar(255) NOT NULL,
`password` varchar(40) NOT NULL,
`registered` int(10) unsigned DEFAULT \'0\' NOT NULL,
`logged` int(10) unsigned DEFAULT \'0\' NOT NULL,
`last_logged` int(10) unsigned DEFAULT \'0\' NOT NULL,
`temp_time` int(10) unsigned DEFAULT \'0\',
`last_click` int(10) unsigned DEFAULT \'0\' NOT NULL,
`edited` int(10) unsigned DEFAULT \'0\' NOT NULL,
`userid` int(8) unsigned DEFAULT \'0\' NOT NULL,
`ip` int(15) DEFAULT \'0\' NOT NULL,
`active` tinyint(1) unsigned DEFAULT \'0\',
`code` varchar(32) NOT NULL,
`notes` text NOT NULL,
`status` tinyint(1) unsigned DEFAULT \'0\',
`facebook` int(20) unsigned DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `login` (`login`),
KEY `groupid` (`groupid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';
	
	$sql['users_profile'] = 'CREATE TABLE `[[:DB_PREFIX:]]users_profile` (
`setid` int(8) DEFAULT \'0\',
`firstname` varchar(50) NOT NULL,
`lastname` varchar(50) NOT NULL,
`company` varchar(255) NOT NULL,
`reg_nr` varchar(30) NOT NULL,
`vat_nr` varchar(30) NOT NULL,
`phone` varchar(50) NOT NULL,
`fax` varchar(50) NOT NULL,
`msn` varchar(100) NOT NULL,
`skype` varchar(100) NOT NULL,
`country` varchar(3) NOT NULL,
`state` varchar(100) NOT NULL,
`city` varchar(100) NOT NULL,
`district` varchar(100) NOT NULL,
`zip` varchar(10) NOT NULL,
`street` varchar(255) NOT NULL,
`street2` varchar(255) NOT NULL,
`gender` enum(\'\',\'M\',\'F\') NOT NULL,
`dob` datetime NOT NULL,
`www` varchar(255) NOT NULL,
`about` text NOT NULL,
`interests` text NOT NULL,
`relation` text NOT NULL,
`signature` text NOT NULL,
`signature_conv` text NOT NULL,
`options` mediumblob,
`subject` varchar(255) NOT NULL,
`message` text NOT NULL,
`money` double(8,2) NOT NULL,
UNIQUE KEY `userid` (`setid`),
KEY `city` (`city`),
KEY `state` (`state`),
KEY `country` (`country`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;';

	$sql['users_friends'] = 'CREATE TABLE `[[:DB_PREFIX:]]users_friends` (
`id` int(8) NOT NULL AUTO_INCREMENT,
`setid` int(8) NOT NULL,
`userid` int(8) NOT NULL,
`blocked` enum(\'N\',\'Y\') NOT NULL,
`confirmed` enum(\'\',\'N\',\'Y\') NOT NULL,
`added` int(10) NOT NULL,
`hook` varchar(255) NOT NULL,
`price` decimal(10,2) NOT NULL,
`currency` varchar(3) NOT NULL,
PRIMARY KEY (`id`)
)';

	$sql['users_transfers'] = 'CREATE TABLE `[[:DB_PREFIX:]]users_transfers` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`price` double(12,2) NOT NULL,
`title` varchar(255) NOT NULL,
`currency` varchar(3) DEFAULT NULL,
`userid` int(8) unsigned NOT NULL,
`added` int(10) unsigned unsigned NOT NULL,
`account` int(8) unsigned NOT NULL,
KEY `userid` (`userid`),
PRIMARY KEY (`id`)
)';

	$sql['votes'] = 'CREATE TABLE `[[:DB_PREFIX:]]votes` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`setid` int(10) unsigned NOT NULL DEFAULT \'0\',
`table` varchar(20) NOT NULL,
`rate` tinyint(2) unsigned NOT NULL,
`rated` int(10) NOT NULL DEFAULT \'0\',
`userid` int(8) NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id`),
KEY `setid` (`setid`,`table`),
KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

	$sql['vars'] = 'CREATE TABLE `[[:DB_PREFIX:]]vars` (
`template` varchar(30) NOT NULL,
`name` varchar(255) NOT NULL,
`val_en` text NOT NULL,
PRIMARY KEY (`template`, `name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	
	$sql['wall'] = 'CREATE TABLE `[[:DB_PREFIX:]]wall` (
`id` int(8) unsigned NOT NULL auto_increment,
`to_user` int(8) DEFAULT \'0\' NOT NULL,
`from_user` int(8) DEFAULT \'0\' NOT NULL,
`message` text NOT NULL,
`added` int(10) DEFAULT \'0\' NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	// functions
	/*
	$sql['func_GetDistance'] = 'DELIMITER $$

DROP FUNCTION IF EXISTS `GetDistance`$$

CREATE FUNCTION `GetDistance`(coordinate1 VARCHAR(120), coordinate2 VARCHAR(120))
	RETURNS VARCHAR(120)
BEGIN
	DECLARE pos_comma1, pos_comma2 INT;
	DECLARE lon1, lon2, lat1, lat2, distance DECIMAL(12,8);
	
	select locate(\',\', coordinate1) into pos_comma1;
	select locate(\',\', coordinate1, pos_comma1+1) into pos_comma2;
	select CAST(substring(coordinate1, 1, pos_comma1-1) as DECIMAL(12,8)) into lon1;
	select CAST(substring(coordinate1, pos_comma1+1, pos_comma2-pos_comma1-1) as DECIMAL(12,8)) into lat1;
	
	select locate(\',\', coordinate2) into pos_comma1;
	select locate(\',\', coordinate2, pos_comma1+1) into pos_comma2;
	select CAST(substring(coordinate2, 1, pos_comma1-1) as DECIMAL(12,8)) into lon2;
	select CAST(substring(coordinate2, pos_comma1+1, pos_comma2-pos_comma1-1) as DECIMAL(12,8)) into lat2;
	
	select ((ACOS(SIN(lat1 * PI() / 180) * SIN(lat2 * PI() / 180) + COS(lat1 * PI() / 180) * COS(lat2 * PI() / 180) * COS((lon1 - lon2) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) into distance;
	RETURN distance;

END$$

DELIMITER;';
	*/

return $sql;