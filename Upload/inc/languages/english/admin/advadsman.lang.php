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

$l['advadsman_mod_title'] = 'Advanced Ads Manager';
$l['advadsman_home'] = ' Inactive Ads ({1})';

$l['advadsman_mod_confirm'] = 'Confirm action';
$l['advadsman_mod_confirm_desc'] = 'Are you sure that you want to do this action?';

$l['advadsman_error_unknown'] = 'An unknown error occured!';
$l['advadsman_error_invalid'] = 'Invalid entry!';

$l['advadsman_buttons_yes'] = 'Yes';
$l['advadsman_buttons_no'] = 'No';

$l['advadsman_buttons_submit'] = 'Submit';
$l['advadsman_buttons_reset'] = 'Reset';

$l['advadsman_permissions_canmanage'] = 'Can manage "Advanced Ads Manager"?';  

$l['advadsman_infos'] = 'Information';
$l['advadsman_infos_desc'] = 'This page contains some stats for "Advanced Ads Manager" plugin.';
$l['advadsman_infos_text'] = '<h3>Stats...</h3><ul type="square"><li>until now <strong>{1} NewPoints credits</strong> were spent in order to buy some advertisements on your board;</li><li>a number of <strong>{2} zones</strong> don`t have an advertisement associated (there are <strong>{3} zones</strong> created);</li><li><strong>{4}</strong> advertisements expire soon (next week) from a total of <strong>{5} active ads</strong>;</li><li><strong>{6} advertisement requests</strong> need the first approvement (not included here the approvals necessary after editing);</li></ul><p class="notice" style="text-align: justify">Press on "Rebuild cache?" button if you deleted all or partial cache data of your board. Using this simple method there are solved a lot of problems with advertisements who are not displayed.</p>';
$l['advadsman_infos_text1'] = '<p class="notice" style="text-align: justify">In the following table you can see the permissions of "advadsman" directory, where banner images are uploaded. You must be sure that this directory have at least 0755 - CHMOD.</p>';
$l['advadsman_infos_directory'] = 'Directory';
$l['advadsman_infos_current'] = 'Current';
$l['advadsman_infos_recommended'] = 'Recommended';
$l['advadsman_infos_table'] = 'Permissions for directories';

$l['advadsman_rebuild_cache'] = 'Do you want to rebuild plugin cache?';
$l['advadsman_rebuild_template'] = 'Check template changes for current theme?';
$l['advadsman_rebuild_success'] = 'Cache successfully rebuild!';
$l['advadsman_rebuild_temp_success'] = 'No problem detected for your current theme!';
$l['advadsman_rebuild_error'] = 'An error occurred on cache rebuild process!';
$l['advadsman_rebuild_temp_error'] = 'An error occurred on template process check!';

$l['advadsman_infos_credit'] = 'Team';
$l['advadsman_infos_credit_name'] = 'Name';
$l['advadsman_infos_credit_role'] = 'Role';
$l['advadsman_infos_credit_website'] = 'Website';

$l['advadsman_zones'] = 'Zones';
$l['advadsman_zones_desctab'] = 'Here you can manage all advertisement zones.';
$l['advadsman_zones_name'] = 'Zone name';
$l['advadsman_zones_desc'] = 'Description';
$l['advadsman_zones_price'] = 'Price per month';
$l['advadsman_zones_code'] = 'Code';
$l['advadsman_zones_has'] = 'Has space?';
$l['advadsman_zones_actions'] = 'Options';
$l['advadsman_zones_actions_edit'] = 'Edit';
$l['advadsman_zones_actions_delete'] = 'Delete';
$l['advadsman_zones_nozones'] = 'No zones found on your database.';

$l['advadsman_zones_add_form'] = 'Add zone';
$l['advadsman_zones_add_name'] = 'Zone name';
$l['advadsman_zones_add_name_desc'] = 'Enter the name of the zone. Must be an unique one!';
$l['advadsman_zones_add_description'] = 'Description';
$l['advadsman_zones_add_description_desc'] = 'Enter a description for this zone.';
$l['advadsman_zones_add_maxdimension'] = 'Maximum dimension';
$l['advadsman_zones_add_maxdimension_desc'] = 'Maximum size of banners for this zone. Format : width x height. Example : 468x60.';
$l['advadsman_zones_add_points'] = 'Price / month';
$l['advadsman_zones_add_points_desc'] = 'How many NewPoints credits are taken for each month?';
$l['advadsman_zones_add_error1'] = 'Name of the area, the maximum size or field points are filled incorrectly!';
$l['advadsman_zones_add_error2'] = 'A zone with the same name already exists!';
$l['advadsman_zones_add_loga'] = 'New zone';
$l['advadsman_zones_add_logd'] = 'A new zone has been added successfully (ID : {1}).';
$l['advadsman_zones_add_success'] = 'A new zone has been added successfully.';

