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
        'title' => 'Plugin enabled?',
        'description' => 'Choose Yes if you really wants this plugin to do his work.(Default : Yes)',
        'optionscode' => 'yesno',
        'value' => 1
    );
    $settings[] = array(
        'name' => 'setting_gae',
        'title' => 'Stats module enabled?',
        'description' => 'Is Google Analytics module active?(Default : No)',
        'optionscode' => 'yesno',
        'value' => 0
    );
    $settings[] = array(
        'name' => 'setting_gaad',
        'title' => 'Google Analytics account details',
        'description' => 'Please give some information about your Google Analytics account. Format : <strong>EMAIL ADDRESS,PASSWORD,ACCOUNT NAME</strong>. Use comma as separator.(Default : ,,)',
        'optionscode' => 'text',
        'value' => ',,'
    );
    $settings[] = array(
        'name' => 'setting_gaadtime',
        'title' => 'Google Analytics stats days',
        'description' => 'For how many days do we retrive stats from <i>Google Analytics</i>? (Default : 7 days)',
        'optionscode' => 'select\n3=3 days\n7=7 days\n10=10 days',
        'value' => '7'
    );
    $settings[] = array(
        'name' => 'setting_period',
        'title' => 'Number of months avaible to buy',
        'description' => 'Interval of months that system allow user to buy an advertisement. (Default : 1,12)',
        'optionscode' => 'text',
        'value' => '1,12'
    );
    $settings[] = array(
        'name' => 'setting_extperiod',
        'title' => 'Before X days',
        'description' => 'Before how many days above an advertisement expire, the buyer can extend it using same zone and other credits. (Default : 3 days)',
        'optionscode' => 'select\n1=1 day\n3=3 days\n7=7 days',
        'value' => '3'
    );
    $settings[] = array(
        'name' => 'setting_defaultimgs',
        'title' => 'Default banners',
        'description' => 'You can choose to show a default banner if a zone don`t have an active advertisement. Moreover for each zone you can attach a default image. This setting must have a JSON format. (Default : {"1":{"img":"default_728x80.jpg","width":"728"},"2":{"img":"default_256x256.jpg","width":"256"},"3":{"img":"default_728x80.jpg","width":"728"}})',
        'optionscode' => 'textarea',
        'value' => '{"1":{"img":"default_728x80.jpg","width":"728"},"2":{"img":"default_256x256.jpg","width":"256"},"3":{"img":"default_728x80.jpg","width":"728"}}'
    );
    $settings[] = array(
        'name' => 'setting_defaulturls',
        'title' => 'URL for default banners',
        'description' => 'When a guest clicks on a default banner, he will be redirected to the following address : (Default : ' . $mybb->settings['bburl'] . ')',
        'optionscode' => 'text',
        'value' => $mybb->settings['bburl']
    );
    $settings[] = array(
        'name' => 'setting_validext',
        'title' => 'Image extensions allowed',
        'description' => 'Specify image extensions allowed for this plugin. Please use comma as separator.(Default : gif,jpg,jpeg,png)',
        'optionscode' => 'text',
        'value' => 'gif,jpg,jpeg,png'
    );
    $settings[] = array(
        'name' => 'setting_deletead',
        'title' => 'Active advertisement deleted',
        'description' => 'What happens when an administrator delete an active advertisement on this board?(Default : Give some money back)',
        'optionscode' => 'select\n0=Do not give money back\n1=Give some money back\n2=Give all money back',
        'value' => '1'
    );

    return $settings;
}
?>