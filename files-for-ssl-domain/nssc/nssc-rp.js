/* Base64 encode / decode http://www.webtoolkit.info/ */

var ssldomain = 'https://domain-hosting-ssl-files.com';
var mainurl = 'http://url-to-wordpress-install.com';

var Base64 = {
_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
encode : function (input) {
    var output = "";
    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
    var i = 0;

    input = Base64._utf8_encode(input);

    while (i < input.length) {

        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
            enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
            enc4 = 64;
        }

        output = output +
        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

    }

    return output;
},
_utf8_encode : function (string) {
    string = string.replace(/\r\n/g,"\n");
    var utftext = "";

    for (var n = 0; n < string.length; n++) {

        var c = string.charCodeAt(n);

        if (c < 128) {
            utftext += String.fromCharCode(c);
        }
        else if((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        }
        else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }

    }

    return utftext;
}

}

if (typeof(gmloadScript) !== "function") {
function gmloadScript(sScriptSrc) {
    var oHead = document.getElementsByTagName('head')[0];
    var oScript = document.createElement('script');
    oScript.type = 'text/javascript';
    oScript.src = sScriptSrc;
    oHead.appendChild(oScript);
}
}
// Checker for when jQuery is ready to go
if (typeof(onjQueryAvailable) !== "function") {
    function onjQueryAvailable(oCallback) {
        if (typeof(jQuery) === 'function') {
            oCallback();
        } else {
            setTimeout(function () {
            onjQueryAvailable(oCallback);
            }), 50
            }
        }
}


function nsscTimeConverter(UNIX_timestamp){
 var a = new Date(UNIX_timestamp*1000);
 var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var mins = a.getMinutes();
     var sec = a.getSeconds();
     var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + mins + ':' + sec ;
     return time;
 }


var riddocount = 0;
var rptimeout = 0;
var murktimer = false;
var murkfail = false;


function scrollWindowDown() {
		window.scrollTo(0,document.body.scrollHeight);	
	}
	
function scrollWindowUp() {
	window.scrollTo(0,0);	
}

function countRiddos() {
	var countem = jQuery('li.soundList__item').length;
	return countem;	
}

function murkReposts() {
	jQuery('li.soundList__item').each(function(){
		if(!jQuery(this).find('a.repostingUser').length){
			jQuery(this).remove();	
		}
	});
}

function startFreshMurk() {
	if (!jQuery('#murkconsole').hasClass('working')) {
			jQuery('#murkconsolee').addClass('working');
			var murkdays = new Date();
			murkdays.setDate(murkdays.getDate() - jQuery('#murkdays').val()-1);
			murktimer = window.setInterval(function(){dayBasedMurk(murkdays.getTime());},100);
		}
		else { return false; }
}

function startUpdateMurk(murktime) {
	if (!jQuery('#murkconsole').hasClass('working')) {
			jQuery('#murkconsolee').addClass('working');
			murkdays = murktime * 1000;
			murktimer = window.setInterval(function(){dayBasedMurk(murkdays);},100);
		}
		else { return false; }
}

function dayBasedMurk(murktime) {
	
	var bum = 'bum';
	alert(bum);
	
	murkReposts();
	rptimeout++;
	
	if (rptimeout == 1000) {
		jQuery('#murkconsole').removeClass('working');
		alert('Timeout');	
		clearInterval(murktimer);
	}
	else if (Date.parse(jQuery('li.soundList__item:last-child .sound__uploadTime .relativeTime').attr('datetime').substring(0, lct.length - 5).replace('T',' ').replace(/-/gi,'/'))+(60*60*1000) < murktime) {
		clearInterval(murktimer);
		scrollWindowUp();
		
		while ((lct < murktime) && jQuery('li.soundList__item').length) {
			jQuery('li.soundList__item:last-child').remove();
			if (jQuery('li.soundList__item').length) {
				var lct = jQuery('li.soundList__item:last-child .sound__uploadTime .relativeTime').attr('datetime');	
				lct = lct.substring(0, lct.length - 5);
				lct = lct.replace('T',' ');
				lct = Date.parse(lct)+(60*60*1000);
				//console.log('Murk Time:       '+murktime);
				//console.log('Last Child time: '+lct);
			} else {
				jQuery('#nssc-updatemurk').append('<p>No reposts to import.</p><p><button onclick="window.location = \''+mainurl+'/reposts\';">Back To NSSC</button> <button onclick="location.reload();">Reload</button></p>');
				murkfail = true;
			}
		}
		jQuery('#murkconsole').removeClass('working');
		if (!murkfail) grabTunes();
	} 
	else if (riddocount < countRiddos()) {
		rptimeout = 0;
		riddocount = countRiddos(); 
		scrollWindowDown();	
	} 
	
}

