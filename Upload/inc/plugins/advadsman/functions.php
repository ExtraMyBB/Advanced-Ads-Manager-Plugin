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
if ( ! defined('IN_MYBB') || ! defined('ADVADSMAN_VERSION')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB and ADVADSMAN_VERSION are defined.');
}

/* UPLOAD SYSTEM */

/*
 * Using this function you can upload one file to your server. It returns
 * the width and the path of image.
 */
function advadsman_upload_file($file, $maxdimension)
{
    global $mybb, $lang;
    
    $errors = array();
    if ( ! defined('IN_ADMINCP'))
    {
        $lang->load('advadsman');
        
        $errors[1] = $lang->sprintf($lang->advadsman_upload_error1, 
            $mybb->settings['advadsman_setting_validext'], $extension);
        $errors[2] = $lang->advadsman_upload_error2;
        $errors[3] = $lang->advadsman_upload_error3;
        $errors[4] = $lang->sprintf($lang->advadsman_upload_error4, $maxdimension);
    } else {
        for($i = 1; $i <= 4; $i++) {
            $errors[$i] = $i;
        }
    }
    
    require_once MYBB_ROOT . 'inc/functions_upload.php';

    // current user
    $uid = $mybb->user['uid'];
    
    $result = array();

    // check for a valid extension?!
    $extension = get_extension(my_strtolower($file['name']));
    $valid = explode(',', $mybb->settings['advadsman_setting_validext']);
    if ( ! in_array($extension, $valid)) {
        $result['error'] = $errors[1];
        return $result;
    }
                
    // choose a name for uploaded file
    $filename = 'aam_' . random_str(7) . '_' . $uid . '.' . $extension;

    // try to upload file on server
    $file_up = upload_file($file, ADVADSMAN_UPLOAD_PATH, $filename);
    if ($file_up['error']) {
        @unlink(ADVADSMAN_UPLOAD_PATH . $filename);
        $result['error'] = $errors[2];
        return $result;
    }
   
    // is the file uploaded an image?
    $img = @getimagesize(ADVADSMAN_UPLOAD_PATH . $filename);
    if ( ! is_array($img)) {
        @unlink(ADVADSMAN_UPLOAD_PATH . $filename);
        $result['error'] = $errors[3];
        return $result;
    }

    // check for valid image dimensions?
    list($width, $height) = @explode('x', $maxdimension);
    if (($width && $img[0] > $width) || ($height && $img[1] > $height)) {
        @unlink(ADVADSMAN_UPLOAD_PATH . $filename);
        $result['error'] = $errors[4];
        return $result;
    }
    
    $result = array(
        'path' => 'uploads/advadsman/' . $filename,
        'width' => $width
    );
    
    return $result;
}

/* DB SYSTEM - LATER QUERY */

/*
 * Delay doing some queries are necessary sometimes for increasing application
 * performance.
 */
function advadsman_db_later($data)
{
    global $advadsman_queries;
    
    $advadsman_queries[] = $data;
    
    add_shutdown('advadsman_db_queries');
}

/*
 * Who must execute delayed queries...
 */
function advadsman_db_queries()
{
    global $db, $advadsman_queries;
    
    if (is_array($advadsman_queries)) {
        foreach($advadsman_queries as $key => $query) {
            if (is_array($query) && isset($query['action'])) {
                switch($query['action']) {
                    case 'insert' :
                        $db->insert_query($query['table'], $query['data']);
                    break;
                    case 'update' :
                        $db->update_query($query['table'], $query['data'], $query['where']);
                    break;
                    case 'delete' :
                        $db->delete_query($query['table'], $query['where']);
                    break;
                    default :
                        $db->write_query($query['data']);     
                }    
            }
        }
    }
    
    unset($advadsman_queries);
}

/* CACHING SYSTEM */

/*
 * Read cache entry and parse it.
 */
function advadsman_cache_read($entry, $part = 'part') 
{
    global $mybb;

    $cache = $mybb->cache->read('advadsman_cache');

    if (!$cache || !is_array($cache) || !isset($cache[$entry])) {
        return FALSE;
    } else {
        if ($entry == 'zones' && $part != 'all') {
            $result = array();
            foreach ($cache[$entry] as $key => $value) {
                if (is_array($value) && isset($value['is_ad']) && $value['is_ad'] == 0) {
                    $result[$key] = $value;
                }
            }
            return $result;
        } else {
            return $cache[$entry];
        }
    }
}