$l['advadsman_zones_edit_form'] = 'Edit zone';
$l['advadsman_zones_edit_name'] = 'Zone name';
$l['advadsman_zones_edit_name_desc'] = 'Enter the name of the zone. Leave empty if you don`t want to change this field!';
$l['advadsman_zones_edit_description'] = 'Description';
$l['advadsman_zones_edit_description_desc'] = 'Enter a description for this zone. Leave empty if you don`t want to change this field!';
$l['advadsman_zones_edit_maxdimension'] = 'Maximum dimension';
$l['advadsman_zones_edit_maxdimension_desc'] = 'Maximum size of banners for this area. Format : width x height. Leave empty if you don`t want to change this field!';
$l['advadsman_zones_edit_points'] = 'Price / month';
$l['advadsman_zones_edit_points_desc'] = 'How many NewPoints credits are taken for each month? Leave empty if you don`t want to change this field!';
$l['advadsman_zones_edit_posts'] = 'X Posts';
$l['advadsman_zones_edit_posts_desc'] = 'After how many posts an advertisement is displayed on <strong>Postbit-ului</strong> zone. Leave empty if you don`t want to change this field!';
$l['advadsman_zones_edit_invalid'] = 'Invalid ad zone.';
$l['advadsman_zones_edit_loga'] = 'Zone edited';
$l['advadsman_zones_edit_logd'] = 'The selected zone (ID : {1}) has been edited successfully.';
$l['advadsman_zones_edit_success'] = 'The selected zone has been edited successfully.';

$l['advadsman_zones_delete_hasads'] = 'First you need to delete all advertisements associated with this zone, and after that you can delete it!';
$l['advadsman_zones_delete_loga'] = 'Zone deleted';
$l['advadsman_zones_delete_logd'] = 'The selected zone (ID : {1}) has been deleted successfully.';
$l['advadsman_zones_delete_success'] = 'The selected zone has been deleted successfully.';

$l['advadsman_ads'] = 'Advertisements';  
$l['advadsman_ads_desc'] = 'Here you can manage advertisements.';
$l['advadsman_ads_user'] = 'Username';
$l['advadsman_ads_create'] = 'Created on';
$l['advadsman_ads_expire'] = 'Expires on';
$l['advadsman_ads_url'] = 'Website URL';
$l['advadsman_ads_active'] = 'Approved?';
$l['advadsman_ads_options'] = 'Options';
$l['advadsman_ads_actions'] = 'Actions';
$l['advadsman_ads_actions_edit'] = 'Edit';
$l['advadsman_ads_actions_fapprove'] = 'Approve edit';
$l['advadsman_ads_actions_approve'] = 'Approve';
$l['advadsman_ads_actions_disapprove'] = 'Disapprove';
$l['advadsman_ads_actions_delete'] = 'Delete';
$l['advadsman_ads_noresults'] = 'No advertisements found.';
$l['advadsman_ads_activeads'] = 'Active Ads';

$l['advadsman_ads_status_text'] = 'This advertisement has the following features : {1} Are you sure that you want to approve it?';
$l['advadsman_ads_approve_text_features'] = '<li>Chosen zone "{1}";</li><li>Website URL "{2}";</li><li>Banner image <a href="{3}" target="_blank">here</a>;</li>';
$l['advadsman_ads_status_loga'] = 'Status changed';
$l['advadsman_ads_status_logd'] = 'Advertisement status (ID : {1}) changed successfully.';
$l['advadsman_ads_status_success'] = 'Advertisement status changed successfully.';

$l['advadsman_ads_doadd_error1'] = 'Zone, number of nmonths or URL are filled incorrectly!';
$l['advadsman_ads_doadd_error2'] = 'Error appear on image upload process.';
$l['advadsman_ads_doadd_loga'] = 'Advertisement added';
$l['advadsman_ads_doadd_logd'] = 'Advertisement added from AdminCP for {1} months.';
$l['advadsman_ads_doadd_success'] = 'Advertisement added from AdminCP successfully.';

