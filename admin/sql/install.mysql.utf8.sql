CREATE TABLE IF NOT EXISTS `#__alpha_userpoints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `referreid` varchar(160) NOT NULL DEFAULT '',
  `upnid` varchar(25) NOT NULL DEFAULT '',
  `points` float(10,2) NOT NULL DEFAULT '0',
  `max_points` float(10,2) NOT NULL DEFAULT '0.00',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `referraluser` varchar(160) NOT NULL DEFAULT '',
  `referrees` int(11) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',  
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `levelrank` int(11) NOT NULL DEFAULT '0',
  `leveldate` date NOT NULL DEFAULT '0000-00-00',
  `profileviews` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `shareinfos` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`),
  INDEX (referreid),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referreid` varchar(160) NOT NULL DEFAULT '',
  `points` float(10,2) NOT NULL DEFAULT '0',
  `insert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `rule` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `keyreference` varchar(255) NOT NULL DEFAULT '',
  `datareference` text NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`),
  INDEX (referreid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_details_archive` (
  `id` int(11) NOT NULL DEFAULT '0',
  `referreid` varchar(160) NOT NULL DEFAULT '',
  `points` float(10,2) NOT NULL DEFAULT '0',
  `insert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `rule` int(11) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `keyreference` varchar(255) NOT NULL DEFAULT '',
  `datareference` text NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`),
  INDEX (referreid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(100) NOT NULL DEFAULT '',
  `rule_description` varchar(255) NOT NULL DEFAULT '',
  `rule_plugin` varchar(30) NOT NULL DEFAULT '', 
  `plugin_function` varchar(50) NOT NULL DEFAULT '', 
  `access` tinyint(1) NOT NULL DEFAULT '1',
  `component` varchar(50) NOT NULL DEFAULT '',
  `calltask` varchar(50) NOT NULL DEFAULT '',
  `taskid` varchar(50) NOT NULL DEFAULT '',
  `points` float(10,2) NOT NULL DEFAULT '0',
  `points2` float(10,2) NOT NULL DEFAULT '0',
  `percentage` tinyint(1) NOT NULL DEFAULT '0',
  `rule_expire` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sections` text NOT NULL DEFAULT '',
  `categories` text NOT NULL DEFAULT '',
  `content_items` text NOT NULL DEFAULT '',
  `exclude_items` text NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `system` tinyint(1) NOT NULL DEFAULT '0',
  `duplicate` tinyint(1) NOT NULL DEFAULT '0',
  `blockcopy` tinyint(1) NOT NULL DEFAULT '0',
  `autoapproved` tinyint(1) NOT NULL DEFAULT '1',
  `fixedpoints` tinyint(1) NOT NULL DEFAULT '1',
  `category` varchar(2) NOT NULL DEFAULT '',
  `displaymsg` tinyint(1) NOT NULL DEFAULT '1',
  `msg` varchar(255) NOT NULL DEFAULT '',
  `method` tinyint(1) NOT NULL DEFAULT '0',
  `notification` tinyint(1) NOT NULL DEFAULT '1',
  `emailsubject` varchar(255) NOT NULL DEFAULT '',
  `emailbody` text NOT NULL DEFAULT '',
  `emailformat` tinyint(1) NOT NULL DEFAULT '0',
  `bcc2admin` tinyint(1) NOT NULL DEFAULT '0',
  `type_expire_date` int(11) NOT NULL DEFAULT '0',
  `chain` tinyint(1) NOT NULL DEFAULT '0',
  `linkup` int(11) NOT NULL DEFAULT '0',
  `displayactivity` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `couponcode` varchar(255) NOT NULL DEFAULT '',
  `points` float(10,2) NOT NULL DEFAULT '0',
  `expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `category` int(11) NOT NULL DEFAULT '1',
  `printable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_qrcodetrack` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`couponid` int(11) NOT NULL DEFAULT '0',
	`trackid` varchar(30) NOT NULL DEFAULT '',
	`trackdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`country` varchar(50) NOT NULL DEFAULT '',
	`city` varchar(50) NOT NULL DEFAULT '',
	`device` varchar(30) NOT NULL DEFAULT '',
	`ip` varchar(40) NOT NULL DEFAULT '',
	`confirmed` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
      
CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_raffle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `inscription` tinyint(1) NOT NULL DEFAULT '0',
  `rafflesystem` tinyint(1) NOT NULL DEFAULT '0',
  `numwinner` tinyint(1) NOT NULL DEFAULT '1',
  `couponcodeid1` int(11) NOT NULL DEFAULT '0',
  `couponcodeid2` int(11) NOT NULL DEFAULT '0',
  `couponcodeid3` int(11) NOT NULL DEFAULT '0',
  `sendcouponbyemail` tinyint(1) NOT NULL DEFAULT '0',
  `pointstoparticipate` float(10,2) NOT NULL DEFAULT '0',
  `removepointstoparticipate` tinyint(1) NOT NULL DEFAULT '0',
  `pointstoearn1` float(10,2) NOT NULL DEFAULT '0',
  `pointstoearn2` float(10,2) NOT NULL DEFAULT '0',
  `pointstoearn3` float(10,2) NOT NULL DEFAULT '0',
  `raffledate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `winner1` int(11) NOT NULL DEFAULT '0',
  `winner2` int(11) NOT NULL DEFAULT '0',
  `winner3` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `link2download1` varchar(255) NOT NULL DEFAULT '',
  `link2download2` varchar(255) NOT NULL DEFAULT '',
  `link2download3` varchar(255) NOT NULL DEFAULT '',
  `multipleentries` tinyint(1) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_raffle_inscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `raffleid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `ticket` varchar(30) NOT NULL DEFAULT '',
  `referredraw` int(11) NOT NULL DEFAULT '0',
  `inscription` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_levelrank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(50) NOT NULL DEFAULT '',  
  `description` varchar(255) NOT NULL DEFAULT '',
  `levelpoints` float(10,2) NOT NULL DEFAULT '0',
  `typerank` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `gid` int(11) NOT NULL DEFAULT '0',
  `ruleid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `category` int(11) NOT NULL DEFAULT '1',
  `notification` tinyint(1) NOT NULL DEFAULT '1',
  `emailsubject` varchar(255) NOT NULL DEFAULT '',
  `emailbody` text NOT NULL DEFAULT '',
  `emailformat` tinyint(1) NOT NULL DEFAULT '0',
  `bcc2admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_medals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `medal` int(11) NOT NULL DEFAULT '0',
  `medaldate` date NOT NULL DEFAULT '0000-00-00',
  `reason` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_version` (
  `version` varchar(8) NOT NULL DEFAULT 'AUP100'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__alpha_userpoints_template_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT '1',
  `emailsubject` varchar(255) NOT NULL DEFAULT '',
  `emailbody` text NOT NULL DEFAULT '',
  `emailformat` tinyint(1) NOT NULL DEFAULT '1',
  `bcc2admin` tinyint(1) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;