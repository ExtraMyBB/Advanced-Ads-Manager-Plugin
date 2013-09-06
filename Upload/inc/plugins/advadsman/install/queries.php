<?php
/*
 * ---PLUGIN-----------------------------------
 * Name 	: Advanced Ads Manager
 * Version 	: 1.1.0
 * ---TEAM-------------------------------------
 * Developer: Surdeanu Mihai
 * Tester	: Harald Razvan, Surdeanu Mihai
 * ---COPYRIGHT--------------------------------
 * (C) 2013 ExtraMyBB.com. All rights reserved.
 */
 
// Direct initialization it is not allowed!
if ( ! defined('IN_MYBB') ||  ! defined('ADVADSMAN_VERSION')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB and ADVADSMAN_VERSION are defined.');
}

/*
 * Returns an array of queries that can be executed over database used by forum board.
 */
function advadsman_install_queries($collation)
{
    $queries = array();
    
    $queries['table:advadsman_zones'] = "CREATE TABLE `" . TABLE_PREFIX . "advadsman_zones` (
        `zid` int(12) UNSIGNED NOT NULL auto_increment,
        `name` varchar(32) NOT NULL default '',
        `description` varchar(128) NOT NULL default '',
        `maxdimension` varchar(9) NOT NULL default '256x256',
        `points` decimal(12,2) NOT NULL default '0',
        `posts` int(5) UNSIGNED NOT NULL default '0',
	    `is_ad` smallint(1) UNSIGNED NOT NULL default '0',
        PRIMARY KEY (`zid`)) ENGINE=MyISAM{$collation}";
    
    $queries['table:advadsman_ads'] = "CREATE TABLE `" . TABLE_PREFIX . "advadsman_ads` (
        `aid` int(12) UNSIGNED NOT NULL auto_increment,
        `uid` bigint(30) UNSIGNED NOT NULL default '0',
        `date` bigint(30) UNSIGNED NOT NULL default '0',
        `expire` bigint(30) UNSIGNED NOT NULL default '0',
        `groups` varchar(128) NOT NULL default '',
        `url` varchar(64) NOT NULL default '',
        `urlc` varchar(64) NOT NULL default '',
        `image` varchar(24) NOT NULL default '',
        `imagec` varchar(24) NOT NULL default '',
        `zone` int(12) UNSIGNED NOT NULL default '0',
        `width` int(4) UNSIGNED NOT NULL default '0',
        `views` bigint(30) UNSIGNED NOT NULL default '0',
        `clicks` bigint(30) UNSIGNED NOT NULL default '0',
        `disabled` smallint(1) UNSIGNED NOT NULL default '2',
        PRIMARY KEY (`aid`), INDEX(`expire`)) ENGINE=MyISAM{$collation}";
        
    $queries['table:advadsman_logs'] = "CREATE TABLE `" . TABLE_PREFIX . "advadsman_logs` (
        `lid` bigint(30) UNSIGNED NOT NULL auto_increment,
        `action` varchar(128) NOT NULL default '',
	    `data` text NOT NULL,
        `date` bigint(30) UNSIGNED NOT NULL default '0',
	    `user` varchar(32) NOT NULL default '',
        PRIMARY KEY  (`lid`)) ENGINE=MyISAM{$collation}";
        
    return $queries;  
}

/*
 * Which tables must be present into database in order to have an installed application.
 */
function advadsman_install_check()
{
    $tables = array('advadsman_zones', 'advadsman_ads', 'advadsman_logs');
    
    return $tables;    
}

function advadsman_install_delete()
{
    $rows = array();
      
    $rows['table_exists:drop_table:advadsman_zones'] = '';
    $rows['table_exists:drop_table:advadsman_ads'] = '';
    $rows['table_exists:drop_table:advadsman_logs'] = '';
    
    return $rows;
}
?>