$l['advadsman_ads_add_title'] = 'Add advertisement';
$l['advadsman_ads_add_zone'] = 'Zone';
$l['advadsman_ads_add_zone_desc'] = 'Choose a zone for this advertisement.';
$l['advadsman_ads_add_period'] = 'Number of months';
$l['advadsman_ads_add_period_desc'] = 'For how many months will this ad be avaible.';
$l['advadsman_ads_add_url'] = 'Website URL';
$l['advadsman_ads_add_url_desc'] = 'What web address will be associated with this ad?';
$l['advadsman_ads_add_image'] = 'Image attached';
$l['advadsman_ads_add_image_desc'] = 'Specify an image that can be attached to this advertisement.';

$l['advadsman_ads_edit_title'] = 'Edit advertisement';
$l['advadsman_ads_edit_form'] = 'Edit';
$l['advadsman_ads_edit_uid'] = 'Name';
$l['advadsman_ads_edit_uid_desc'] = 'Which user is the author of this advertisement. Leave empty for no changes.';
$l['advadsman_ads_edit_groups'] = 'Groups';
$l['advadsman_ads_edit_groups_desc'] = 'Which groups <strong>cannot see</strong> this advertisement.';
$l['advadsman_ads_edit_url'] = 'Website URL';
$l['advadsman_ads_edit_url_desc'] = 'Do you want to change the web address associated with this space? Leave empty for no changes.';
$l['advadsman_ads_edit_image'] = 'Banner Image';
$l['advadsman_ads_edit_image_desc'] = 'Do you want to change the banner image associated with this space? Leave empty for no changes.';
$l['advadsman_ads_edit_relative'] = 'Expires on';
$l['advadsman_ads_edit_relative_desc'] = 'The setting allows increasing or decreasing the time interval during which the advertisement is displayed. At this moment, date of expire is {1}.';
$l['advadsman_ads_edit_loga'] = 'Advertisement changed';
$l['advadsman_ads_edit_logd'] = 'Advertisement (ID : {1}) has been changed successfully.';
$l['advadsman_ads_edit_success'] = 'Advertisement changed successfully!';

$l['advadsman_ads_approve_text'] = 'This advertisement need the administrator approvement after edit! Here you can find what he changed : {1}';
$l['advadsman_ads_approve_text_url'] = '<li>URL changed from "{1}" into "{2}";</li>';
$l['advadsman_ads_approve_text_image'] = '<li>Image changed from <a href="{1}" target="_blank">this</a> into <a href="{2}" target="_blank">this</a>;</li>';
$l['advadsman_ads_approve_loga'] = 'Approvement after edit';
$l['advadsman_ads_approve_logd'] = 'Advertisement (ID : {1}) has been approved or disapproved after edit.';
$l['advadsman_ads_approve_success'] = 'Action realized with success!';

$l['advadsman_ads_delete_loga'] = 'Advertisement deleted';
$l['advadsman_ads_delete_logd'] = 'Advertisement deleted successfully. (User {1} get {2} NewPoints credits)';
$l['advadsman_ads_delete_success'] = 'Entry deleted successfully.';

$l['advadsman_ads_legend'] = 'The significance of colors present on <em>"Approved?"</em> column';
$l['advadsman_legend_active'] = 'Green';
$l['advadsman_legend_active_text'] = 'Advertisement who is approved and it is displayed on your board!';
$l['advadsman_legend_inactive'] = 'Dark Yellow';
$l['advadsman_legend_inactive_text'] = 'Advertisement who need first admin aprovement!';
$l['advadsman_legend_edited'] = 'Brown';
$l['advadsman_legend_edited_text'] = 'Advertisement who need aprovement after author editing!';

$l['advadsman_logs'] = 'Logs';
$l['advadsman_logs_desc'] = 'Here can be managed all logs for this plugin.';
$l['advadsman_logs_action'] = 'Action';
$l['advadsman_logs_data'] = 'Data';
$l['advadsman_logs_date'] = 'Date';
$l['advadsman_logs_user'] = 'User';
$l['advadsman_logs_noresults'] = 'No logs found in your database!';
$l['advadsman_logs_title'] = 'Log entries';

$l['advadsman_logs_prune'] = 'Prune logs';
$l['advadsman_logs_older'] = 'Older then';
$l['advadsman_logs_older_desc'] = 'You have the option to delete all logs that are older then X days.';
$l['advadsman_logs_pruned'] = 'Logs deleted successfully!';

$l['advadsman_whocanadd'] = 'Can add an advertisement?';
$l['advadsman_whodenyview'] = 'Cannot see the advertisements?';
?>