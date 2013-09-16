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

// Directly access will not be allowed.
if ( ! defined('IN_MYBB') || ! defined('ADVADSMAN_VERSION')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB and ADVADSMAN_VERSION are defined.');
}

// Define upload path
if ( ! defined('ADVADSMAN_UPLOAD_PATH')) {
    define('ADVADSMAN_UPLOAD_PATH', MYBB_ROOT . 'uploads/advadsman/');
}

// Is an ajax request defined?
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

$plugins->add_hook('admin_home_menu_quick_access', 'advadsman_admin_quick_access');
/*
 * Quick access from AdminCP homepage to modification settings.
 */
function advadsman_admin_quick_access(&$sub_menu) 
{
    global $mybb, $lang;

    $cache = $mybb->cache->read('advadsman_cache');

    if (! $cache || ! isset($cache['zones'])) {
        return;
    }

	// how many ad spaces do we have?
    $ads = 0;
    if (isset($cache['ads']) && is_array($cache['ads'])) {
        $ads = count($cache['ads']);
    }

    // how many active ad spaces do we have?
    $zones = 0;
    if (isset($cache['zones']) && is_array($cache['zones'])) {
        foreach ($cache['zones'] as $key => $value) {
            if (isset($value['is_ad']) && $value['is_ad'] == 1) {
                $zones++;
            }
        }
    }

    if ($zones == 0) {
        return;
    }

    $lang->load('advadsman', false, true);

    $sub_menu[] = array(
        'id' => 'advadsman',
        'title' => $lang->sprintf($lang->advadsman_home, (int) ($zones - $ads)),
        'link' => 'index.php?module=tools-advadsman&action=ads'
    );
}

$plugins->add_hook('admin_tools_menu', 'advadsman_admin_tools_menu');
/*
 * Add a new menu item into "Tools & Maintenance" section.
 */
function advadsman_admin_tools_menu(&$sub_menu) 
{
    global $lang;

    $lang->load('advadsman', false, true);

    $sub_menu[] = array(
        'id' => 'advadsman',
        'title' => $lang->advadsman_mod_title,
        'link' => 'index.php?module=tools-advadsman'
    );
}

$plugins->add_hook('admin_tools_action_handler', 'advadsman_admin_tools_action_handler');
/*
 * Controller for plugin administration.
 */ 
function advadsman_admin_tools_action_handler(&$actions) 
{
    $actions['advadsman'] = array(
        'active' => 'advadsman',
        'file' => 'advadsman'
    );
}

$plugins->add_hook('admin_tools_permissions', 'advadsman_admin_permissions');
/*
 * Permissions for plugin administration.
 */
function advadsman_admin_permissions(&$admin_permissions) 
{
    global $lang;

    $lang->load('advadsman', false, true);

    $admin_permissions['advadsman'] = $lang->advadsman_permissions_canmanage;
}

$plugins->add_hook('admin_user_groups_edit_graph_tabs', 'advadsman_group_permissions_tab');
/*
 * Creates a new permissions tab for each usergroup.
 */
function advadsman_group_permissions_tab(&$tab)
{
	global $lang;

	$lang->load('advadsman');
	$tab['advadsman'] = $lang->advadsman_mod_title;
}
	
$plugins->add_hook('admin_user_groups_edit_graph', 'advadsman_group_permissions');
/*
 * Edit user group permissions related with this plugin.
 */
function advadsman_group_permissions()
{
	global $lang, $form, $mybb;

	$lang->load('advadsman');

	echo '<div id="tab_advadsman">';
	$form_container = new FormContainer($lang->advadsman_mod_title);
	$advadsman_options = array(
		$form->generate_check_box('advadsman_whocanadd', 1, $lang->advadsman_whocanadd, array("checked" => (int)$mybb->input['advadsman_whocanadd'])),
		$form->generate_check_box('advadsman_whodenyview', 1, $lang->advadsman_whodenyview, array("checked" => (int)$mybb->input['advadsman_whodenyview']))
	);
	$form_container->output_row($lang->advadsman_mod_title, "", "<div class=\"group_settings_bit\">" . implode("</div><div class=\"group_settings_bit\">", $advadsman_options) . "</div>");
	$form_container->end();
	echo '</div>';
}
	
$plugins->add_hook('admin_user_groups_edit_commit', 'advadsman_group_permissions_save');
/*
 * Commit all changes after editing user group permissions.
 */
function advadsman_group_permissions_save()
{
	global $mybb, $updated_group;

	$updated_group['advadsman_whocanadd'] = (int)$mybb->input['advadsman_whocanadd'];
	$updated_group['advadsman_whodenyview'] = (int)$mybb->input['advadsman_whodenyview'];
}

$plugins->add_hook('admin_page_output_footer', 'advadsman_settings_footer');
function advadsman_settings_footer()
{
	global $mybb, $db;
	
	// we're viewing the settings form but not submitting it
	if($mybb->input['action'] == 'change' && $mybb->request_method != 'post')
	{
		echo '<script type="text/javascript">
		Event.observe(window, "load", function() {
			// workaround to avoid doing another database query 
			if ($("row_setting_advadsman_setting_enable") != undefined) {
				loadAdvAdsManPeekers();
			}
		});
		function loadAdvAdsManPeekers() {
			new Peeker($$(".setting_advadsman_setting_gae"), $("row_setting_advadsman_setting_gaad"), /1/, true);
            new Peeker($$(".setting_advadsman_setting_gae"), $("row_setting_advadsman_setting_gaadtime"), /1/, true);
			new Peeker($("setting_advadsman_setting_pointsys"), $("row_setting_advadsman_setting_pointsysname"), /other/, false);
			new Peeker($("setting_advadsman_setting_pointsys"), $("row_setting_advadsman_setting_pointsyscol"), /other/, false);
		}
		</script>';
	}
}

