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

/*
 * Task used for updating number of clicks / views for an advertisement
 * space or for sending private messages.
 */
function task_advadsman($task) 
{
    global $mybb, $db, $lang;

    @set_time_limit(0);

    // update cache to database
    $cache = $mybb->cache->read('advadsman_cache');
    if ($cache || is_array($cache)) {
        // update number of clicks
        advadsman_task_update($cache, 'views');
        // update number of views
        advadsman_task_update($cache, 'clicks');
    }
    $mybb->cache->update('advadsman_cache', $cache);
    
    $lang->load('advadsman');
    
	// we are going to send some notifications
    $now = TIME_NOW;
    $reftime = strtotime("+{$mybb->settings['advadsman_setting_extperiod']} day", $now);
    $query = $db->simple_select(
        'advadsman_ads', 'uid,expire', 
        "expire <= {$reftime} AND expire > {$now}"
    );
    $i = 0;
    while ($row = $db->fetch_array($query)) {
        advadsman_send_pm(array(
            'subject' => $lang->advadsman_task_pmsubject,
            'message' => $lang->sprintf(
                $lang->advadsman_task_pmmessage,
                $mybb->settings['advadsman_setting_extperiod'], 
                my_date($mybb->settings['dateformat'], $row['expire']) . ', ' . my_date($mybb->settings['timeformat'], $row['expire'])
            ),
            'receivepms' => -1,
            'touid' => (int)$row['uid']
        ));
        $i++;
    }
    
    // send at least one notification
    if ($i > 0) {
        // add a new log entry
        advadsman_db_later(array(
            'table' => 'advadsman_logs', 
            'action' => 'insert',
            'data' => array(
                'action' => $lang->advadsman_task_note_loga, 
                'data' => $lang->sprintf($lang->advadsman_task_note_logd, $i), 
                'date' => TIME_NOW, 
                'user' => 'System'
            )
        ));
    }

    // add task log
    add_task_log($task, $lang->advadsman_task_ran);
}

function advadsman_task_update(&$cache, $entry) 
{
    global $db;

    if (isset($cache[$entry])) {
        $values = $cache[$entry];
        
        $array = array();
        foreach ($values as $aid => $inc) {
            $array[] = "SET {$entry} = {$entry} + {$inc} WHERE aid = '{$aid}'";
        }
        
        $db->write_query('
            UPDATE ' . TABLE_PREFIX . 'advadsman_ads 
            ' . @implode(',', $array)
        );
        
        unset($cache[$entry]);
    }
}
?>