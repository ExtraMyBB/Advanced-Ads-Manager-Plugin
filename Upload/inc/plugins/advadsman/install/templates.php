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

if ( ! defined('IN_MYBB') ||  ! defined('ADVADSMAN_VERSION')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB and ADVADSMAN_VERSION are defined.');
}

function advadsman_install_templates()
{
    $templates = array();
    
    $templates[] = array(
        'title' => 'nav_option',
        'template' => "<tr><td class=\"trow1 smalltext\"><a href=\"{\$mybb->settings['bburl']}/{\$nav_link}\" style=\"background: url('images/advadsman/advadsman.gif') no-repeat left center\" class=\"{\$class1}\">{\$nav_text}</a></td></tr>"
    );
    $templates[] = array(
        'title' => 'space_postbit',
        'template' => '<div class="float_right">{$adcode}</div>'
    );
    $templates[] = array(
        'title' => 'space_code',
        'template' => '<div class="advadsman-box" style="width:{$ad[\'width\']}px"><div class="advadsman-img" onclick="AAM.do_click({$ad[\'aid\']});"><a href="{$ad[\'url\']}" target="_blank"><img class="advadsman-image" src="{$ad[\'image\']}" /></a></div><div class="advadsman-copy">{$ad[\'pow\']}</div></div>'
    );
    $templates[] = array(
        'title' => 'stats_table',
        'template' => '<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->advadsman_stats_show_title}</strong></td>
</tr>
<tr>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_stats_show_day}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_stats_show_visitors}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_stats_show_visits}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_stats_show_pageviews}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_stats_show_timeonsite}</strong></td>
</tr>
{$active_stats}
</table>'
    );
    $templates[] = array(
        'title' => 'stats_show',
        'template' => '<tr>
<td class="{$bgcolor}" width="20%" align="center">{$stat[\'day\']}</td>
<td class="{$bgcolor}" width="20%" align="center">{$stat[\'visitors\']}</td>
<td class="{$bgcolor}" width="20%" align="center">{$stat[\'visits\']}</td>
<td class="{$bgcolor}" width="20%" align="center">{$stat[\'pageviews\']}</td>
<td class="{$bgcolor}" width="20%" align="center">{$stat[\'timeonsite\']}</td>
</tr>'
    );
    $templates[] = array(
        'title' => 'space_table',
        'template' => '<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead" colspan="{$colspan}"><strong>{$lang->advinvman_space_show_table}</strong><div class="float_right"><a href="usercp.php?action=advadsman&method=space_add"><img src="images/advadsman/add.png" /></a></div></td>
</tr>
<tr>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_space_show_table_zone}</strong></td>
<td class="tcat" width="10%" align="center"><strong>{$lang->advadsman_space_show_table_disabled}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_space_show_table_from}</strong></td>
<td class="tcat" width="20%" align="center"><strong>{$lang->advadsman_space_show_table_to}</strong></td>
<td class="tcat" width="10%" align="center"><strong>{$lang->advadsman_space_show_table_clicks}</strong></td>
<td class="tcat" width="10%" align="center"><strong>{$lang->advadsman_space_show_table_views}</strong></td>
<td class="tcat" width="10%" align="center"><strong>{$lang->advadsman_space_show_table_options}</strong></td>
</tr>
{$active_spaces}
</table>'
    );
    $templates[] = array(
        'title' => 'space_show',
        'template' => '<tr>
<td class="{$bgcolor}" width="20%" align="center">{$ad[\'zonename\']}</td>
<td class="{$bgcolor}" width="10%" align="center">{$ad[\'enabled\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$ad[\'create\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$ad[\'expire\']}</td>
<td class="{$bgcolor}" width="10%" align="center">{$ad[\'clicks\']}</td>
<td class="{$bgcolor}" width="10%" align="center">{$ad[\'views\']}</td>
<td class="{$bgcolor}" width="20%" align="center">{$ad[\'options\']}</td>
</tr>'
    );
    $templates[] = array(
        'title' => 'no_results',
        'template' => '<tr>
<td class="{$bgcolor}" width="100%" colspan="{$colspan}" align="center">{$no_results}</td>
</tr>'
    );
    $templates[] = array(
        'title' => 'standard_page',
        'template' => '<html>
<head>
<title>{$mybb->settings[bbname]} - {$title}</title>
{$headerinclude}
{$extraheader}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
     {$usercpnav}
     <td valign="top">
          {$content}
          <br/>
          <div style="background: #efefef;border: 1px solid #4874a3;padding: 4px;"><span class="smalltext">Copyright &copy; 2012-2013 by <a href="http://extramybb.com" target="_blank">ExtraMyBB.com Community</a>. All rights reserved.</span></div>
     </td>
</tr>
</table>
{$footer}
</body>
</html>'
    );
    $templates[] = array(
        'title' => 'space_add',
        'template' => '<form action="usercp.php?action=advadsman&method=space_do_add" method="post" enctype="multipart/form-data" name="input">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->advadsman_space_add_form_title}</strong><div style="float: right">{$lang->advadsman_space_add_form_prices}</div></td>
</tr>
<tr>
<td class="trow1" width="50%"><strong>{$lang->advadsman_space_add_form_user}:<sup>*</sup></strong></td>
<td class="trow1"><input type="text" class="textbox" name="username" size="10" maxlength="100" value="{$mybb->user[\'username\']}" tabindex="1" readonly/></td>
</tr>
<tr>
<td class="trow2" width="50%"><strong>{$lang->advadsman_space_add_form_zone}:<sup>*</sup></strong><br />
<span class="smalltext">{$lang->advadsman_space_add_form_zonedesc}</span></td>
<td class="trow2"><select name="zone" id="advadsman_add_zones" size="1">{$options}</select></td>
</tr>  
<tr>
<td class="trow1" width="50%"><strong>{$lang->advadsman_space_add_form_period}:<sup>*</sup></strong><br />
<span class="smalltext">{$lang->advadsman_space_add_form_perioddesc}</span></td>
<td class="trow1"><input type="text" class="textbox" name="period" size="10" maxlength="12" value="" tabindex="1"/></td>
</tr>     
<tr id="advadsman_row_toogle">
<td class="trow2" width="50%"><strong>{$lang->advadsman_space_add_form_list}:</strong><br />
<span class="smalltext">{$lang->advadsman_space_add_form_listdesc}</span></td>
<td class="trow2">{$list_prices}</td>
</tr>                       
<tr>
<td class="trow1" width="50%"><strong>{$lang->advadsman_space_add_form_url}:<sup>*</sup></strong><br />
<span class="smalltext">{$lang->advadsman_space_add_form_urldesc}</span></td>
<td class="trow1"><input type="text" class="textbox" name="url" size="40" maxlength="100" value="" tabindex="1" />
</td>
</tr>
<tr>
<td class="trow2" width="50%"><strong>{$lang->advadsman_space_add_form_img1}:<sup>*</sup></strong><br />
<span class="smalltext">{$img1desc}</span></td>
<td class="trow2"><input type="file" size="50" name="imagebrowse" class="fileupload"/></td>
</tr>
</table>
<div style="text-align:center"><input type="submit" class="button" name="submit" value="{$lang->advadsman_space_add_form_submit}" tabindex="4" accesskey="s" /></div>

</form>'
    );
    $templates[] = array(
        'title' => 'space_buy',
        'template' => '<form action="usercp.php?action=advadsman&method=space_do_buy" method="post" name="input">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<input type="hidden" name="aid" value="{$aid}" />
<input type="hidden" name="zid" value="{$zone}" />
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->advadsman_space_buy_form_title}</strong></td>
</tr>                         
<tr>
<td class="trow1" width="50%"><strong>{$lang->advadsman_space_buy_form_period}:<sup>*</sup></strong><br />
<span class="smalltext">{$lang->advadsman_space_buy_form_perioddesc}</span></td>
<td class="trow1"><input type="text" class="textbox" name="period" size="10" maxlength="12" value="" tabindex="1" />
</td>
</tr>
<tr>
<td class="trow2" colspan="2" align="center">{$current_expire}</td>
</tr>
</table>
<div style="text-align:center"><input type="submit" class="button" name="submit" value="{$lang->advadsman_space_add_form_submit}" tabindex="4" accesskey="s" /></div>
</form>'
    );
	$templates[] = array(
        'title' => 'space_edit',
        'template' => '<form action="usercp.php?action=advadsman&method=space_do_edit" method="post" enctype="multipart/form-data" name="input">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<input type="hidden" name="aid" value="{$aid}" />
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->advadsman_space_edit_form_title}</strong></td>
</tr>                         
<tr>
<td class="trow1" width="50%"><strong>{$lang->advadsman_space_edit_form_url}:</strong><br />
<span class="smalltext">{$lang->advadsman_space_edit_form_urldesc}</span></td>
<td class="trow1"><input type="text" class="textbox" name="url" size="40" maxlength="100" value="{$ad[\'url\']}" tabindex="1" />
</td>
</tr>
<tr>
<td class="trow2" width="50%"><strong>{$lang->advadsman_space_edit_form_img}:</strong><br />
<span class="smalltext">{$lang->advadsman_space_edit_form_imgdesc}</span></td>
<td class="trow2"><input type="file" size="50" name="imagebrowse" class="fileupload"/></td>
</tr>
</table>
<div style="text-align:center"><input type="submit" class="button" name="submit" value="{$lang->advadsman_space_edit_form_submit}" tabindex="4" accesskey="e" /></div>
</form>'
    );
    
    return $templates;
}
?>