function grabTunes() {
	var tunestring = {};
	var i = 0;
	jQuery('li.soundList__item').each(function() {
			tunestring[i] = {};
			tunestring[i].url = jQuery(this).find('a.sound__coverArt').attr('href');
			tunestring[i].reposter = jQuery(this).find('a.repostingUser span').text();
			tunestring[i].reposterurl = jQuery(this).find('a.repostingUser').attr('href');
			tunestring[i].reposttime = jQuery(this).find('.sound__uploadTime .relativeTime').attr('datetime');
			if (jQuery(this).find('.activity > div').hasClass('playlist') ) {
				tunestring[i].type = 'p';
			} else {
				tunestring[i].type = 't';
			}
			i++;
	});
	var formScraper = '<form action="'+mainurl+'?reposter" method="post" id="sendreposts" style="display:none;"><input type="hidden" value="'+Base64.encode(JSON.stringify(tunestring))+'" name="tunestring" /><input type="hidden" name="nsscid" value="'+window.nsscid+'" /><input type="submit" value="Send Reposts" /></form>';
	jQuery('body').append(formScraper);
	document.getElementById("sendreposts").submit();
}


// Admin panel functions

function startCloudScrape() {
	if (!jQuery('#cloudscrape').hasClass('working')) {
		jQuery('#cloudscrape').addClass('working');
		murktimer = window.setInterval(function(){reapizzle();},5);
	}
	else { return false; }
}
	
	
	
	
function reapizzle() {
	murkReposts();
	rptimeout++;
	if (rptimeout == 1000) {
		jQuery('#cloudscrape').removeClass('working');
		alert('timeout');	
		clearInterval(murktimer);
	} 
	else if (riddocount > 50) {
			scrollWindowUp();
			jQuery('#cloudscrape').removeClass('working');
			clearInterval(murktimer);
			window.setTimeout(function(){murkReposts();jQuery('#cloudscrape .downloadlink').show();},5000);
		}
		else if (riddocount < countRiddos()) {
			rptimeout = 0;
			riddocount = countRiddos(); 
			scrollWindowDown();	
		}
}

function reapizzleAndGo() {
	murkReposts();
	rptimeout++;
	if (rptimeout == 1000) {
		jQuery('#cloudscrape').removeClass('working');
		alert('timeout');	
		clearInterval(murktimer);
	} 
	else if (riddocount > 50) {
			scrollWindowUp();
			jQuery('#cloudscrape').removeClass('working');
			clearInterval(murktimer);
			window.setTimeout(function(){murkReposts();grabTunes();},5000);
		}
		else if (riddocount < countRiddos()) {
			rptimeout = 0;
			riddocount = countRiddos(); 
			scrollWindowDown();	
		}
}

function instaMurk(){
if (!jQuery('#cloudscrape').hasClass('working')) {
		jQuery('#cloudscrape').addClass('working');
		murktimer = window.setInterval(function(){reapizzleAndGo();},5);
	}
	else { return false; }
}

function stopCloudScrape() {
	clearInterval(murktimer);	
	jQuery('#cloudscrape').removeClass('working');
	jQuery('#cloudscrape .downloadlink').hide();
}

function countCloudScrape() {
	alert(countRiddos());
}
	