$plugins->add_hook('admin_config_settings_change', 'advadsman_settings_change');
function advadsman_settings_change()
{
	global $mybb, $db;
	
	$updated = $mybb->input['upsetting'];
	if ($mybb->request_method == 'post' && isset($updated['advadsman_setting_pointsys']) &&
		($value = $updated['advadsman_setting_pointsys']) != 'none') 
	{
		$field = 'newpoints';
		$table = 'users';
		if ($value == 'other') {
			if (isset($updated['advadsman_setting_pointsyscol'])) {
				$field = $db->escape_string($updated['advadsman_setting_pointsyscol']);
			} else {
				$field = $db->escape_string($mybb->settings['advadsman_setting_pointsyscol']);
			}
		}
		
		if ( ! $db->field_exists($field, $table) {
			unset($mybb->input['upsetting']['advadsman_setting_pointsys']);
		}
	}
}

$plugins->add_hook('admin_config_plugins_deactivate_commit', 'advadsman_configplugins_deactivate');
function advadsman_configplugins_deactivate()
{
	global $codename, $mybb;
	
	if ($mybb->settings['advadsman_setting_pointsys'] == $codename &&
		$codename == 'newpoints')
	{
		// TO DO
	}	
}

$plugins->add_hook('admin_config_plugins_activate_commit', 'advadsman_configplugins_activate');
function advadsman_configplugins_activate()
{
	// TO DO	
}

function advadsman_get_upgrades() 
{
    $upgrades_list = array();

    // which directory will be used
    $dir = @opendir(ADVADSMAN_PLUGIN_PATH . 'upgrades/');

    // read file by file
    if ($dir) 
	{
        while ($file = readdir($dir)) 
		{
            if ($file == '.' || $file == '..') {
                continue;
			}
            
            if ( ! is_dir(ADVADSMAN_PLUGIN_PATH . 'upgrades/' . $file)) 
			{
                $ext = get_extension($file);
                if ($ext == 'php') {
                    // check file format
                    if (preg_match('/upgrade_([0-9]{3})_([0-9]{3})\.php/i', $file, $matches) &&
                            version_compare($matches[1], str_replace('.', '', ADVADSMAN_VERSION), '==')) {
                        $upgrades_list[] = array(
                            'file' => $file,
                            'from' => $matches[1],
                            'to' => $matches[2]
                        );
                    }
                }
            }
        }
        
        // sort using "to" field
        $lambda = create_function('$a,$b', 
                'return (intval($a[\'to\']) - intval($b[\'to\']));');
		// sort keys
        usort($upgrades_list, $lambda);
        
        // close current directory
        @closedir($dir);
    }
	
    // results are needed
    return $upgrades_list;
}

$plugins->add_hook('admin_load', 'advadsman_adminpage');
/*
 * Main administration function.
 */
function advadsman_adminpage() 
{
    global $db, $lang, $mybb, $page, $run_module, $action_file;

    if ($run_module == 'tools' && $action_file == 'advadsman') 
    {
        $lang->load('advadsman', false, true);

        if (IS_AJAX) {
            switch($mybb->input['action']) {
                case 'do_rebuild_cache' :
                    advadsman_cache_massupdate();
                    echo $lang->advadsman_rebuild_success;
                break;
                case 'do_rebuild_template' :
                    // firstly, we need to remove all changes
                    advadsman_remove_templates();
                    // secondly, we need to promote all changes
                    advadsman_insert_templates();
                    echo $lang->advadsman_rebuild_temp_success;
                break;
                case 'check_strtotime' :
                    echo ((strtotime($mybb->input['value']) === FALSE) ? 0 : 1);
                break;
                case 'check_fileexists' :
                    echo (( ! file_exists('../' . $mybb->input['value'])) ? 0 : 1);
                break;
            }
            exit();
        }

        $page->add_breadcrumb_item($lang->advadsman_mod_title, 'index.php?module=tools-advadsman');

        $sub_tabs['infos'] = array(
            'title' => $lang->advadsman_infos,
            'link' => 'index.php?module=tools-advadsman',
            'description' => $lang->advadsman_infos_desc
        );
        $sub_tabs['zones'] = array(
            'title' => $lang->advadsman_zones,
            'link' => 'index.php?module=tools-advadsman&amp;action=zones',
            'description' => $lang->advadsman_zones_desctab
        );
        $sub_tabs['ads'] = array(
            'title' => $lang->advadsman_ads,
            'link' => 'index.php?module=tools-advadsman&amp;action=ads',
            'description' => $lang->advadsman_ads_desc
        );
		$sub_tabs['upgrades'] = array(
            'title' => $lang->advadsman_upgrades,
            'link' => 'index.php?module=tools-advadsman&amp;action=upgrades',
            'description' => $lang->advadsman_upgrades_desc
		);
        $sub_tabs['logs'] = array(
            'title' => $lang->advadsman_logs,
            'link' => 'index.php?module=tools-advadsman&amp;action=logs',
            'description' => $lang->advadsman_logs_desc
		);

        // switch between possible pages
        if (!$mybb->input['action']) 
        {
            $page->extra_header .= '<script type="text/javascript" src="../jscripts/scriptaculous.js?load=effects"></script>
            <script type="text/javascript">
				Event.observe(window, \'load\', function() {
                    $(\'message\').hide();
				});
				function doRebuildCache() {
                    new Ajax.Request("index.php?module=tools-advadsman", {
						parameters: { action: "do_rebuild_cache" },
						onComplete: function(data) { 
                            if(data.responseText) {
                                var answer = $("message");
								answer.innerHTML = data.responseText;
								answer.setStyle("color: #4F8A10; background-color: #DFF2BF; font-weight: bold; border: 1px solid; padding: 10px 15px 10px 15px;");
								answer.appear({ delay: 0.1 });
								answer.fade({ duration: 5, from: 1, to: 0 });
                            }
						},
						onFailure: function(data) {
                            var answer = $("message");
                            answer.innerHTML = "' . $lang->advadsman_rebuild_error . '";
                            answer.setStyle("color: #D8000C; background-color: #FFBABA; font-weight: bold; border: 1px solid; padding: 10px 15px 10px 15px;");
                            answer.appear({ delay: 0.1 });
                            answer.fade({ duration: 5, from: 1, to: 0 });
						}
                    });
				}
                function doRebuildTemplate() {
                    new Ajax.Request("index.php?module=tools-advadsman", {
						parameters: { action: "do_rebuild_template" },
						onComplete: function(data) { 
                            if(data.responseText) {
                                var answer = $("message");
								answer.innerHTML = data.responseText;
								answer.setStyle("color: #4F8A10; background-color: #DFF2BF; font-weight: bold; border: 1px solid; padding: 10px 15px 10px 15px;");
								answer.appear({ delay: 0.1 });
								answer.fade({ duration: 5, from: 1, to: 0 });
                            }
						},
						onFailure: function(data) {
                            var answer = $("message");
                            answer.innerHTML = "' . $lang->advadsman_rebuild_temp_error . '";
                            answer.setStyle("color: #D8000C; background-color: #FFBABA; font-weight: bold; border: 1px solid; padding: 10px 15px 10px 15px;");
                            answer.appear({ delay: 0.1 });
                            answer.fade({ duration: 5, from: 1, to: 0 });
						}
                    });
				}
            </script>';
            $page->output_header($lang->advadsman_mod_title);
            $page->output_nav_tabs($sub_tabs, 'infos');

            // right section
            echo '<div style="overflow: auto; width: 100%">
			<div class="float_right" style="width: 49%;">';

            echo $lang->advadsman_infos_text1;

            // right permissions for upload directory?
            $perm = substr(sprintf('%o', fileperms(ADVADSMAN_UPLOAD_PATH)), -4);
            $required = '0755';
            
			// build table
            $table = new Table;
            $table->construct_header($lang->advadsman_infos_directory, array('width' => '50%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_infos_current, array('width' => '25%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_infos_recommended, array('width' => '25%', 'class' => 'align_center'));

            $table->construct_cell('<strong>./uploads/advadsman/</strong>', array('class' => 'align_center'));
            if ($perm < $required) {
                $table->construct_cell("<font style='color: red; font-weight: bold'>$perm</font", array('class' => 'align_center'));
            } else {
                $table->construct_cell("<font style='color: green; font-weight: bold'>$perm</font", array('class' => 'align_center'));
            }
            $table->construct_cell($required, array('class' => 'align_center'));
            $table->construct_row();

            // display table
            $table->output($lang->advadsman_infos_table);

            // rebuild cache
            echo '<div id="message"></div><div class="form_button_wrapper" style="padding: 10px;"><input type="button" name="check" id="cache_button" value="' . $lang->advadsman_rebuild_cache . '" class="submit_button" onclick="doRebuildCache();" /><br/><input type="button" name="check" id="template_button" value="' . $lang->advadsman_rebuild_template . '" class="submit_button" onclick="doRebuildTemplate();" /></div>';

            echo '</div><div class="float_left" style="width:49%;">';
            // intern statistics
            // step 1 : read data from cache
            $datacache = $mybb->cache->read('advadsman_cache');
            $total_money = $total_free = $next_expire = $total_zones = $total_ads = $need_approve = 0;
            // step 2 : generate results
            if ($datacache && is_array($datacache)) {
                if (isset($datacache['stats']) && isset($datacache['stats']['total_spend'])) {
                    $total_money = (int) $datacache['stats']['total_spend'];
                }
                if (isset($datacache['zones']) && is_array($datacache['zones'])) {
                    $total_zones = count($datacache['zones']);

                    foreach ($datacache['zones'] as $zid => $zone) {
                        if (isset($zone['is_ad']) && $zone['is_ad'] == 0) {
                            $total_free++;
                        }
                    }
                }
                if (isset($datacache['ads']) && is_array($datacache['ads'])) {
                    $total_ads = count($datacache['ads']);

                    $timestamp = strtotime('+1 week');
                    foreach ($datacache['ads'] as $zone => $ad) {
                        if (isset($ad['expire']) && $ad['expire'] < $timestamp) {
                            $next_expire++;
                        }
                    }
                }
                $need_approve = $total_zones - $total_free - $total_ads;
                if ($need_approve < 0) {
                    $need_approve = 0;
                }
            }
            // step 3 : send results to user
            $lang->advadsman_infos_text = $lang->sprintf($lang->advadsman_infos_text, $total_money, $total_free, $total_zones, $next_expire, $total_ads, $need_approve);
            $donate = '<center><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="D8N958QBWT5XY"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form></center>';
            echo $lang->advadsman_infos_text . $donate . "<br/>";
            echo '</div></div>';

            // team management
            $table = new Table;
            $table->construct_header($lang->advadsman_infos_credit_name, array('width' => '30%'));
            $table->construct_header($lang->advadsman_infos_credit_role, array('width' => '40%'));
            $table->construct_header($lang->advadsman_infos_credit_website, array('width' => '30%'));

            $table->construct_cell('Surdeanu Mihai', array('class' => 'align_center'));
            $table->construct_cell('Product Manager & Developer', array('class' => 'align_center'));
            $table->construct_cell('<a href="http://extramybb.com" target="_blank">http://extramybb.com</a>', array('class' => 'align_center'));
            $table->construct_row();

            $table->construct_cell('Harald R&#259;zvan', array('class' => 'align_center'));
            $table->construct_cell('Developer & Quality Assurance Specialist', array('class' => 'align_center'));
            $table->construct_cell('<a href="http://extramybb.com" target="_blank">http://extramybb.com</a>', array('class' => 'align_center'));
            $table->construct_row();

            // display table
            echo $table->output($lang->advadsman_infos_credit);
        } else if ($mybb->input['action'] == 'zones') 
        {
            $page->output_header($lang->advadsman_mod_title);
            $page->output_nav_tabs($sub_tabs, 'zones');
            
            // we do no need pagination because we are sure that will be only few zones

            // zones table
            $table = new Table;
            $table->construct_header($lang->advadsman_zones_name, array('width' => '15%'));
            $table->construct_header($lang->advadsman_zones_desc, array('width' => '35%'));
            $table->construct_header($lang->advadsman_zones_price, array('width' => '10%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_zones_code, array('width' => '20%'));
            $table->construct_header($lang->advadsman_zones_has, array('width' => '10%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_zones_actions, array('width' => '10%', 'class' => 'align_center'));

            $query = $db->simple_select('advadsman_zones', '*');

            while ($zone = $db->fetch_array($query)) {
                $table->construct_cell(htmlspecialchars_uni($zone['name']));
                $table->construct_cell(htmlspecialchars_uni($zone['description']));
                $table->construct_cell(newpoints_format_points($zone['points']), array('class' => 'align_center'));

                // identification code
                if ($zone['zid'] != 2) {
                    $table->construct_cell(sprintf('<!--advadsman_z%s-->', $zone['zid']));
                } else {
                    $table->construct_cell('$post[\'advadsman_ads\']');
                }

                // has attached space?
                if ((int) $zone['is_ad'] == 0) {
                    $table->construct_cell($lang->advadsman_buttons_no, array('class' => 'align_center'));
                } else {
                    $table->construct_cell($lang->advadsman_buttons_yes, array('class' => 'align_center'));
                }

                $popup = new PopupMenu("zones_{$zone['zid']}", $lang->advadsman_zones_actions);
                $popup->add_item($lang->advadsman_zones_actions_edit, 'index.php?module=tools-advadsman&amp;action=zone_edit&amp;zid=' . (int) $zone['zid']);
                // some zones cannot be deleted
                if ($zone['zid'] > 3) {
                    $popup->add_item($lang->advadsman_zones_actions_delete, 'index.php?module=tools-advadsman&amp;action=zone_delete&amp;zid=' . (int) $zone['zid']);
                }
                $table->construct_cell($popup->fetch(), array('class' => 'align_center'));

                $table->construct_row();
            }

            if ($table->num_rows() == 0) {
                $table->construct_cell($lang->advadsman_zones_nozones, array('colspan' => 6));
                $table->construct_row();
            }
			
            $table->output($lang->advadsman_zones . '<div class="float_right"><a href="index.php?module=tools-advadsman&amp;action=zone_add"><img src="../images/advadsman/add.png" border="0" title="Add" /></a></div>');
        } elseif ($mybb->input['action'] == 'zone_add') 
        {
            if ($mybb->request_method == 'post') {
                $name = $db->escape_string($mybb->input['name']);
                $maxdimension = $mybb->input['maxdimension'];
                $points = (int) $mybb->input['points'];

                if (empty($name) || $points < 0
                        || !preg_match("/^([0-9]{3})x([0-9]{2,3})$/", $maxdimension)) {
                    flash_message($lang->advadsman_zones_add_error1, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=zone_add');
                }
                if ($zone = $db->fetch_array($db->simple_select('advadsman_zones', '*', "name = '$name'"))) {
                    flash_message($lang->advadsman_zones_add_error2, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=zone_add');
                }

                $insert = array(
                    'name' => $name,
                    'description' => $db->escape_string($mybb->input['description']),
                    'maxdimension' => $mybb->input['maxdimension'],
                    'points' => $points,
                    'is_ad' => 0
                );

                // add zone to database	
                $id = $db->insert_query('advadsman_zones', $insert);

                // update cache
                advadsman_cache_update('zones', $id, $insert);
                
                // add log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_zones_add_loga, 
                        'data' => $lang->sprintf($lang->advadsman_zones_add_logd, $id), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));

                flash_message($lang->advadsman_zones_add_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
            } else {
                // javascript code used for checking maximal dimensions accepted
                $page->extra_header .= '<script type="text/javascript">
				Event.observe(document, "dom:loaded", function() {
                    $(\'maxdimension\').observe(\'change\', function() {
                    	var value = this.getValue();
                    	if (value.match(/^([0-9]{3})x([0-9]{2,3})$/)) {
                            this.setStyle({ background: \'#BCF5A9\' });
                    	}
                    	else {
                            this.setStyle({ background: \'#F5A9BC\' });
                    	}
                    });
				});
				</script>';
                $page->output_header($lang->advadsman_mod_title);
                $page->output_nav_tabs($sub_tabs, 'zones');

                // form to add a new zone
                $form = new Form('index.php?module=tools-advadsman&amp;action=zone_add', 'post', 'advadsman');

                $form_container = new FormContainer($lang->advadsman_zones_add_form);

                $form_container->output_row($lang->advadsman_zones_add_name, $lang->advadsman_zones_add_name_desc, $form->generate_text_box('name', '', array('id' => 'name')), 'name');
                $form_container->output_row($lang->advadsman_zones_add_description, $lang->advadsman_zones_add_description_desc, $form->generate_text_box('description', '', array('id' => 'description')), 'description');
                $form_container->output_row($lang->advadsman_zones_add_maxdimension, $lang->advadsman_zones_add_maxdimension_desc, $form->generate_text_box('maxdimension', '', array('id' => 'maxdimension')), 'maxdimension');
                $form_container->output_row($lang->advadsman_zones_add_points, $lang->advadsman_zones_add_points_desc, $form->generate_text_box('points', 0, array('id' => 'points')), 'points');

                $form_container->end();

                // show form buttons
                $buttons = array();
                $buttons[] = $form->generate_submit_button($lang->advadsman_buttons_submit);
                $buttons[] = $form->generate_reset_button($lang->advadsman_buttons_reset);
                $form->output_submit_wrapper($buttons);
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'zone_edit') 
        {
            $zid = (int)$mybb->input['zid'];
            if ($zid <= 0 || ( ! ($zone = $db->fetch_array($db->simple_select('advadsman_zones', '*', "zid = {$zid}"))))) {
                flash_message($lang->advadsman_error_invalid, 'error');
                admin_redirect('index.php?module=tools-advadsman');
            }

            if ($mybb->request_method == 'post') {
                $update = array();

                $name = $mybb->input['name'];
                if (!empty($name)) {
                    $update['name'] = $db->escape_string($name);
                }

                $description = $mybb->input['description'];
                if (!empty($description)) {
                    $update['description'] = $db->escape_string($description);
                }

                $maxdimension = $mybb->input['maxdimension'];
                if ( ! empty($maxdimension) && preg_match("/^([0-9]{3})x([0-9]{2,3})$/", $maxdimension)) {
                    $update['maxdimension'] = $maxdimension;
                }

                $points = $mybb->input['points'];
                if ( ! empty($points) && is_numeric($points)) {
                    $update['points'] = $points;
                }

                $posts = $mybb->input['posts'];
                if (!empty($posts) && is_integer($posts)) {
                    $update['posts'] = $posts;
                }
                
                // update database
                $db->update_query('advadsman_zones', $update, "zid = '$zid'");
                
                // update cache with all changes
                if (isset($update['description'])) {
                    unset($update['description']);
                }
                advadsman_cache_update('zones', $zid, $update);
                
                // add log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_zones_edit_loga, 
                        'data' => $lang->sprintf($lang->advadsman_zones_edit_logd, $zid), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));

                flash_message($lang->advadsman_zones_edit_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
            } else {
                $page->extra_header .= '<script type="text/javascript">
				Event.observe(document, "dom:loaded", function() {
                    $(\'maxdimension\').observe(\'change\', function() {
                    	var value = this.getValue();
                    	if (value.match(/^([0-9]{3})x([0-9]{2,3})$/)) {
                            this.setStyle({ background: \'#BCF5A9\' });
                    	}
                    	else {
                            this.setStyle({ background: \'#F5A9BC\' });
                    	}
                    });
				});
				</script>';
                $page->output_header($lang->advadsman_mod_title);
                $page->output_nav_tabs($sub_tabs, 'zones');

                // form to edit a zone
                $form = new Form('index.php?module=tools-advadsman&amp;action=zone_edit', 'post', 'advadsman');

                $form_container = new FormContainer($lang->advadsman_zones_edit_form);

                echo $form->generate_hidden_field('zid', $zid);

                $form_container->output_row($lang->advadsman_zones_edit_name, $lang->advadsman_zones_edit_name_desc, $form->generate_text_box('name', htmlspecialchars_uni($zone['name']), array('id' => 'name')), 'name');
                $form_container->output_row($lang->advadsman_zones_edit_description, $lang->advadsman_zones_edit_description_desc, $form->generate_text_box('description', htmlspecialchars_uni($zone['description']), array('id' => 'description')), 'description');
                $form_container->output_row($lang->advadsman_zones_edit_maxdimension, $lang->advadsman_zones_edit_maxdimension_desc, $form->generate_text_box('maxdimension', htmlspecialchars_uni($zone['maxdimension']), array('id' => 'maxdimension')), 'maxdimension');
                $form_container->output_row($lang->advadsman_zones_edit_points, $lang->advadsman_zones_edit_points_desc, $form->generate_text_box('points', htmlspecialchars_uni($zone['points']), array('id' => 'points')), 'points');
                // current location is Postbit?
                if ($zone['zid'] == 2) {
                    $form_container->output_row($lang->advadsman_zones_edit_posts, $lang->advadsman_zones_edit_posts_desc, $form->generate_text_box('posts', htmlspecialchars_uni($zone['posts']), array('id' => 'posts')), 'posts');
                }

                $form_container->end();

                // show form buttons
                $buttons = array();
                $buttons[] = $form->generate_submit_button($lang->advadsman_buttons_submit);
                $buttons[] = $form->generate_reset_button($lang->advadsman_buttons_reset);
                $form->output_submit_wrapper($buttons);
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'zone_delete') 
        {
            $zid = intval($mybb->input['zid']);

            // default zones cannot be deleted by anyone
            if (!$zid || $zid < 4) {
                flash_message($lang->advadsman_error_invalid, 'error');
                admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
            }

            // action confirmed?
            if ($mybb->input['no']) {
                admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
            }

            if ($mybb->request_method == 'post') {
                if ($zid <= 0 || (!($zone = $db->fetch_array($db->simple_select('advadsman_zones', 'zid', "zid = '$zid'"))))) {
                    flash_message($lang->advadsman_error_invalid, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
                }
                
                // there are ad spaces associated?
                if ($db->num_rows($db->simple_select('advadsman_ads', 'aid', "zone = '$zid'")) > 0) {
                    flash_message($lang->advadsman_zones_delete_hasads, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
                }

                // delete zone
                $db->delete_query('advadsman_zones', "zid = '$zid'");

                // update cache
                advadsman_cache_delete('zones', $zid);
                
                // add log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_zones_delete_loga, 
                        'data' => $lang->sprintf($lang->advadsman_zones_delete_logd, $zid), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));

                flash_message($lang->advadsman_zones_delete_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=zones');
            } else {
                $page->output_header($lang->advadsman_mod_title);

                $form = new Form("index.php?module=tools-advadsman&amp;action=zone_delete&amp;zid={$zid}&amp;my_post_key={$mybb->post_code}", 'post');
                echo "<div class=\"confirm_action\">\n";
                echo "<p>{$lang->advadsman_mod_confirm_desc}</p>\n";
                echo "<br />\n";
                echo "<p class=\"buttons\">\n";
                echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
                echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
                echo "</p>\n";
                echo "</div>\n";
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'ads') 
        {
            $page->output_header($lang->advadsman_mod_title);
            $page->output_nav_tabs($sub_tabs, 'ads');

            // build table
            $table = new Table;
            $table->construct_header($lang->advadsman_ads_user, array('width' => '15%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_ads_create, array('width' => '20%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_ads_expire, array('width' => '20%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_ads_url, array('width' => '25%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_ads_active, array('width' => '10%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_ads_options, array('width' => '10%', 'class' => 'align_center'));

            // build query
            $query = $db->query("
            	SELECT u.*, u.username AS userusername, a.*
            	FROM " . TABLE_PREFIX . "advadsman_ads a
            	LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = a.uid)
            	ORDER BY a.disabled DESC, a.date ASC
            ");

            // draw table row by row
            while ($row = $db->fetch_array($query)) {
                if ($row['uid'] > 0) {
                    $user = build_profile_link(htmlspecialchars_uni($row['userusername']), $row['uid']);    
                } else {
                    $user = 'System';
                }
                $table->construct_cell($user, array('class' => 'align_center'));
                $table->construct_cell(my_date($mybb->settings['dateformat'], $row['date'], '', false) . ', ' . my_date($mybb->settings['timeformat'], $row['date']), array('class' => 'align_center'));
                $table->construct_cell(my_date($mybb->settings['dateformat'], $row['expire'], '', false) . ', ' . my_date($mybb->settings['timeformat'], $row['expire']), array('class' => 'align_center'));
                $table->construct_cell($db->escape_string($row['url']), array('class' => 'align_center'));
                if (!empty($row['urlc']) || !empty($row['imagec'])) {
                    $table->construct_cell($lang->advadsman_buttons_yes, array('class' => 'align_center', 'style' => 'background-color: #ADA96E'));
                } else {
                    $info = array();
                    if ((int) $row['disabled'] > 0) {
                        $info['display'] = $lang->advadsman_buttons_no;
                        $info['color'] = '#EDDA74';
                    } else {
                        $info['display'] = $lang->advadsman_buttons_yes;
                        $info['color'] = '#99C68E';
                    }
                    $table->construct_cell($info['display'], array('class' => 'align_center', 'style' => 'background-color:' . $info['color']));
                }

                $popup = new PopupMenu("activeads_{$row['aid']}", $lang->advadsman_ads_actions);
                $popup->add_item($lang->advadsman_ads_actions_edit, 'index.php?module=tools-advadsman&amp;action=ad_edit&amp;aid=' . (int) ($row['aid']));
                if ((int) $row['disabled'] == 1) {
                    $popup->add_item($lang->advadsman_ads_actions_fapprove, 'index.php?module=tools-advadsman&amp;action=ad_approve&amp;aid=' . (int) ($row['aid']));
                } else if ((int) $row['disabled'] == 2) {
                    $popup->add_item($lang->advadsman_ads_actions_approve, 'index.php?module=tools-advadsman&amp;action=ad_status&amp;aid=' . (int) ($row['aid']));
                } else {
                    $popup->add_item($lang->advadsman_ads_actions_disapprove, 'index.php?module=tools-advadsman&amp;action=ad_status&amp;aid=' . (int) ($row['aid']));
                }
                $popup->add_item($lang->advadsman_ads_actions_delete, 'index.php?module=tools-advadsman&amp;action=ad_delete&amp;aid=' . (int) ($row['aid']));
                $table->construct_cell($popup->fetch(), array('class' => 'align_center'));

                $table->construct_row();
            }

            $rows = $table->num_rows();

            if ($rows == 0) {
                $table->construct_cell($lang->advadsman_ads_noresults, array('class' => 'align_center', 'colspan' => 7));
                $table->construct_row();
            }

            // sent table to user
            $table->output($lang->advadsman_ads_activeads . '<div class="float_right"><a href="index.php?module=tools-advadsman&amp;action=ad_add"><img src="../images/advadsman/add.png" border="0" title="Add" /></a></div>');

            // show legend
            if ($rows > 0) {
                echo "<fieldset><legend>{$lang->advadsman_ads_legend}</legend>
                <font color='#99C68E'><strong>{$lang->advadsman_legend_active}</strong></font> &bull; {$lang->advadsman_legend_active_text}<br />
		        <font color='#EDDA74'><strong>{$lang->advadsman_legend_inactive}</strong></font> &bull; {$lang->advadsman_legend_inactive_text}<br />
		        <font color='#ADA96E'><strong>{$lang->advadsman_legend_edited}</strong></font> &bull; {$lang->advadsman_legend_edited_text}
		        </fieldset>";
            }
        } elseif ($mybb->input['action'] == 'ad_add') 
        {
            if ($mybb->request_method == 'post') {
                // empty fields found?
                if (empty($mybb->input['zone']) || empty($mybb->input['period']) || empty($mybb->input['url'])) {
                    flash_message($lang->advadsman_ads_doadd_error1, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ad_add');
                }
                // period of time must be integer
                if ( ! is_numeric($mybb->input['period']) || ! filter_var($mybb->input['url'], FILTER_VALIDATE_URL)) {
                    flash_message($lang->advadsman_ads_doadd_error1, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ad_add');
                }
                $period = (int) $mybb->input['period'];
                $zones = advadsman_cache_read('zones');
                $max = $zones[$mybb->input['zone']];
                
                // upload image to server
                $image = $_FILES['image'];
                $result = advadsman_upload_file($image, $max['maxdimension']);
                if (isset($result['error'])) {
                    flash_message($lang->advadsman_ads_doadd_error2, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ad_add');
                }
                
                // when ad space expire?
                $expire = TIME_NOW + 30 * 86400 * $period;
                
                // add log to database
                $insert = array(
                    'uid' => 0,
                    'date' => TIME_NOW,
                    'expire' => $expire,
                    'url' => $db->escape_string($mybb->input['url']),
                    'image' => $result['path'],
                    'zone' => (int)$mybb->input['zone'],
                    'width' => $result['width']
                );
                // insert with delay
                advadsman_db_later(array(
                    'table' => 'advadsman_ads', 
                    'action' => 'insert',
                    'data' => $insert
                ));

                // that zone cannot be selected anymore...
                $db->update_query('advadsman_zones', array('is_ad' => 1), "zid = '" . (int)$mybb->input['zone'] . "'");

                // update intern cache
                advadsman_cache_update('zones', (int)$mybb->input['zone'], array('is_ad' => 1));
            
                // add log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_ads_doadd_loga, 
                        'data' => $lang->sprintf($lang->advadsman_ads_doadd_logd, (int) $mybb->input['period']), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));
                
                flash_message($lang->advadsman_ads_doadd_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            } else {
                $page->output_header($lang->advadsman_mod_title);
                $page->output_nav_tabs($sub_tabs, 'ads');
                
                // read zones from cache
                $zones = advadsman_cache_read('zones');
                $zone = array();
                foreach ($zones as $zid => $details) {
                    $zone[$zid] = $details['name'];
                }

                $form = new Form('index.php?module=tools-advadsman&amp;action=ad_add', 'post', 'advadsman', 1);

                $form_container = new FormContainer($lang->advadsman_ads_add_title);

                $form_container->output_row($lang->advadsman_ads_add_zone, $lang->advadsman_ads_add_zone_desc, $form->generate_select_box('zone', $zone, array('id' => 'zone', 'multiple' => FALSE)), 'zone');
                $form_container->output_row($lang->advadsman_ads_add_period, $lang->advadsman_ads_add_period_desc, $form->generate_text_box('period', '', array('id' => 'period')), 'period');
                $form_container->output_row($lang->advadsman_ads_add_url, $lang->advadsman_ads_add_url_desc, $form->generate_text_box('url', '', array('id' => 'url')), 'url');
                $form_container->output_row($lang->advadsman_ads_add_image, $lang->advadsman_ads_add_image_desc, $form->generate_file_upload_box('image', array('id' => 'image')), 'image');
                $form_container->end();

                $buttons = "";
                $buttons[] = $form->generate_submit_button($lang->advadsman_buttons_submit);
                $buttons[] = $form->generate_reset_button($lang->advadsman_buttons_reset);
                $form->output_submit_wrapper($buttons);
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'ad_edit') 
        {
            $aid = intval($mybb->input['aid']);

            if ($aid <= 0 || !($ad = $db->fetch_array($db->simple_select('advadsman_ads', "*", "aid = '$aid'", array('limit' => 1))))) {
                flash_message($lang->advadsman_error_invalid, 'error');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            }

            if ($mybb->request_method == 'post') {
                $update = array();

                $uid = (int) $mybb->input['uid'];
                if (!empty($uid)) {
                    $user = get_user($uid);
                    if (is_array($user)) {
                        $update['uid'] = $uid;
                    }
                }

                $groups = implode(',', $mybb->input['groups']);
                $update['groups'] = $groups;

                $url = $db->escape_string($mybb->input['url']);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $update['url'] = $url;
                }

                $image = $db->escape_string($mybb->input['image']);
                if (!empty($image) && file_exists(MYBB_ROOT . $image)) {
                    $update['image'] = $image;
                    $img = @getimagesize(MYBB_ROOT. $image);
                    $update['width'] = $img[0];
                }

                $time = $db->escape_string($mybb->input['time']);
                $new = strtotime($time, $ad['expire']);
                if (!empty($time) && $new !== FALSE) {
                    $update['expire'] = $new;
                }

                if (count($update) > 0) {
                    $db->update_query('advadsman_ads', $update, "aid = '$aid'");

                    if (isset($update['uid'])) {
                        unset($update['uid']);
                    }
                    advadsman_cache_update('ads', $ad['zone'], $update);
                    
                    advadsman_db_later(array(
                        'table' => 'advadsman_logs', 
                        'action' => 'insert',
                        'data' => array(
                            'action' => $lang->advadsman_ads_edit_loga, 
                            'data' => $lang->sprintf($lang->advadsman_ads_edit_logd, $aid), 
                            'date' => TIME_NOW, 
                            'user' => $mybb->user['username']
                        )
                    ));
                }
				
                flash_message($lang->advadsman_ads_edit_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            } else {
                $page->extra_header .= '<script type="text/javascript">
				Event.observe(document, "dom:loaded", function() {
                    $(\'time\').observe(\'change\', function() {
                    	new Ajax.Request("index.php?module=tools-advadsman", {
                            parameters: { action: "check_strtotime", value : this.getValue() },
                            onComplete: function(data) {
                                if (data.responseText == "1") {
                                    $(\'time\').setStyle("background: #BCF5A9");
                                } else {
                                    $(\'time\').setStyle("background: #F5A9BC");
                                }
                            }
                        }); 
                    });
                    $(\'image\').observe(\'change\', function() {
                    	new Ajax.Request("index.php?module=tools-advadsman", {
                            parameters: { action: "check_fileexists", value : this.getValue() },
                            onComplete: function(data) {
                                if (data.responseText == "1") {
                                    $(\'image\').setStyle("background: #BCF5A9");
                                } else {
                                    $(\'image\').setStyle("background: #F5A9BC");
                                }
                            }
                        }); 
                    });
				});
				</script>';
                $page->output_header($lang->advadsman_mod_title);
                $page->output_nav_tabs($sub_tabs, 'ads');

                $form = new Form('index.php?module=tools-advadsman&amp;action=ad_edit', 'post', 'advadsman');


                $form_container = new FormContainer($lang->advadsman_ads_edit_title);

                echo $form->generate_hidden_field('aid', $aid);

                $form_container->output_row($lang->advadsman_ads_edit_uid, $lang->advadsman_ads_edit_uid_desc, $form->generate_text_box('uid', (int) $ad['uid'], array('id' => 'uid')), 'uid');
                $form_container->output_row($lang->advadsman_ads_edit_groups, $lang->advadsman_ads_edit_groups_desc, $form->generate_group_select('groups[]', explode(',', $ad['groups']), array('id' => 'groups', 'multiple' => TRUE)), 'groups');
                $form_container->output_row($lang->advadsman_ads_edit_url, $lang->advadsman_ads_edit_url_desc, $form->generate_text_box('url', $ad['url'], array('id' => 'url')), 'url');
                $form_container->output_row($lang->advadsman_ads_edit_image, $lang->advadsman_ads_edit_image_desc, $form->generate_text_box('image', $ad['image'], array('id' => 'image')), 'image');
                $lang->advadsman_ads_edit_relative_desc = $lang->sprintf($lang->advadsman_ads_edit_relative_desc, my_date($mybb->settings['dateformat'], $ad['expire']));
                $form_container->output_row($lang->advadsman_ads_edit_relative, $lang->advadsman_ads_edit_relative_desc, $form->generate_text_box('time', '', array('id' => 'time')), 'time');
                $form_container->end();

                $buttons = "";
                $buttons[] = $form->generate_submit_button($lang->advadsman_buttons_submit);
                $buttons[] = $form->generate_reset_button($lang->advadsman_buttons_reset);
                $form->output_submit_wrapper($buttons);
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'ad_status') 
        {
            if ( ! ($ad = $db->fetch_array($db->simple_select('advadsman_ads', '*', 'aid = ' . (int) $mybb->input['aid'])))) {
                flash_message($lang->advadsman_error_invalid, 'error');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            }
			
            if ($mybb->input['no']) {
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            }

            if ($mybb->request_method == 'post') {
                if (!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key']) {
                    $mybb->request_method = 'get';
                    flash_message($lang->advadsman_error_unknown, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
                }

                $add_update = array(
					'disabled' => '!disabled'
				);
                if ($ad['disabled'] == 2) {
                    $diff = TIME_NOW - $ad['date'];
					$add_update['expire'] = 'expire + ' . $diff;
                }
				
                $db->update_query('advadsman_ads', $add_update, "aid = '" . (int)$mybb->input['aid'] . "'", true);

                advadsman_cache_massupdate(2);
                
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_ads_status_loga, 
                        'data' => $lang->sprintf($lang->advadsman_ads_status_logd, (int) $mybb->input['aid']), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));

                flash_message($lang->advadsman_ads_status_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            } else {
                $page->output_header($lang->advadsman_mod_title);

                // convert output to integer
                $aid = (int) $mybb->input['aid'];
                $form = new Form("index.php?module=tools-advadsman&amp;action=ad_status&amp;aid={$aid}&amp;my_post_key={$mybb->post_code}", 'post');
                
                if ($ad['disabled'] == 2) {
                    $additional = '<ul type="square">';
                    $path = $mybb->settings['bburl'] . '/';
                    $additional .= $lang->sprintf($lang->advadsman_ads_approve_text_features, $ad['zone'], $ad['url'], $path . $ad['image']);
                    $additional .= '</ul>';
                
                    $lang->advadsman_ads_status_text = $lang->sprintf($lang->advadsman_ads_status_text, $additional);   
                } else {
                    $lang->advadsman_ads_status_text = $lang->advadsman_mod_confirm_desc;
                }
                echo "<div class=\"confirm_action\">\n";
                echo "<p>{$lang->advadsman_ads_status_text}</p>\n";
                echo "<br />\n";
                echo "<p class=\"buttons\">\n";
                echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
                echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
                echo "</p>\n";
                echo "</div>\n";
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'ad_approve') 
        {
            $aid = (int)$mybb->input['aid'];

            if ( ! ($ad = $db->fetch_array($db->simple_select('advadsman_ads', '*', "aid = '$aid'")))) {
                flash_message($lang->advadsman_error_invalid, 'error');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            }

            // check request
            if ($mybb->request_method == 'post') {
                // valid code request...
                if (!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key']) {
                    $mybb->request_method = 'get';
                    flash_message($lang->advadsman_error_unknown, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
                }

                // reques cannot be anymore stopped
                ignore_user_abort(TRUE);

                $update = array();

                // something to be updated?
                if ( ! $mybb->input['no']) {
                    if ( ! empty($ad['urlc'])) {
                        $update['url'] = $ad['urlc'];
                    }
                    if ( ! empty($ad['imagec'])) {
                        // old image will be removed
                        @unlink(MYBB_ROOT . $ad['image']);
                        // do the transfer
                        $update['image'] = $ad['imagec'];
                        // update image width
                        $img = @getimagesize(MYBB_ROOT . $ad['imagec']);
                        $update['width'] = $img[0];
                    }
                }
                $update['urlc'] = '';
                $update['imagec'] = '';
                $update['disabled'] = 0;

                // final update to database
                $db->update_query('advadsman_ads', $update, "aid = '$aid'");

                // cache update
                advadsman_cache_massupdate(2);
                
                // add log into system
                advadsman_db_later(array(
                    'table' => 'advadsman_logs', 
                    'action' => 'insert',
                    'data' => array(
                        'action' => $lang->advadsman_ads_approve_loga, 
                        'data' => $lang->sprintf($lang->advadsman_ads_approve_logd, $aid), 
                        'date' => TIME_NOW, 
                        'user' => $mybb->user['username']
                    )
                ));

                flash_message($lang->advadsman_ads_approve_success, 'success');
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            } else {
                $page->output_header($lang->advadsman_mod_title);

                $aid = (int) $mybb->input['aid'];
                $form = new Form("index.php?module=tools-advadsman&amp;action=ad_approve&amp;aid={$aid}&amp;my_post_key={$mybb->post_code}", 'post');
                $additional = '<ul type="square">';
                if (!empty($ad['urlc'])) {
                    $additional .= $lang->sprintf($lang->advadsman_ads_approve_text_url, $ad['url'], $ad['urlc']);
                }
                if (!empty($ad['imagec'])) {
                    $path = $mybb->settings['bburl'] . '/';
                    $additional .= $lang->sprintf($lang->advadsman_ads_approve_text_image, $path . $ad['image'], $path . $ad['imagec']);
                }
                $additional .= '</ul>';
                $lang->advadsman_ads_approve_text = $lang->sprintf($lang->advadsman_ads_approve_text, $additional);
                echo "<div class=\"confirm_action\">\n";
                echo "<p>{$lang->advadsman_ads_approve_text}</p>\n";
                echo "<br />\n";
                echo "<p class=\"buttons\">\n";
                echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
                echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
                echo "</p>\n";
                echo "</div>\n";
                $form->end();
            }
        } elseif ($mybb->input['action'] == 'ad_delete') 
        {
            if ($mybb->input['no']) {
                admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
            }
			
            if ($mybb->request_method == 'post') {
                if (!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key']) {
                    $mybb->request_method = 'get';
                    flash_message($lang->advadsman_error_unknown, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
                }

                $aid = (int) $mybb->input['aid'];

                $query = $db->simple_select('advadsman_ads', '*', "aid = '$aid'");
                if ($db->num_rows($query) == 0) {
                    flash_message($lang->advadsman_error_invalid, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
                } else {
                    $row = $db->fetch_array($query);

                    @unlink(MYBB_ROOT . $row['image']);
                    $copy = $row['imagec'];
                    if ( ! empty($copy)) {
                        @unlink(MYBB_ROOT . $copy);
                    }

                    $zone = $row['zone'];

                    $case = (int) $mybb->settings['advadsman_setting_deletead'];
                    $back = 0;
                    if ($row['uid'] > 0)
                    {
                        switch ($case) {
                            case 1 :
                                $points = $db->fetch_field($db->simple_select('advadsman_zones', 'points', "zid = '$zone'"), 'points');
                                $months = number_format(($row['expire'] - TIME_NOW) / 2592000, 2);
                                if ($months > 0) {
                                    $back = $points * $months;
                                }
                                break;
                            case 2 :
                                $points = $db->fetch_field($db->simple_select('advadsman_zones', 'points', "zid = '$zone'"), 'points');
                                $months = floor(($row['expire'] - $row['date']) / 2592000);
                                if ($months > 0) {
                                    $back = $points * $months;
                                }
                                break;
                        }
                    }

                    if ($back > 0) {
                        if (function_exists('newpoints_addpoints')) {
                            newpoints_addpoints((int)$row['uid'], number_format($back, intval($mybb->settings['newpoints_main_decimal'])));
                        } else {
							$db->update_query(
								'users', 
								array('newpoints' => 'newpoints + ' . number_format($back, intval($mybb->settings['newpoints_main_decimal']))), 
								"uid = '" . (int) $row['uid'] . "'",
								TRUE
							); 
                        }
                        
                        advadsman_cache_update('stats', 'total_spend', -number_format($back, intval($mybb->settings['newpoints_main_decimal'])), TRUE);
                    }

                    $db->delete_query('advadsman_ads', "aid = '$aid'");

                    $db->update_query('advadsman_zones', array('is_ad' => 0), "zid = '$zone'");

                    advadsman_cache_delete('ads', $zone);
                    advadsman_cache_update('zones', $zone, array('is_ad' => 0));
                    
                    if ($back > 0) {
                        $uid = $row['uid'];
                    } else {
                        $uid = '-';
                    }

                    advadsman_db_later(array(
                        'table' => 'advadsman_logs', 
                        'action' => 'insert',
                        'data' => array(
                            'action' => $lang->advadsman_ads_delete_loga, 
                            'data' => $lang->sprintf($lang->advadsman_ads_delete_logd, $uid, $back), 
                            'date' => TIME_NOW, 
                            'user' => $mybb->user['username']
                        )
                    ));

                    flash_message($lang->advadsman_ads_delete_success, 'success');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=ads');
                }
            } else {
                $page->output_header($lang->advadsman_mod_title);

                $aid = (int) $mybb->input['aid'];
                $form = new Form("index.php?module=tools-advadsman&amp;action=ad_delete&amp;aid={$aid}&amp;my_post_key={$mybb->post_code}", 'post');
                echo "<div class=\"confirm_action\">\n";
                echo "<p>{$lang->advadsman_mod_confirm_desc}</p>\n";
                echo "<br />\n";
                echo "<p class=\"buttons\">\n";
                echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
                echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
                echo "</p>\n";
                echo "</div>\n";
                $form->end();
            }
		} elseif ($mybb->input['action'] == 'upgrades') 
        {
            $page->output_header($lang->advadsman_mod_title);
            $page->output_nav_tabs($sub_tabs, 'upgrades');
			
			// get all possible upgrades
			$upgrades = advadsman_get_upgrades();
            
			// table with upgrades
			$table = new Table;
			// build header
			$table->construct_header($lang->advinvsys_upgrades_name, array('width' => '70%'));
			$table->construct_header($lang->advinvsys_upgrades_controls, array('width' => '30%', 'class' => 'align_center'));
			if ( ! empty($upgrades)) 
			{
				foreach ($upgrades as $upgrade) 
				{
					$codename = str_replace(".php", "", $upgrade['file']);
                
					$from = array();
					$to = array();
					$id = 2;
					while ($id >= 0) {
						$from[] = intval($upgrade['from'] / pow(10, $id)) % 10; 
						$to[] = intval($upgrade['to'] / pow(10, $id)) % 10; 
						$id--;
					}
                
					// add a new row
					$table->construct_cell('<a href="http://extramybb.com" target="_blank"><b>' . 
                        $lang->advadsman_mod_title . '</b></a> (v' . 
                        implode('.', $from) . ' => v' . implode('.', $to) . ')<br /><i><small>' . 
                        $lang->created_by . ' ExtraMyBB Team</small></i>');
					$table->construct_cell("<a href=\"index.php?module=tools-advadsman&amp;action=upgrade_run&amp;upgrade_file=" . $codename . "&amp;my_post_key={$mybb->post_code}\" target=\"_self\">{$lang->advadsman_upgrades_run}</a>", array('class' => 'align_center'));
					$table->construct_row();
				}
			} else {
				// no upgrades?
				$table->construct_cell($lang->advadsman_no_upgrades, 
                    array('colspan' => 2, 'class' => 'align_center'));
				$table->construct_row();
			}
        
			// display table
			$table->output($lang->advadsman_upgrades_title);
		} elseif ($mybb->input['action'] == 'upgrade_run')
		{
			if ($mybb->input['no']) {
				admin_redirect('index.php?module=tools-advadsman&amp;action=upgrades');
			}
			if ($mybb->request_method == 'post') 
			{
				if ( ! isset($mybb->input['my_post_key']) || 
						$mybb->post_code != $mybb->input['my_post_key']) {
					$mybb->request_method = 'get';
					flash_message($lang->advadsman_error_invalid, 'error');
					admin_redirect('index.php?module=tools-advadsman&amp;action=upgrades');
				}
            
				// select file
				$upgrade = htmlspecialchars($mybb->input['upgrade_file']);
            
				// include file
				require_once ADVADSMAN_PLUGIN_PATH . "upgrades/{$upgrade}.php";
            
				// call function
				$runfunction = $upgrade . '_run';
				if ( ! function_exists($runfunction)) {
					continue;
				}
            
				$result = $runfunction();
            
				// success?
				if ($result) {
					flash_message($lang->advadsman_upgrades_success, 'success');
				} else {
					flash_message($lang->advadsman_upgrades_error, 'error');
				}
            
				// in ambele cazuri se face aceeasi redirectionare...
				admin_redirect('index.php?module=tools-advadsman&amp;action=upgrades');
			} else {
				// confirmation is required
				$page->add_breadcrumb_item($lang->advadsman_upgrades_title, 
                    'index.php?module=tools-advadsman&amp;action=upgrades');
				$page->output_header($lang->advadsman_upgrades_title);
            
				// do cleanup
				$mybb->input['upgrade_file'] = htmlspecialchars($mybb->input['upgrade_file']);
            
				// create a new form
				$form = new Form("index.php?module=tools-advadsman&amp;action=upgrade_run&amp;upgrade_file=" . $mybb->input['upgrade_file'] . "&amp;my_post_key={$mybb->post_code}", 'post');
				echo "<div class=\"confirm_action\">\n";
				echo "<p>{$lang->advadsman_upgrades_confirm}</p>\n";
				echo "<br />\n";
				echo "<p class=\"buttons\">\n";
				echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
				echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
				echo "</p>\n";
				echo "</div>\n";
				$form->end();
			}
        } elseif ($mybb->input['action'] == 'logs') 
        {
            $page->output_header($lang->advadsman_mod_title);
            $page->output_nav_tabs($sub_tabs, 'logs');
            
            $per_page = 10;
            if($mybb->input['page'] && $mybb->input['page'] > 1) {
                $mybb->input['page'] = (int)$mybb->input['page'];
				$start = ($mybb->input['page'] * $per_page) - $per_page;
            } else {
				$mybb->input['page'] = 1;
				$start = 0;
            }
	
            $query = $db->simple_select('advadsman_logs', 'COUNT(lid) AS total_logs');
            $total_rows = $db->fetch_field($query, 'total_logs');
            if ($total_rows > $per_page) {
				echo draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=tools-advadsman&amp;action=logs&amp;page={page}");
            }

            $table = new Table;
            $table->construct_header($lang->advadsman_logs_action, array('width' => '20%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_logs_data, array('width' => '45%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_logs_date, array('width' => '20%', 'class' => 'align_center'));
            $table->construct_header($lang->advadsman_logs_user, array('width' => '15%', 'class' => 'align_center'));

            $query = $db->simple_select(
				'advadsman_logs', '*', '', 
				array('order_by' => 'date', 'order_dir' => 'DESC', 'limit' => "{$start}, {$per_page}")
			);

            while ($row = $db->fetch_array($query)) {
                $table->construct_cell(htmlspecialchars_uni($row['action']), array('class' => 'align_center'));
                $table->construct_cell(htmlspecialchars_uni($row['data']), array('class' => 'align_center'));
                $table->construct_cell(my_date($mybb->settings['dateformat'], $row['date'], '', false) . ', ' . my_date($mybb->settings['timeformat'], $row['date']), array('class' => 'align_center'));
                $table->construct_cell(htmlspecialchars_uni($row['user']), array('class' => 'align_center'));
                $table->construct_row();
            }

            if ($table->num_rows() == 0) {
                $table->construct_cell($lang->advadsman_logs_noresults, array('class' => 'align_center', 'colspan' => 4));
                $table->construct_row();
            }

            $table->output($lang->advadsman_logs_title);

            echo '<br />';
	
            $form = new Form('index.php?module=tools-advadsman&amp;action=logs_prune', 'post', 'advadsman');
	
            echo $form->generate_hidden_field('my_post_key', $mybb->post_code);
		
            $form_container = new FormContainer($lang->advadsman_logs_prune);
            $form_container->output_row($lang->advadsman_logs_older, $lang->advadsman_logs_older_desc, $form->generate_text_box('days', 7, array('id' => 'days')), 'days');
            $form_container->end();

            $buttons = array();;
            $buttons[] = $form->generate_submit_button($lang->advadsman_buttons_submit);
            $buttons[] = $form->generate_reset_button($lang->advadsman_buttons_reset);
            $form->output_submit_wrapper($buttons);
            $form->end();
        } elseif ($mybb->input['action'] == 'logs_prune') 
        {
            if($mybb->input['no']) {
                admin_redirect('index.php?module=tools-advadsman&amp;action=logs');
            }
            
            if($mybb->request_method == "post")
            {
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key']) {
                    $mybb->request_method = "get";
                    flash_message($lang->advadsman_error_unknown, 'error');
                    admin_redirect('index.php?module=tools-advadsman&amp;action=logs');
				}
                
				$db->delete_query('advadsman_logs', 'date < ' . (TIME_NOW - ((int)$mybb->input['days']) * 3600 * 24));
                
				flash_message($lang->advadsman_logs_pruned, 'success');
				admin_redirect('index.php?module=tools-advadsman&amp;action=logs');
            } else {
                $page->output_header($lang->advadsman_mod_title);

                $days = (int)$mybb->input['days'];
                $form = new Form("index.php?module=tools-advadsman&amp;action=logs_prune&amp;days={$days}&amp;my_post_key={$mybb->post_code}", 'post');
                echo "<div class=\"confirm_action\">\n";
                echo "<p>{$lang->advadsman_mod_confirm_desc}</p>\n";
                echo "<br />\n";
                echo "<p class=\"buttons\">\n";
                echo $form->generate_submit_button($lang->advadsman_buttons_yes, array('class' => 'button_yes'));
                echo $form->generate_submit_button($lang->advadsman_buttons_no, array("name" => "no", 'class' => 'button_no'));
                echo "</p>\n";
                echo "</div>\n";
                $form->end();
            }
        }

        $page->output_footer();
    }
}
?>