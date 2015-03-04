// Shoutbox by hellcore, 2012.
var ls = ls || {};
var updatetime = $.cookie('shoutbox_update_interval');
var lastshouttext = $.cookie('lastshout_text'); // защита от случайного репоста
var allowupdate = true;
var shoutboxsb = $('#scrollbar1');

if (updatetime == 0) {
	var autoupdate = false;
} else {
	var autoupdate = true;
}

ls.shoutbox = (function ($) {

	this.Timer = function() {
		if (updatetime >= 10000) {
			setTimeout(function() {
				if (autoupdate) {
					if (allowupdate) {
						ls.shoutbox.DoUpdate();
					}
					ls.shoutbox.Timer();
				}
			}, updatetime);
		} else {
			updatetime = 10000;
		}
	}

	this.Update = function() {
		//$('#shouts').html('');
		var url = aRouter['shoutbox']+'update/';
		var vkey = $('#sbcp').html();
		var params = {vkey: vkey};
		shoutboxsb = $('#scrollbar1');
		ls.shoutbox.UIShout(true);
		ls.ajax(url, params, function(result) {
			if (result.aHtml) {
				var rhtml_top = '<table class="table sb_table"><tbody id="shoutsbody">';
				var rhtml_tottom = "</tbody></table>";
				$('#shouts').html(rhtml_top+result.aHtml+rhtml_tottom);
				shoutboxsb.tinyscrollbar_update('relative');
				$("#shoutsbody").show('highlight');
				$('#shoutlastid').val(result.iLastId);
			}
			ls.shoutbox.UIShout(false);
		}.bind(this));
		return true;
	};
	this.CheckErrors = function(bError,sText,sTitle) {
		if (bError) {
			var thtml = '<h4>'+sTitle+'</h4><p>'+sText+'</p>';
			$("#shoutbox_error_area").html(thtml);
		}
	}
	this.DoUpdate = function() {
		var url = aRouter['shoutbox']+'update/';
		var iLastId = $('#shoutlastid').val();
		var vkey = $('#sbcp').html();
		var params = {iLastId: iLastId, vkey: vkey};
		shoutboxsb = $('#scrollbar1');
		ls.shoutbox.UIShout(true);
		ls.ajax(url, params, function(result) {
			if (result.aDoUpdateResult) {
				var thtml = result.aHtml;
				$("#shoutsbody").prepend(thtml);
				$("#shoutsbody tr").first().show('highlight');
				$('#shoutlastid').val(result.iLastId);
				shoutboxsb.tinyscrollbar_update('relative');
			}
			ls.shoutbox.UIShout(false);
		}.bind(this));
		return true;
	};

	this.Moderate = function(iId,iType) {
		//1-soft,2-hard,3-restore
		var url = aRouter['shoutbox']+'moderate/';
		var params = {iId: iId, iType: iType};
		ls.ajax(url, params, function(result) {
			if (result.bStateError) {
				ls.shoutbox.CheckErrors(result.bStateError,result.sMsg,result.sMsgTitle);
			} else {
				if (result.bMod == true) {
					ls.shoutbox.Update();
				}
			}
		}.bind(this));
		return true;
	};

	this.Add = function(sText) {
		if (lastshouttext != sText) {
			ls.shoutbox.UIShout(true);

			var url = aRouter['shoutbox']+'add/';
			var params = {sText: sText};
			var lasttext = sText;
			allowupdate = false;
			
			ls.ajax(url, params, function(result) {

				if (result.bStateError) {
					ls.shoutbox.CheckErrors(result.bStateError,result.sMsg,result.sMsgTitle);
				} else {
					$('#shouttext').val('');
					$('#shoutbox_error_area').html('');
					ls.shoutbox.SetCookie('lastshout_text',lasttext,10);
					lastshouttext = $.cookie('lastshout_text');
				}

				ls.shoutbox.DoUpdate();
				allowupdate = true;

			}.bind(this));

			return false;
		} else {
			$('#shouttext').val('');
		}
	};


	this.QuoteAuthor = function(author) {
		var inputfield = $('#shouttext');
		inputfield.val(inputfield.val()+' #'+author+' ');
		return author;
	}

	this.UIShout = function(bUnlock) {
		$('.js-shout-submit').attr('disabled',bUnlock);
		$('#shoutswitcher').attr('disabled',bUnlock);
		$('#shoutupdate').attr('disabled',bUnlock);
	};

	this.SetCookie = function(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toGMTString();
		}
		document.cookie = name + "=" + value + expires;
	}

	this.SetInterval = function(seconds) {
		ls.shoutbox.SetCookie('shoutbox_update_interval',seconds*1000,30);
	}
return this;
}).call(ls.shoutbox || {},jQuery);

$(document).ready(function() {
	
	$('#sbcp').html($('#security_sb_key').val());
  	ls.shoutbox.Update();
 	ls.shoutbox.Timer();
 	shoutboxsb.tinyscrollbar();
});

