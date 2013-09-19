<?php
/*
 * -PLUGIN-----------------------------------------
 *	Name		: Advanced Ads Manager
 * 	Version 	: 1.1.0
 * -TEAM-------------------------------------------
 * 	Developers	: Baltzatu, Mihu
 * -LICENSE----------------------------------------
 *  Copyright (C) 2013 ExtraMyBB.com. All rights reserved.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
function upgrade_100_110_run()
{   
	global $db;
	
    try {
		/*
		 * DATABASE CHANGES
		 */
		 
		// delete old settings (bug)
		$db->delete_query('settings', "name LIKE 'advadsman_settings_%'");
		
		// drop "groups" column from "advadsman_ads" table (optimization)
		if ($db->field_exists('groups', 'advadsman_ads')) {
			$db->drop_column('advadsman_ads', 'groups');
		}
		
		// table changes
		$db->modify_column('advadsman_zones', 'name', "varchar(32) NOT NULL default ''");
		$db->modify_column('advadsman_zones', 'description', "varchar(128) NOT NULL default ''");
		$db->modify_column('advadsman_zones', 'maxdimension', "varchar(9) NOT NULL default '256x256'");
		$db->modify_column('advadsman_zones', 'posts', "int(3) UNSIGNED NOT NULL default '0'");
		
		$db->modify_column('advadsman_ads', 'image', "varchar(24) NOT NULL default ''");
		$db->modify_column('advadsman_ads', 'imagec', "varchar(24) NOT NULL default ''");
		
		$db->modify_column('advadsman_logs', 'user', "varchar(32) NOT NULL default ''");
		
		/*
		 * TEMPLATE CHANGES
		 */
		 
		// remove old zone tags (feature)
        require_once MYBB_ROOT . 'inc/adminfunctions_templates.php';
        find_replace_templatesets('header', '#' . preg_quote('{advadsman_z1}') . '#', '', 0);
        find_replace_templatesets('footer', '#' . preg_quote('{advadsman_z3}') . '#', '', 0);
		
        return TRUE;
    } catch (Exception $e) {
        return FALSE;
    }
}

?>
