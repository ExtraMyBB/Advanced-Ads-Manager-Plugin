<?php
/*
 * -PLUGIN-----------------------------------------
 *		Name		: Advanced Ads Manager
 * 		Version 	: 1.1.0
 * -TEAM-------------------------------------------
 * 		Developers	: Baltzatu, Mihu
 * -LICENSE----------------------------------------
 *  Copyright (C) 2013  ExtraMyBB.com. All rights reserved.
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

if ( ! defined('IN_MYBB') || ! defined('ADVADSMAN_VERSION')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB and ADVADSMAN_VERSION are defined.');
}

function advadsman_install_settings() 
{
    global $mybb;

    $settings = array();

    $settings[] = array(
        'name' => 'setting_enable',
        'title' => 'Plugin Enabled?',
        'description' => 'Do you want to enable this plugin? (Default : Yes)',
        'optionscode' => 'yesno',
        'value' => 1
    );
    $settings[] = array(
        'name' => 'setting_period',
        'title' => 'Interval of Months',
        'description' => 'Interval of months that system allows users to buy an advertisement (first integer represents the minimum and the second one the maximum). (Default : 1,12)',
        'optionscode' => 'text',
        'value' => '1,12'
    );
    $settings[] = array(
        'name' => 'setting_defaultimgs',
        'title' => 'Default Images',
        'description' => 'You can choose to show a default image if a zone do not have an active advertisement. This setting must have a JSON format. (Default : {"1":{"img":"default_728x80.jpg","width":"728"},"2":{"img":"default_256x256.jpg","width":"256"},"3":{"img":"default_728x80.jpg","width":"728"}})',
        'optionscode' => 'textarea',
        'value' => '{"1":{"img":"default_728x80.jpg","width":"728"},"2":{"img":"default_256x256.jpg","width":"256"},"3":{"img":"default_728x80.jpg","width":"728"}}'
    );
    $settings[] = array(
        'name' => 'setting_defaulturls',
        'title' => 'URL for Default Images',
        'description' => 'When a guest clicks on a default banner, he will be redirected to the following web address : (Default : ' . $mybb->settings['bburl'] . ')',
        'optionscode' => 'text',
        'value' => $mybb->settings['bburl']
    );
    $settings[] = array(
        'name' => 'setting_validext',
        'title' => 'Extensions Allowed',
        'description' => 'Specify image extensions allowed for this plugin. Please use comma as separator.(Default : gif,jpg,jpeg,png)',
        'optionscode' => 'text',
        'value' => 'gif,jpg,jpeg,png'
    );
	$settings[] = array(
        'name' => 'setting_extperiod',
        'title' => 'Before X Days',
        'description' => 'Before how many days above an advertisement expire, the buyer can extend it using same zone and other credits. (Default : 3 days)',
        'optionscode' => 'select\n1=1 day\n3=3 days\n7=7 days',
        'value' => '3'
    );
    $settings[] = array(
        'name' => 'setting_deletead',
        'title' => 'Advertisement Deleted before Expiration',
        'description' => 'What happens when an administrator delete an active advertisement on this board? (Default : Give some money back)',
        'optionscode' => 'select\n0=Do not give money back\n1=Give some money back\n2=Give all money back',
        'value' => '1'
    );
	$settings[] = array(
		'name' => 'setting_pointsys',
		'title' => 'Points System',
		'description' => 'Which points system do you want to integrate with Advanced Ads Manager? If you have another points system you would like to use, choose "Other" and fill in the new options that will appear. (Default : None (Disabled))',
		'optionscode' => 'select
myps=MyPS
newpoints=NewPoints
other=Other
none=None (Disabled)',
		'value' => 'none'
	);
	$settings[] = array(
		'name' => 'setting_pointsysname',
		'title' => 'Custom Points System name',
		'description' => 'If you want to use a points system that is not supported by default, put the name of it here. The name is the same as the name of the file for the plugin in <em>./inc/plugins/</em>. For example, if the plugin file was called <strong>mypoints.php</strong>, you would put <strong>mypoints</strong> into this setting.',
		'optionscode' => 'text',
		'value' => ''
	);
	$settings[] = array(
		'name' => 'setting_pointsyscol',
		'title' => 'Custom Points System database column',
		'description' => 'If you want to use a points system that is not supported by default, put the name of the column from the users table which stores the number of points here. If you are unsure what to put here, please contact the author of the points plugin you want to use.',
		'optionscode' => 'text',
		'value' => ''
	);
	$settings[] = array(
        'name' => 'setting_gae',
        'title' => 'Google Analytics Enabled?',
        'description' => 'Is Google Analytics module active? (Default : No)',
        'optionscode' => 'yesno',
        'value' => 0
    );
    $settings[] = array(
        'name' => 'setting_gaad',
        'title' => 'Google Analytics Account Details',
        'description' => 'Please give some information about your Google Analytics account. Format : <strong>EMAIL ADDRESS,PASSWORD,ACCOUNT NAME</strong>. Use comma as separator. (Default : ,,)',
        'optionscode' => 'text',
        'value' => ',,'
    );
    $settings[] = array(
        'name' => 'setting_gaadtime',
        'title' => 'Google Analytics Days',
        'description' => 'For how many days do you want to retrive statistics about your board? (Default : 7 days)',
        'optionscode' => 'select\n3=3 days\n7=7 days\n10=10 days',
        'value' => '7'
    );

    return $settings;
}
?>