/*
 * Update number of clicks and views for an advertisement.
 */
function advadsman_cache_update($entry, $key, $value, $relative = false) 
{
    global $mybb;

    $cache = $mybb->cache->read('advadsman_cache');

    if (!$cache || !is_array($cache) || !isset($cache[$entry])) {
        if (!is_array($cache)) {
            $cache = array();
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $cache[$entry][$key][$k] = $v;
            }
        } else {
            $cache[$entry][$key] = $value;
        }
    } else {
        if (isset($cache[$entry][$key])) {
            if ($relative) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $cache[$entry][$key][$k] += $v;
                    }
                } else {
                    $cache[$entry][$key] += $value;
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $cache[$entry][$key][$k] = $v;
                    }
                } else {
                    $cache[$entry][$key] = $value;
                }
            }
        } else {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $cache[$entry][$key][$k] = $v;
                }
            } else {
                $cache[$entry][$key] = $value;
            }
        }
    }

    $mybb->cache->update('advadsman_cache', $cache);
}

/*
 * Update intern cache for zones and banners.
 */
function advadsman_cache_massupdate($update = 0) 
{
    global $mybb, $db;

    $cache = $mybb->cache->read('advadsman_cache');

    if ($update < 2) {
        $zones = array();
        $query = $db->simple_select('advadsman_zones', 'zid,name,maxdimension,points,posts,is_ad');
        while ($row = $db->fetch_array($query)) {
            $zone = $row['zid'];
            unset($row['zid']);
            $zones[$zone] = $row;
        }
        $cache['zones'] = $zones;
    }

    if ($update == 0 || $update == 2) {
        $ads = array();
        $query = $db->simple_select(
			'advadsman_ads', 
			'aid,expire,groups,url,image,zone,width', 
			"(disabled = 0 OR (disabled = 1 AND (urlc <> '' OR imagec <> ''))) AND expire > " . TIME_NOW
		);
        while ($row = $db->fetch_array($query)) {
            $zone = $row['zone'];
            unset($row['zone']);
            $ads[$zone] = $row;
        }
        $cache['ads'] = $ads;
    }

    $mybb->cache->update('advadsman_cache', $cache);
}

/*
 * Delete an entry key from cache system.
 */
function advadsman_cache_delete($entry, $key)
{
    global $mybb;
    
    $cache = $mybb->cache->read('advadsman_cache');
    
    if ($cache && isset($cache[$entry]) && isset($cache[$entry][$key])) {
        unset($cache[$entry][$key]);
        
        $mybb->cache->update('advadsman_cache', $cache);
    }
}

/*
 * Select an advertisement space for a zone.
 */
function advadsman_select_ad($zone) 
{
    $ads = advadsman_cache_read('ads');

    // search an ad zone
    if ($ads && isset($ads[$zone]) && is_array($ads[$zone])) {
        $ad = $ads[$zone];

        if ($ad['expire'] < TIME_NOW) {
            advadsman_db_later(array(
                'table' => 'advadsman_zones', 
                'action' => 'update',
                'data' => array('is_ad' => 0),
                'where' => "zid = '{$zone}'"
            ));

            advadsman_db_later(array(
                'table' => 'advadsman_ads', 
                'action' => 'delete',
                'where' => "zone = '{$zone}'"
            ));
 
            advadsman_cache_delete('ads', $zone);
            advadsman_cache_update('zones', $zone, array('is_ad' => 0));

            return FALSE;
        } else {
            return $ad;
        }
    } else {
        return FALSE;
    }
}

/* GOOGLE ANALYTICS */

/*
 * Google Analytics stats will be updated periodically using a task. (each 24 hours)
 */
