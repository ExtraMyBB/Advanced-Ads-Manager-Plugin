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