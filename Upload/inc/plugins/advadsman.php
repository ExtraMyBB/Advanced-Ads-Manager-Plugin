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

// Direct initialization of this file is not allowed.
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

// Directory where can be found all plugin functions
define('ADVADSMAN_PLUGIN_PATH', MYBB_ROOT . 'inc/plugins/advadsman/');
// Were will be stored all user attachments related with this plugin?
define('ADVADSMAN_UPLOAD_PATH', MYBB_ROOT . 'uploads/advadsman/');
// Plugin version
define('ADVADSMAN_VERSION', '1.1.0');

// AdminCP was been accessed?
if (defined('IN_ADMINCP')) 
{
    /*
	 * Some infos about plugin.
	 */
    function advadsman_info() 
    {
        return array(
            'name' => 'Advanced Ads Manager',
            'description' => 'A powerful ads manager it is added to your MyBB board using multiple payment gateways.',
            'website' => 'http://extramybb.com',
            'author' => 'Surdeanu Mihai',
            'authorsite' => 'http://mybb.ro',
            'version' => ADVADSMAN_VERSION,
            'compatibility' => '16*'
        );
    }

    /*
	 * Called when someone click on "Install" link.
	 */
    function advadsman_install() 
    {
        global $db, $cache;

        // do some database changes
        $collation = $db->build_create_table_collation();
        require_once(ADVADSMAN_PLUGIN_PATH . 'install/queries.php');
        if (function_exists('advadsman_install_queries')) {
            $queries = advadsman_install_queries($collation);
            foreach ($queries as $condition => $query) {
                if (is_integer($condition)) {
                    $db->write_query($query);
                } else {
					// format : condition:table
                    $info = @explode(':', $condition);
                    if (is_array($info) && count($info) == 2
                            && method_exists($db, $info[0] . '_exists')) {
                        $function = $info[0] . '_exists';
                        if (!$db->$function($info[1])) {
                            $db->write_query($query);
                        }
                    } else {
                        $db->write_query($query);
                    }
                }
            }
        }

        // 3 default ad zones will be added ("maximension" format : width x height)
        $db->insert_query('advadsman_zones', array(
            'name' => 'Header',
            'description' => 'Ads placed in this zone will be displayed in the header.',
            'maxdimension' => '728x80',
            'points' => 30
        ));
        $db->insert_query('advadsman_zones', array(
            'name' => 'Postbit',
            'description' => 'Ads placed in this zone will be displayed in posts.',
            'maxdimension' => '256x256',
            'points' => 10
        ));
        $db->insert_query('advadsman_zones', array(
            'name' => 'Footer',
            'description' => 'Ads placed in this zone will be displayed in the footer.',
            'maxdimension' => '728x80',
            'points' => 20
        ));

		if( ! $db->field_exists('advadsman_whocanadd', 'usergroups')) {
			$db->query('ALTER TABLE ' . TABLE_PREFIX . 'usergroups ADD advadsman_whocanadd TINYINT(1) NOT NULL DEFAULT 1');
		}
		if( ! $db->field_exists('advadsman_whodenyview', 'usergroups')) {
			$db->query('ALTER TABLE ' . TABLE_PREFIX . 'usergroups ADD advadsman_whodenyview TINYINT(1) NOT NULL DEFAULT 0');
		}
		
		// banned users cannot add new spaces
		$db->query('UPDATE ' . TABLE_PREFIX . 'usergroups SET advadsman_whocanadd = 0 WHERE gid = 7');
		
		// update cache immediately
		$cache->update_usergroups();

        // a new template group will be added
        $template_group = array(
            'prefix' => 'advadsman',
            'title' => 'Advanced Ads Manager'
        );
        $db->insert_query('templategroups', $template_group);

        // templates added by plugin        
        require_once(ADVADSMAN_PLUGIN_PATH . 'install/templates.php');
        if (function_exists('advadsman_install_templates')) {
            $templates = advadsman_install_templates();
            foreach ($templates as $template) {
                $insert = array(
                    'title' => 'advadsman_' . $template['title'],
                    'template' => $db->escape_string($template['template']),
                    'sid' => '-2',
                    'version' => '1600',
                    'dateline' => TIME_NOW
                );
                $db->insert_query('templates', $insert);
            }
        }

        // finally, create a new task      
        $task = array(
            'title' => 'Advanced Ads Manager',
            'description' => 'Gives you the possibility to create automated tasks.',
            'file' => 'advadsman',
            'minute' => '0',
            'hour' => '12',
            'day' => '*',
            'month' => '*',
            'weekday' => '*',
            'nextrun' => 0,
            'lastrun' => 0,
            'enabled' => 0,
            'logging' => 1,
            'locked' => 0
        );
        $db->insert_query('tasks', $task);
    }

    /*
	 * Is our plugin installed?
	 */
    function advadsman_is_installed() 
    {
        global $db;

        // at least one database table is missing => uninstalled plugin
        require_once(ADVADSMAN_PLUGIN_PATH . 'install/queries.php');
        if (function_exists('advadsman_install_check')) {
            $tables = advadsman_install_check();
            foreach ($tables as $table) {
                if (!$db->table_exists($table)) {
                    return FALSE;
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    /*
	 * What happens when user click on "Uninstall" link?!
	 */
    function advadsman_uninstall() 
    {
        global $db, $cache;

        // rollback all changes made to database
        require_once(ADVADSMAN_PLUGIN_PATH . 'install/queries.php');
        if (function_exists('advadsman_install_delete')) {
            $queries = advadsman_install_delete();
            foreach ($queries as $condition => $query) {
                if (is_integer($condition)) {
                    $db->write_query($query);
                } else {
                    $info = @explode(':', $condition); // test:action:table
                    if (is_array($info) && count($info) == 3
                            && method_exists($db, $info[0]) 
							&& method_exists($db, $info[1])) {
                        $test = $info[0];
                        $action = $info[1];
                        if ($db->$test($info[2])) {
                            if (empty($query)) {
                                $db->$action($info[2]);
                            } else {
                                $db->$action($query);
                            }
                        }
                    } else {
                        $db->write_query($query);
                    }
                }
            }
        }
		
		if($db->field_exists('advadsman_whocanadd', 'usergroups')) {
			$db->query('ALTER TABLE ' . TABLE_PREFIX . 'usergroups DROP advadsman_whocanadd');
		}
		if($db->field_exists('advadsman_whodenyview', 'usergroups')) {
			$db->query('ALTER TABLE ' . TABLE_PREFIX . 'usergroups DROP advadsman_whodenyview');
		}
		
		// update cache immediately
		$cache->update_usergroups();

        // rollback all changes related with templates
        $db->delete_query('templategroups', "prefix = 'advadsman'");
        $db->delete_query('templates', "title LIKE 'advadsman_%'");

        // delete task
        $db->delete_query('tasks', "file = 'advadsman'");
        
        // delete all plugin images uploaded by users
        $files = glob(ADVADSMAN_UPLOAD_PATH . '/aam_*');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /*
	 * What happens on plugin activation?!
	 */
    function advadsman_activate() 
    {
        global $db, $mybb;

        // activate the new task created
        $db->update_query('tasks', array('enabled' => 1), "file = 'advadsman'");

        // create a new settings group
        $setting_group = array(
            'name' => 'advadsman_group',
            'title' => 'Advanced Ads Manager',
            'description' => "Settings for the \"Advanced Ads Manager\" plugin.",
            'disporder' => '1',
            'isdefault' => 'no',
        );
        $db->insert_query('settinggroups', $setting_group);
        $gid = $db->insert_id();

        // add settings to the group created above
        require_once(ADVADSMAN_PLUGIN_PATH . 'install/settings.php');
        if (function_exists('advadsman_install_settings')) {
            $settings = advadsman_install_settings();
            $disporder = 1;
            foreach ($settings as $setting) {
                $name = 'advadsman_' . $db->escape_string($setting['name']);
                $setting['disporder'] = $disporder++;
                $setting['gid'] = (int) $gid;
                if (array_key_exists($name, $mybb->settings)) {
                    unset($setting['name']);
                    $db->update_query('settings', $setting, "name = '{$name}'");
                } else {
                    $setting['name'] = $name;
                    $db->insert_query('settings', $setting);
                }
            }
        }
		
		// update intern cache (default ad zones need to be recognized)
        advadsman_cache_massupdate(1);

        // rebuild all settings
        rebuild_settings();

        // make some core template changes
        advadsman_insert_templates();
    }

	/*
	 * Deactivate plugin.
	 */
    function advadsman_deactivate() 
    {
        global $db;

        // disable task run
        $db->update_query('tasks', array('enabled' => 0), "file = 'advadsman'");

        // delete all settings from database
        $db->delete_query('settinggroups', "name = 'advadsman_group'");
        $db->delete_query('settings', "name LIKE 'advadsman_setting_%'");


        // rebuild MyBB core settings
        rebuild_settings();

        // cache use by modificaion will be also deleted
        $db->delete_query('datacache', "title = 'advadsman_cache'");
        
        // remove all changes done into MyBB core templates
        advadsman_remove_templates();
    }

    /*
	 * Other functions required for plugin administration.
	 */
    require_once(ADVADSMAN_PLUGIN_PATH . 'admin.php');
} else {
    $plugins->add_hook('usercp_menu', 'advadsman_menu_built', 30);
	/*
	 * A new item it is added into "User CP" menu.
	 */
    function advadsman_menu_built() 
    {
        global $mybb, $lang, $templates, $usercpmenu;

        if ($mybb->settings['advadsman_setting_enable'] != 1) {
            return;
        }

        $lang->load('advadsman');

		// link it is visible only from few pages
        if (in_array(THIS_SCRIPT, array('private.php', 'usercp.php'))) {
            $advadsman_nav_option = '';
            $class1 = 'usercp_nav_item';
            $nav_link = 'usercp.php?action=advadsman';
            $nav_text = $lang->advinvman_menu_title;

            eval("\$usercpmenu .= \"" . $templates->get('advadsman_nav_option') . "\";");
        }
    }

    $plugins->add_hook('xmlhttp', 'advadsman_click');
    /*
	 * The number of clicks and views for an active ad space it is updated
	 * using this function.
	 */
    function advadsman_click() 
    {
        global $mybb, $lang;

        if ($mybb->settings['advadsman_setting_enable'] != 1) {
            return;
        }

        if ($mybb->request_method != 'post' || $mybb->input['action'] != 'do_click'
                || ! is_numeric($mybb->input['aid']) || $mybb->input['aid'] < 1) {
            return;
        }

        if ( ! verify_post_check($mybb->input['my_post_key'], TRUE)) {
            xmlhttp_error($lang->invalid_post_code);
        }

        ignore_user_abort(TRUE);

        // save data to cache for increasing board performance
        advadsman_cache_update('clicks', (int) $mybb->input['aid'], 1, TRUE);
    }

    $plugins->add_hook('pre_output_page', 'advadsman_showads');
    /*
	 * Ad spaces will be displayed on board using this function (except ads from
	 * Postbit)
	 */
    function advadsman_showads(&$page) 
    {
        global $mybb, $templates;

        if ($mybb->settings['advadsman_setting_enable'] != 1) {
            return;
        }

        // are default images present?
        $imgs = json_decode($mybb->settings['advadsman_setting_defaultimgs'], TRUE);
        if ($imgs == NULL) {
            $imgs = array();
        }

        $default = array(
            'aid' => 0,
            'url' => $mybb->settings['advadsman_setting_defaulturls'],
            'pow' => base64_decode('PGVtPkFkcyBNYW5hZ2VyPC9lbT4gYnkgPGEgaHJlZj0iaHR0cDovL21paGFpc3VyZGVhbnUucm8iIHRhcmdldD0iX2JsYW5rIj5NaWhhaVN1cmRlYW51LnJvPC9hPg==')
        );

        // caching it is used for reading data
        $zones = advadsman_cache_read('zones', 'all');
        foreach ($zones as $zid => $zone) 
		{
            // for Postbit zone aother function will be used
            if ($zid == 2) {
                continue;
            }

            // ad zone exists?
            if (strstr($page, "<!--advadsman_z$zid-->")) {
                // advertisement present on that zone selected
                $ad = advadsman_select_ad($zid);

                // it is hidden for that user?
                if ($ad && advadsman_canview($ad['groups'])) {
                    $ad['pow'] = $default['pow'];
                    $ad['width'] += 6;
                    eval("\$code = \"" . $templates->get('advadsman_space_code') . "\";");

                    // increase number of views
                    advadsman_cache_update('views', $ad['aid'], 1, TRUE);
                } else {
                    // default image displayed?
                    if ( ! $ad && isset($imgs[$zid]) && 
							advadsman_canview($mybb->settings['advadsman_setting_whodenyview']) && 
							file_exists(ADVADSMAN_UPLOAD_PATH . $imgs[$zid]['img'])) {
                        $ad = $default;
                        $ad['image'] = './uploads/advadsman/' . $imgs[$zid]['img'];
                        $ad['width'] = $imgs[$zid]['width'] + 6;
                        eval("\$code = \"" . $templates->get('advadsman_space_code') . "\";");
                    }
                }
				
				$page = str_replace("<!--advadsman_z$zid-->", "<div align='center'>{$code}</div>", $page);
            }
        }
    }

    $plugins->add_hook('postbit', 'advadsman_showads1');
    /*
	 * Ad spaces will be displayed on board using this function (only for Postbit).
	 */
    function advadsman_showads1(&$post) 
    {
        global $mybb, $templates, $postcounter;

        // plugin enabled?
        if ($mybb->settings['advadsman_setting_enable'] != 1) {
            return;
        }

        // select an ad zone
        $ad = advadsman_select_ad(2);

        $encrypt = base64_decode('PGVtPkFkcyBNYW5hZ2VyPC9lbT4gYnkgPGEgaHJlZj0iaHR0cDovL21paGFpc3VyZGVhbnUucm8iIHRhcmdldD0iX2JsYW5rIj5NaWhhaVN1cmRlYW51LnJvPC9hPg==');

        // selected?
        if ($ad) {
            // what kind of zone do we have?
            $zones = advadsman_cache_read('zones');
            if (!isset($zones[2]) || !isset($zones[2]['posts'])) {
                $posts = 0;
            } else {
                $posts = (int) $zones[2]['posts'];
            }

            // do we have permissions?
            if (advadsman_canview($ad['groups'])) {
                $ad['pow'] = $encrypt;
                $ad['width'] += 6;
                eval("\$adcode = \"" . $templates->get('advadsman_space_code') . "\";");

                // advertisement space displayed in first post of each page
                if (($postcounter - 1) % $mybb->settings['postsperpage'] == 0) {
                    eval("\$post['advadsman_ads'] = \"" . $templates->get('advadsman_space_postbit') . "\";");
                    return;
                }

                // advertisement space displayed in first post of each page + each X posts after
                if ($posts > 0 && (($postcounter - 1) % $posts == $posts - 1)) {
                    eval("\$post['advadsman_ads'] = \"" . $templates->get('advadsman_space_postbit') . "\";");
                }
            }
        } else {
            // default image displayed?
            if (advadsman_canview($mybb->settings['advadsman_setting_whodenyview'])
                    && ($postcounter - 1) % $mybb->settings['postsperpage'] == 0) {
                $imgs = json_decode($mybb->settings['advadsman_setting_defaultimgs'], TRUE);
                if ($imgs == NULL) {
                    $imgs = array();
                }

                if (isset($imgs[2]) && file_exists(ADVADSMAN_UPLOAD_PATH . $imgs[2]['img'])) {
                    $ad = array(
                        'aid' => 0,
                        'url' => $mybb->settings['advadsman_setting_defaulturls'],
                        'image' => './uploads/advadsman/' . $imgs[2]['img'],
                        'width' => $imgs[2]['width'] + 6,
                        'pow' => $encrypt
                    );
                    eval("\$adcode = \"" . $templates->get('advadsman_space_code') . "\";");
                    eval("\$post['advadsman_ads'] = \"" . $templates->get('advadsman_space_postbit') . "\";");
                }
            }
        }
    }

    $plugins->add_hook('usercp_start', 'advadsman_enduser');
    /*
	 * Offers a beautiful user interface.
	 */
    function advadsman_enduser() 
    {
        global $mybb, $db, $lang, $templates, $header, $headerinclude, 
			$extraheader, $footer, $usercpnav, $cache;

        if ($mybb->settings['advadsman_setting_enable'] != 1
                || THIS_SCRIPT != 'usercp.php' || $mybb->input['action'] != 'advadsman') {
            return;
        }

        $lang->load('advadsman');

        // main page
        if ( ! $mybb->input['method']) 
        {
            // Google Analytics stats are displayed?
            $gaad = @explode(',', $mybb->settings['advadsman_setting_gaad']);
            if ($mybb->settings['advadsman_setting_gae'] == 1 && count($gaad) == 3) {
                $data = advadsman_cache_analytics($gaad);

                $active_stats = '';
                
                if (is_array($data)) {
                    foreach ($data as $date => $value) {
                        $bgcolor = alt_trow();                   
                        $stat['day'] = substr($date, 6, 2) . '.' . substr($date, 4, 2) . '.' . substr($date, 0, 4);
                        $stat['visitors'] = $value['ga:visitors'];
                        $stat['visits'] = $value['ga:visits'];
                        $stat['pageviews'] = $value['ga:pageviews'];
                        $stat['timeonsite'] = advadsman_time_format($value['ga:timeOnSite']);
                        eval("\$active_stats .= \"" . $templates->get('advadsman_stats_show') . "\";");
                    }
                }

                $colspan = 5;
                // nothing to be displayed?
                if (empty($active_stats)) {
                    $bgcolor = alt_trow();
                    $no_results = $lang->advadsman_stats_noresults;
                    eval("\$active_stats = \"" . $templates->get('advadsman_no_results') . "\";");
                }

                // page content will be sent to user
                eval("\$content = \"" . $templates->get('advadsman_stats_table') . "<br />\";");
            } else {
                $content = '';
            }

            $active_spaces = '';

            // retrieve all active ad spaces for current user
            $query = $db->query('
                SELECT z.name AS zonename, a.* 
                FROM ' . TABLE_PREFIX . 'advadsman_ads a
                LEFT JOIN ' . TABLE_PREFIX . 'advadsman_zones z ON (z.zid = a.zone)
		        WHERE a.uid = ' . (int) $mybb->user['uid'] . ' AND a.expire > ' . TIME_NOW . '
                ORDER BY a.date DESC
            ');
            $future = TIME_NOW + 86400 * ((int) $mybb->settings['advadsman_setting_extperiod']);
            
            // display information row by row
            while ($ad = $db->fetch_array($query)) {
                $bgcolor = alt_trow();
                if ($ad['expire'] < $future) {
                    $link = "<a href='usercp.php?action=advadsman&method=space_buy&aid={$ad['aid']}'>{$lang->advadsman_space_show_table_extend}</a> | ";
                } else {
                    $link = "";
                }

                $ad['enabled'] = ($ad['disabled'] == 0) ? $lang->advadsman_space_show_table_disabled_y : $lang->advadsman_space_show_table_disabled_n;
                $ad['create'] = my_date($mybb->settings['dateformat'], $ad['date']) . ', ' . my_date($mybb->settings['timeformat'], $ad['date']);
                $ad['expire'] = my_date($mybb->settings['dateformat'], $ad['expire']) . ', ' . my_date($mybb->settings['timeformat'], $ad['expire']);
                // when an ad space would not be edited
                if ($ad['disabled'] == 2 || ! empty($ad['urlc']) || ! empty($ad['imagec'])) {
                    $ad['options'] = "-";
                } else {
                    $ad['options'] = "<small>$link<a href='usercp.php?action=advadsman&method=space_edit&aid={$ad['aid']}'>{$lang->advadsman_space_show_table_edit}</a></small>";
                }

                eval("\$active_spaces .= \"" . $templates->get('advadsman_space_show') . "\";");
            }

            $colspan = 7;
            
            // no active space
            if (empty($active_spaces)) {
                $bgcolor = alt_trow();
                $no_results = $lang->advadsman_space_show_noresults;
                eval("\$active_spaces = \"" . $templates->get('advadsman_no_results') . "\";");
            }

            // page content
            eval("\$content .= \"" . $templates->get('advadsman_space_table') . "\";");
        } elseif ($mybb->input['method'] == 'space_add') 
        {
            $extraheader = '<script type="text/javascript" src="../jscripts/scriptaculous.js?load=effects"></script>
            <script type="text/javascript">
            String.prototype.extract = function(regex, number) {
                number = number === undefined ? 0 : number;
                if ( ! regex.global) {
                    return this.match(regex)[number] || \'\';
                }
                var match, extracted = [];
                while ((match = regex.exec(this)) ) {
                    extracted[extracted.length] = match[number] || \'\';
                }
                return extracted;
            };
            Event.observe(document, \'dom:loaded\', function() {
                $(\'advadsman_add_zones\').observe(\'change\', function() {
                    var text = this.childElements()[this.selectedIndex].innerHTML; 
                    $(\'advadsman_add_dimension\').innerHTML = text.extract(/(\d+)x(\d+)/g);
                });
                $(\'advadsman_price_toogle\').observe(\'click\', function(e) {
                    if($(this.rel)) {
                        Effect.toggle(this.rel, \'appear\', { duration: 1.0 });
                    }
                    Event.stop(e);
                });
                $(\'advadsman_row_toogle\').hide();
            });
            </script>';
            
			// use cache for checking
			if ( ! advadsman_canadd()) {
                error($lang->advadsman_error_cannotadd);
            }

            add_breadcrumb($lang->advadsman_space_add, 'usercp.php?action=advadsman&method=space_add');

            // refresh all zones where a new advertisement can be placed
            $zones = advadsman_cache_read('zones');
            $options = '<option value="-">' . $lang->advadsman_space_add_form_zoned . '</option>';
            $prices = array();
            if ($zones) {
                foreach ($zones as $zid => $zone) {
                    // if everything is fine then add zone
                    $prices[] = $zone['name'] . ' : ' . number_format($zone['points'], 2);
                    $options .= '<option value="' . $zid . '">' . $zone['name'] . ' (' . $zone['maxdimension'] . ' px)</option>';
                }
            }
            $list_prices = @implode(' , ', $prices);
            $img1desc = $lang->sprintf($lang->advadsman_space_add_form_img1desc, $mybb->settings['advadsman_setting_validext']);

            eval("\$content = \"" . $templates->get('advadsman_space_add') . "\";");
        } elseif ($mybb->input['method'] == 'space_do_add') 
        {
            verify_post_check($mybb->input['my_post_key']);

            // log-in is required!
            if ($mybb->user['uid'] == 0 && empty($mybb->input['username'])) {
                error($lang->advadsman_error_loginrequired);
            }
            
            // can add new ad spaces?
			if ( ! advadsman_canadd()) {
                error($lang->advadsman_error_cannotadd);
            }

            // good period of time
            $period = (int)$mybb->input['period'];
            $psetting = $mybb->settings['advadsman_setting_period'];
            $interval = @explode(',', $psetting);
            if (is_array($interval) && count($interval) == 2 
                    && $period < $interval[0] || $period > $interval[1]) {
                error($lang->sprintf($lang->advadsman_error_invalidperiod, $psetting)); 
            }
            
            // valid web address?
            if ( ! filter_var($mybb->input['url'], FILTER_VALIDATE_URL)) {
                error($lang->advadsman_error_invalidurl);
            }

            $zones = advadsman_cache_read('zones');
            $select_zone = FALSE;
            // search for an advertisement zone
            if ($zones && isset($zones[(int)$mybb->input['zone']])) {
                $select_zone = TRUE;
                $zone = $zones[(int)$mybb->input['zone']];
            }

            // error on zone selection
            if ( ! $select_zone) {
                error($lang->advadsman_error_invalidzone);
            }

            $amount = (float) $zone['points'] * $period;
            // are utilizatorul destui bani pentru a cumpara spatiul publicitar?
            if ($mybb->user['newpoints'] < $amount) {
                error($lang->sprintf($lang->advadsman_error_nomoney, $amount));
            }

            // try to upload user image
            $image = $_FILES['imagebrowse'];
            $result = advadsman_upload_file($image, $zone['maxdimension']);
            if (isset($result['error'])) {
                error($result['error']);
            }

            // take points from user
            if (function_exists('newpoints_addpoints')) {
                newpoints_addpoints((int)$mybb->user['uid'], -number_format($amount, 2));
            } else {
                $db->update_query(
                    'users', 
                    array('newpoints' => 'newpoints - ' . number_format($amount, 2)),
                    "uid = '" . (int) $mybb->user['uid'] . "'",
                    TRUE
                );
            }
            
            // cache update is required
            advadsman_cache_update(
                'stats', 'total_spend', 
                number_format($amount, intval($mybb->settings['newpoints_main_decimal'])),
                TRUE
            );

            // when the space will expire more exactly?
            $expire = TIME_NOW + 30 * 24 * 60 * 60 * $period;

            // insert row into database
            $insert = array(
                'uid' => (int) $mybb->user['uid'],
                'date' => TIME_NOW,
                'expire' => $expire,
                'url' => $db->escape_string($mybb->input['url']),
                'image' => $result['path'],
                'zone' => (int)$mybb->input['zone'],
                'width' => $result['width']
            );
            // insert can be later done (increase application performance)
            advadsman_db_later(array(
                'table' => 'advadsman_ads', 
                'action' => 'insert',
                'data' => $insert
            ));

            // deny access to that zone
            $db->update_query(
                'advadsman_zones', 
                array('is_ad' => 1), 
                "zid = '" . (int)$mybb->input['zone'] . "'"
            );

            // update intern cache only for that zone
            advadsman_cache_update('zones', (int)$mybb->input['zone'], array('is_ad' => 1));
            
            // add log
            advadsman_db_later(array(
                'table' => 'advadsman_logs', 
                'action' => 'insert',
                'data' => array(
                    'action' => $lang->advadsman_space_doadd_loga, 
                    'data' => $lang->sprintf($lang->advadsman_space_doadd_logd, (int) $mybb->input['period']), 
                    'date' => TIME_NOW, 
                    'user' => $mybb->user['username']
                )
            ));

            // redirect
            redirect('usercp.php?action=advadsman', $lang->advadsman_space_doadd_success);
        } elseif ($mybb->input['method'] == 'space_do_buy') 
        {
            verify_post_check($mybb->input['my_post_key']);
            $aid = (int) $mybb->input['aid'];
            $zid = (int) $mybb->input['zid'];
            $uid = $mybb->user['uid'];

            if ($mybb->user['uid'] == 0) {
                error($lang->advadsman_error_loginrequired);
            }
            
			if ( ! advadsman_canadd()) {
                error($lang->advadsman_error_cannotadd);
            }

            $period = (int)$mybb->input['period'];
            $psetting = $mybb->settings['advadsman_setting_period'];
            $interval = @explode(',', $psetting);
            if (is_array($interval) && count($interval) == 2 
                    && $period < $interval[0] || $period > $interval[1]) {
                error($lang->sprintf($lang->advadsman_error_invalidperiod, $psetting)); 
            }

            $zones = advadsman_cache_read('zones', 'all');
            $select_zone = FALSE;
            if ($zones && isset($zones[$zid])) {
                $select_zone = TRUE;
                $zone = $zones[$zid];
            }
            
            if ( ! $select_zone) {
                error($lang->advadsman_error_invalidzone);
            }

            $amount = (float) $zone['points'] * $period;
            // have our user enough money?
            if ($mybb->user['newpoints'] < $amount) {
                error($lang->sprintf($lang->advadsman_error_nomoney, $amount));
            }

            // take user money
            if (function_exists('newpoints_addpoints')) {
                newpoints_addpoints((int)$mybb->user['uid'], -number_format($amount, intval($mybb->settings['newpoints_main_decimal'])));
            } else {
                $db->update_query(
                    'users', 
                    array('newpoints' => 'newpoints - ' . number_format($amount, intval($mybb->settings['newpoints_main_decimal']))),
                    "uid = '{$uid}'",
                    TRUE
                );
            }
            
            // update user cache
            advadsman_cache_update(
                'stats', 'total_spend',
                number_format($amount, intval($mybb->settings['newpoints_main_decimal'])), 
                TRUE
            );

            // when the space will expire?
            $expire = 30 * 86400 * $period;

            // update expire date for that advertisement
            $db->update_query(
                    'advadsman_ads', 
                    array('expire' => "expire + {$expire}"),
                    "aid = '{$aid}' AND uid = '{$uid}'",
                    TRUE
                );

            redirect('usercp.php?action=advadsman', $lang->advadsman_space_dobuy_success);
        } elseif ($mybb->input['method'] == 'space_buy')
        {
            $aid = (int) $mybb->input['aid'];
            $uid = (int) $mybb->user['uid'];
            $now = TIME_NOW;
            $time = $now + $mybb->settings['advadsman_setting_extperiod'] * 86400;
            
            $query = $db->simple_select(
				'advadsman_ads', 'zone,expire', 
                "aid = '{$aid}' AND uid = '{$uid}' AND expire > {$now} AND expire < {$time}"
			);
            if ($db->num_rows($query) != 1) {
                error($lang->advadsman_error_invalidbuy);
            }
            else {
                $ad = $db->fetch_array($query);
                $zone = $ad['zone'];
                $current_expire = $lang->sprintf($lang->advadsman_space_buy_form_current, 
                    my_date($mybb->settings['dateformat'], $ad['expire']));
            }
            
            eval("\$content = \"" . $templates->get('advadsman_space_buy') . "\";");
        } elseif ($mybb->input['method'] == 'space_edit') 
        {
            $aid = (int) $mybb->input['aid'];

            add_breadcrumb($lang->advadsman_space_edit_form_title, 'usercp.php?action=advadsman&method=space_edit');

            $uid = (int) $mybb->user['uid'];
            $query = $db->simple_select(
                'advadsman_ads', 'url,image', 
                "aid = '{$aid}' AND uid = '{$uid}'"
            );
            if ($db->num_rows($query) != 1) {
                error($lang->advadsman_error_invalidad);
            } else {
                $ad = $db->fetch_array($query);

                $lang->advadsman_space_edit_form_img = $lang->sprintf($lang->advadsman_space_edit_form_img, $ad['image']);
            }

            eval("\$content = \"" . $templates->get('advadsman_space_edit') . "\";");
        } elseif ($mybb->input['method'] == 'space_do_edit') 
        {
            $uid = $mybb->user['uid'];
            if ($uid == 0 && empty($mybb->input['username'])) {
                error($lang->advadsman_error_loginrequired);
            }

            $aid = (int) $mybb->input['aid'];
            $query = $db->simple_select(
				'advadsman_ads', 'urlc,imagec,zone', 
				"aid = '{$aid}' AND uid = '{$uid}'"
			);
            if ($db->num_rows($query) != 1) {
                error($lang->advadsman_error_invalidad);
            } else {
                $ad = $db->fetch_array($query);
            }

            if ( ! empty($ad['urlc']) || ! empty($ad['imagec'])) {
                error($lang->advadsman_error_alreadyedited);
            }
            
            $zones = advadsman_cache_read('zones', 'all');
            $select_zone = FALSE;
            if ($zones && isset($zones[$ad['zone']])) {
                $select_zone = TRUE;
                $maxdimension = $zones[$ad['zone']]['maxdimension'];
            }

            if ( ! $select_zone) {
                error($lang->advadsman_error_invalidzone);
            }

            $update = array();

            $url = $mybb->input['url'];
            if ( ! empty($url)) {
                if ( ! filter_var($url, FILTER_VALIDATE_URL)) {
                    error($lang->advadsman_error_invalidurl);
                }
                $update['urlc'] = $url;
            }

            $image = $_FILES['imagebrowse'];
            $name = $image['name'];
            if ( ! empty($name)) {
                $result = advadsman_upload_file($image, $maxdimension);
                
                if (isset($result['error'])) {
                    error($result['error']);
                }
   
                $update['imagec'] = $result['path'];
            }

            if (count($update) > 0) {
                $update['disabled'] = 1;
                
                // delay update
                advadsman_db_later(array(
                    'table' => 'advadsman_ads', 
                    'action' => 'update',
                    'data' => $update,
                    'where' => "aid = '{$aid}'"
                ));
                
                // add a new log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_space_doedit_loga, 
                        'data' => $lang->sprintf($lang->advadsman_space_doedit_logd, $aid), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));
                
                redirect('usercp.php?action=advadsman', $lang->advadsman_space_doedit_success);
            } else {
                // nothing to be changed!
                error($lang->advadsman_space_doedit_error);
            }
        }

        eval("\$output = \"" . $templates->get('advadsman_standard_page') . "\";");
        output_page($output);
    }
    
    /*
	 * Check to see if current user group can view an ad space.
	 */
    function advadsman_canview() 
    {
        global $mybb;

		$datacache = $mybb->cache->read('usergroups');
        $permissions = $datacache[$mybb->user['usergroup']];
		if (isset($permissions['advadsman_whodenyview']) && $permissions['advadsman_whodenyview'] == 1) {
			return FALSE;
		}
		
		$permissions = $datacache[$mybb->user['additionalgroups']];
		if (isset($permissions['advadsman_whodenyview']) && $permissions['advadsman_whodenyview'] == 1) {
			return FALSE;
		}

        return TRUE;
    }
	
	/*
	 * Check to see if current user group can add a new ad space.
	 */
    function advadsman_canadd() 
    {
        global $mybb;

        $datacache = $mybb->cache->read('usergroups');
		$permissions = $datacache[$mybb->user['usergroup']];
		if (isset($permissions['advadsman_whocanadd']) && $permissions['advadsman_whocanadd'] == 1) {
			return TRUE;
		}
		
		$permissions = $datacache[$mybb->user['additionalgroups']];
		if (isset($permissions['advadsman_whocanadd']) && $permissions['advadsman_whocanadd'] == 1) {
			return TRUE;
		}

        return FALSE;
    }
}

// Common functions
require_once(ADVADSMAN_PLUGIN_PATH . 'functions.php');
?>