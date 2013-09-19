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

$l['advinvman_mod_title'] = 'Advanced Ads Manager';

$l['advinvman_menu_title'] = 'My Ads';

$l['advadsman_upload_error1'] = 'The image you have uploaded doesn`t has a valid extension. The valid entensions are : <strong>{1}</strong>, but yours it is <strong>{2}</strong>.';
$l['advadsman_upload_error2'] = 'The image didn`t uploaded because there are some wrong permissions set to "advadsman" directory.';
$l['advadsman_upload_error3'] = 'The file you`ve uploaded is not an image.';
$l['advadsman_upload_error4'] = 'Image is too much large. Maximum size allowed is {1} px (width x height).';

$l['advadsman_error_invalidbuy'] = 'You cannot extend your advertisement because you don`t have rights.';
$l['advadsman_error_cannotadd'] = 'You do not have permissions to add advertisements!';
$l['advadsman_error_invalidperiod'] = 'Invalid number of months specified. Be sure that you insert only integers on this interval : [{1}] (closed).';
$l['advadsman_error_loginrequired'] = 'You are either not logged in or do not have permission to view this page.';
$l['advadsman_error_nomoney'] = 'You don`t have enough NewPoints credits to do this action. For this action you must have at least {1} points.';
$l['advadsman_error_invalidad'] = 'Advertisement specified doesn`t not exist our is not yours!';
$l['advadsman_error_alreadyedited'] = 'You have already changed this advertisement and, in consequence, you cannot edit until an administrator will approve the first change.';
$l['advadsman_error_invalidzone'] = 'You have chosed an invalid zone name. Please try again!';
$l['advadsman_error_invalidurl'] = 'Your specified URL is not correct. Please try again more carrefully!';
$l['advadsman_error_disabled'] = 'Payment option disabled by an administrator. Only free banners can be added.';

$l['advadsman_stats_show_title'] = 'Board Stats';
$l['advadsman_stats_show_day'] = 'Day';
$l['advadsman_stats_show_visitors'] = 'Visitors';
$l['advadsman_stats_show_visits'] = 'Visits';
$l['advadsman_stats_show_pageviews'] = 'Page Views';
$l['advadsman_stats_show_timeonsite'] = 'Time on Site';
$l['advadsman_stats_show_total'] = 'Total';
$l['advadsman_stats_noresults'] = 'No results founded! Check connection with Google Analytics...';

$l['advadsman_space_show_noresults'] = 'No results founded.';
$l['advinvman_space_show_title'] = 'My Active Advertisements';
$l['advinvman_space_show_table'] = 'My Advertisements';
$l['advadsman_space_show_table_zone'] = 'Zone';
$l['advadsman_space_show_table_disabled'] = 'Approved?';
$l['advadsman_space_show_table_disabled_y'] = 'Yes';
$l['advadsman_space_show_table_disabled_n'] = 'No';
$l['advadsman_space_show_table_from'] = 'Created on';
$l['advadsman_space_show_table_to'] = 'Expire on';
$l['advadsman_space_show_table_clicks'] = 'Clicks';
$l['advadsman_space_show_table_views'] = 'Views';

$l['advadsman_space_show_table_options'] = 'Options';
$l['advadsman_space_show_table_edit'] = 'Edit';
$l['advadsman_space_show_table_extend'] = 'Extend';

$l['advadsman_space_doadd_success'] = 'Your advertising space was successfully added, but now you must wait until one administrator will approve it!';
$l['advadsman_space_doadd_loga'] = 'Ad space bought successfully';
$l['advadsman_space_doadd_logd'] = 'Space purchased for a period of {1} months.';

$l['advadsman_space_add'] = 'Add Advertisement';
$l['advadsman_space_add_form_title'] = 'Add a new Advertisement';
$l['advadsman_space_add_form_user'] = 'Username';
$l['advadsman_space_add_form_zoned'] = 'Choose something!';
$l['advadsman_space_add_form_zone'] = 'Please select a zone';
$l['advadsman_space_add_form_zonedesc'] = 'Choose the area where you want to display advertising space that you want to create.';
$l['advadsman_space_add_form_period'] = 'Number of months';
$l['advadsman_space_add_form_perioddesc'] = 'For how many months you want buy this zone space?';
$l['advadsman_space_add_form_list'] = 'Prices';
$l['advadsman_space_add_form_listdesc'] = 'List of prices for each zone. Please note that the price is for <strong>a month</strong>.';
$l['advadsman_space_add_form_url'] = 'Website URL';
$l['advadsman_space_add_form_urldesc'] = 'Here you must add your website URL. Format : <i>http://www.subdomain.domain.com/path</i>.';
$l['advadsman_space_add_form_img1'] = 'Image displayed';
$l['advadsman_space_add_form_img1desc'] = 'Your image must have a maximum <b id="advadsman_add_dimension"></b> (width x height) pixels and she must have one of the following extensions <b>"{1}"</b>.';
$l['advadsman_space_add_form_prices'] = '<a id="advadsman_price_toogle" href="#" rel="advadsman_row_toogle">[Prices List]</a>';
$l['advadsman_space_add_form_submit'] = 'Submit';

$l['advadsman_space_dobuy_success'] = 'Successfully extended advertising space.';
$l['advadsman_space_buy'] = 'Extend Advertisement';
$l['advadsman_space_buy_form_title'] = 'Extend own Advertisement';
$l['advadsman_space_buy_form_period'] = 'Number of months';
$l['advadsman_space_buy_form_perioddesc'] = 'For how many months you want extend this advertisement? Relative values are required!';
$l['advadsman_space_buy_form_current'] = 'The current expiration date for this advertisement is <strong>{1}</strong>.';

$l['advadsman_space_doedit_success'] = 'Advertisement successfully changed. For the changes to be visible an administrator must approve them!';
$l['advadsman_space_doedit_error'] = 'Nothing to change!';
$l['advadsman_space_doedit_loga'] = 'Advertisement changed successfully';
$l['advadsman_space_doedit_logd'] = 'Advertisement (with ID : {1}) was changed with success.';

$l['advadsman_space_edit_form_title'] = 'Edit your Advertisement';
$l['advadsman_space_edit_form_url'] = 'Website URL';
$l['advadsman_space_edit_form_urldesc'] = 'Here you can change your website URL associated with this space. Leave field blank if you do not want any change.';
$l['advadsman_space_edit_form_img'] = '<a href="{1}" target="_blank">Image</a>';
$l['advadsman_space_edit_form_imgdesc'] = 'Here you can change your banner image that appears on this board. Leave field blank if you do not want any change.';
$l['advadsman_space_edit_form_submit'] = 'Edit';

$l['advadsman_task_pmsubject'] = 'Expiration notice for an advertising space';
$l['advadsman_task_pmmessage'] = 'Hello! Advertising space purchased some time ago due to expire within {1} days. More specifically, the expiry date is {2}.';
$l['advadsman_task_note_loga'] = 'Notifications send';
$l['advadsman_task_note_logd'] = 'A number of {1} notifications were send by task.';
$l['advadsman_task_ran'] = 'Task ran successfully.';
?>