function startmurk() {
if ((window.location == 'https://soundcloud.com/stream') || (window.location == 'https://soundcloud.com/stream/')) {
	 jQuery(document).ready(function() { 
        var adminconsole = '<div id="cloudscrape" style="position:fixed;top:10px;left:10px; padding: 10px 30px; background:#fff; border: 2px solid orange;z-index:999999;"><h2>Admin Console</h2><a href="javascript:void(0)" onclick="startCloudScrape();return false;" class="cloudscrapestart">Start Bumscrape</a> | <a href="javascript:void(0)" onclick="stopCloudScrape();return false;" class="cloudscrapestop">Stop Bumscrape</a> | <a href="javascript:void(0)" onclick="countCloudScrape();return false;" class="cloudscrapecount">Count</a> | <a href="javascript:void(0)" onclick="murkReposts();return false;" class="cloudscrapemurk">One-off Murk</a> | <a href="javascript:void(0)" onclick="grabTunes();return false;" class="cloudscrapegrab">Send To NSSC</a><br /><br /><a href="javascript:void(0)" onclick="instaMurk();return false;" class="cloudscrapeinstamurk">InstaMURK&reg;</a></div>'; 

		if (!jQuery('#murkconsole').length) {
			
			var murkconsole = '<div id="murkconsole" style="position:fixed;top:10px;right:10px; padding: 10px; border: 2px solid orange;z-index:999999;text-align: left;background: #fff;min-width: 285px;">';
			murkconsole += '	<div style="background:url('+ssldomain+'/nssc/nssc-logo-small.png) top right no-repeat #fff;">';
			murkconsole += '		<h2 style="margin-bottom: 15px;">NSSC Importer</h2>';
			murkconsole += '		<div class="loading" style="height: 80px;padding: 0;"></div>';
			murkconsole += '		<div id="nssc-updatemurk" style="display: none;">';
			murkconsole += '			<p>Last Import: <br /><strong id="nssc-lic" style="font-weight:bold;"></strong></p>';
			murkconsole += '			<p><button onclick="startUpdateMurk();return false;" class="murkupdatestart">Get Latest Reposts</a></button>';
			murkconsole += '		</div>';
			murkconsole += '		<div id="nssc-newmurk" style="display: none;">';
			murkconsole += '			<p>Grab the last <select id="murkdays">';
			sci = 1;
			while (sci !== 15) {
				murkconsole += '				<option value="'+sci+'">'+sci+'</option>';
				sci++;	
			}
			murkconsole += '			</select> days of reposts <button onclick="startFreshMurk();return false;" class="murkfreshstart">GO</button>';
			murkconsole += '		</div>';
			murkconsole += '	</div>';
			murkconsole += '</div>';
			// | <a href="#" onclick="stopCloudScrape();return false;" class="cloudscrapestop">Stop Bumscrape</a> | <a href="#" onclick="countCloudScrape();return false;" class="cloudscrapecount">Count</a> | <a href="#" onclick="murkReposts();return false;" class="cloudscrapemurk">One-off Murk</a> | <a href="#" onclick="grabTunes();return false;" class="cloudscrapegrab">Send To NSSC</a><br /><br /><a href="#" onclick="instaMurk();return false;" class="cloudscrapeinstamurk">InstaMURK&reg;</a></div>
			
		jQuery('body').append(murkconsole); }

		// .loading{background:transparent url(https://a-v2.sndcdn.com/assets/images/loader-38b02b00.gif) no-repeat center center;clear:both;text-align:center;height:40px;width:100%;padding:200px 0}.loading.dark{background-image:url(https://a-v2.sndcdn.com/assets/images/loader-dark-38b02b00.gif)}.loading.small{height:20px;background-size:16px}
		
// Check last repost info		
		
var url = ssldomain+'/nssc/nssc-rp-handle.php';
 
jQuery.ajax({
   type: 'GET',
    url: url,
	data: { nsscid : window.nsscid },
    async: false,
	jsonpCallback: 'jsonCallback',
    contentType: "application/json",
    dataType: 'jsonp',
    success: function(json) {
	   if (typeof(json.error) == 'undefined' && typeof(json.lastimport) != 'undefined') { 
		   jQuery('#nssc-lic').text(nsscTimeConverter(json.lastimport));
		   jQuery('.murkupdatestart').attr('onclick','startUpdateMurk("'+json.lastreposttime+'");return false;');
		   //console.log(nsscTimeConverter(json.lastreposttime));
	   } else {
			jQuery('#nssc-updatemurk').html('<p>No previous import detected.</p>');   
	   }
	   	   jQuery('#murkconsole .loading').hide();
		   jQuery('#nssc-updatemurk, #nssc-newmurk').show();
	   // if (typeof(json.admin) != 'undefined') {
		//	if (!jQuery('#cloudscrape').length) {
		//		jQuery('body').append(adminconsole);
		//	}
	   //}
    },
    error: function(e) {
       //console.log(e.message);
    }
});

// console.log(Date.parse(jQuery('li.soundList__item:first-child .sound__uploadTime time').attr('datetime')));

		
    });	
} else {
	var murkconsole = '<div id="murkconsole" style="position:fixed;top:10px;right:10px; padding: 10px; border: 2px solid orange;z-index:999999;text-align: left;background: #fff;min-width: 50px;">';
			murkconsole += '		<h2 style="margin-bottom: 15px;font-size: 20px;">This isn\'t your SoundCloud stream, silly...</h2>';
			murkconsole += '		</div>';	
	jQuery('body').append(murkconsole);
	jQuery('body #murkconsole').delay(3000).fadeOut(function() { jQuery('body #murkconsole, #nssc-script').remove(); });
}
}

var jqsrc = "//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js";
if(!window.jQuery)
{
   gmloadScript(jqsrc);
   onjQueryAvailable(startmurk)
   }
   else {  
    startmurk();
}