function advadsman_gapi($gaad, $datacache) 
{
    global $mybb;

    require_once(ADVADSMAN_PLUGIN_PATH . 'classes/gapi.class.php');

    $ga = new GAPI();

    $ok = TRUE;
    if (!$ga->login($gaad[0], $gaad[1])) {
        $ok = FALSE;
    }

    if ($ok) {
        $ga->load_accounts();

        $id = $ga->getAccountsTableIdFromName($gaad[2]);

        if ($id === FALSE) {
            $ok = FALSE;
        }
    }

    if ($ok) {
        $data = $ga->data(
            $id, 
            'ga:date', 
            'ga:visitors,ga:visits,ga:pageviews,ga:timeOnSite', 
            '-ga:date', 
            date('Y-m-d', strtotime($mybb->settings['advadsman_setting_gaadtime'] . ' day ago')), 
            date('Y-m-d'), 
            $mybb->settings['advadsman_setting_gaadtime']
        );
        $datacache['stats']['gapi_data'] = array();
        foreach ($data as $date => $value) {
            $datacache['stats']['gapi_data'][$date] = $value;
        }
    }

    $datacache['stats']['gapi_last'] = date('Y-m-d');

    $mybb->cache->update('advadsman_cache', $datacache);

    return $datacache['stats']['gapi_data'];
}

/*
 * Read Google Analytics cache entry and process data.
 */
function advadsman_cache_analytics($gaad) 
{
    global $mybb;
 
    $datacache = $mybb->cache->read('advadsman_cache');

    if ($datacache && isset($datacache['stats']) && is_array($datacache['stats'])) {
        $stats = $datacache['stats'];

        $day = '1970-1-1';
        if (isset($stats['gapi_last'])) {
            $day = $stats['gapi_last'];
        }

        $limit = strtotime('+1 day', strtotime($day));

        if (TIME_NOW > $limit || ! isset($stats['gapi_data'])) {
            return advadsman_gapi($gaad, $datacache);
        } else {
            return $datacache['stats']['gapi_data'];
        }
    } else {
        return advadsman_gapi($gaad, $datacache);
    }
}

/* OTHER FUNCTIONS */

/*
 * Check if "NewPoints" plugin it is installed.
 */
function advadsman_newpoints_installed()
{
    $file = MYBB_ROOT . 'inc/plugins/newpoints.php';
    if ( ! file_exists($file)) {
        return FALSE;
    } else {
		if ( ! function_exists('newpoints_is_installed')) {
			require_once $file;
		}
		
		return call_user_func('newpoints_is_installed');
	}
}

/*
 * Format number of seconds passed after 1 jan. 1970 into days, hours, minutes and seconds.
 */
function advadsman_time_format($seconds, $day = 'days')
{
    if ($seconds >= 86400) {
        $days = (int)($seconds / 86400) . " $day ";
        $seconds -= $days * 86400;
    } else {
        $days = '';
    }
    $hours = (int)($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = (int)($seconds / 60);
    $seconds -= $minutes * 60;
    return sprintf('%s%02d:%02d:%02d', $days, $hours, $minutes, $seconds);
}

/*
 * Send private message to an user.
 */
function advadsman_send_pm($pm, $fromid = 0) 
{
    global $lang, $mybb;

    if ($mybb->settings['enablepms'] == 0 || !is_array($pm)) {
        return false;
    }
    if (!$pm['subject'] || !$pm['message'] || !$pm['touid'] || !$pm['receivepms']) {
        return false;
    }
    $lang->load('messages');

    require_once MYBB_ROOT . 'inc/datahandlers/pm.php';
    $pmhandler = new PMDataHandler();

    $subject = $pm['subject'];
    $message = $pm['message'];
    $toid = $pm['touid'];

    if (is_array($toid))
        $recipients_to = $toid;
    else
        $recipients_to = array($toid);
    $recipients_bcc = array();

    if ((int) $fromid == 0)
        $fromid = (int) $mybb->user['uid'];
    elseif ((int) $fromid < 0)
        $fromid = 0;

    $pm = array(
        "subject" => $subject,
        "message" => $message,
        "icon" => -1,
        "fromid" => 0,
        "toid" => $recipients_to,
        "bccid" => $recipients_bcc,
        "do" => '',
        "pmid" => ''
    );

    $pm['options'] = array(
        "signature" => 0,
        "disablesmilies" => 0,
        "savecopy" => 0,
        "readreceipt" => 0
    );
    $pm['saveasdraft'] = 0;
    $pmhandler->admin_override = 1;
    $pmhandler->set_data($pm);
    if ($pmhandler->validate_pm()) {
        $pmhandler->insert_pm();
    } else {
        return false;
    }

    return true;
}
?>