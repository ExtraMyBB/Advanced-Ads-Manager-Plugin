/*
 * -PLUGIN-----------------------------------------
 * Name		: Advanced Ads Manager
 * Version 	: 1.1.0
 * -TEAM-------------------------------------------
 * Developers	: Baltzatu, Mihu
 * -LICENSE----------------------------------------
 * Copyright (C) 2013 ExtraMyBB.com. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var AAM = {
	do_show: function() {
		$$('div.advadsman-box').each(function (e) {
			// set div width
			var w = e.getWidth();
			var message = e.down('div.advadsman-copy');
			// display copyright message?
			var bottom = 5;
			if (w < 180) {
				message.hide();
			} else {
				bottom = bottom + message.getHeight();
			}
			// set a margin padding
    		e.setStyle({marginBottom: bottom.toString() + "px"});
		});
	},
	do_click: function(aid) {
		if(use_xmlhttprequest != 1) {
			return true;
		}
		// release an ajax request
		new Ajax.Request('xmlhttp.php?action=do_click&my_post_key=' + my_post_key, {
			method: 'post',
			postBody: 'aid=' + encodeURIComponent(parseInt(aid))
		});
		return false;
	}
};

Event.observe(document, 'dom:loaded', function() {
	AAM.do_show();
});