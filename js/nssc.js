var mainurl = 'http://siteurlhere.com';

jQuery(document).ready(function() {
	// Feedback button
	
	jQuery('.slide-out-div').tabSlideOut({
    	tabHandle: '.handle',
		pathToTabImage: '/wp-content/themes/lessshitsoundcloud/images/feedback-tab-custom.jpg',
    	imageHeight: '28px',
    	imageWidth: '129px',  
    	tabLocation: 'bottom', 
		speed: 300,
		action: 'click',
		leftPos: 'auto',
		fixedPosition: false   
	});
	
	
	jQuery('.meta a').click(function() {
		if (jQuery(this).hasClass('open')) {
			jQuery(this).html(jQuery(this).html().replace('<i class="fa fa-close"></i>','<i class="fa fa-plus"></i>')).removeClass('open');
			jQuery(this).parent().parent().find('pre').stop().fadeOut();
		}
		else {
			jQuery(this).html(jQuery(this).html().replace('<i class="fa fa-plus"></i>','<i class="fa fa-close"></i>')).addClass('open');
			jQuery(this).parent().parent().find('pre').stop().fadeIn();
		}
	});
	
	jQuery('.reposthelp .intro-copy').each(function() {
		jQuery(this).css({height:'auto'});
		jQuery(this).attr('data-height',jQuery(this).height());
		jQuery(this).css({height:'0px'});
	});
	
	jQuery('.reposthelp a.helper').click(function() {
		el = jQuery(this).parent().parent().find('.intro-copy')
		
		if (jQuery(this).hasClass('open')) {
			jQuery(this).html(jQuery(this).html().replace('<i class="fa fa-close"></i>','<i class="fa fa-plus"></i>')).removeClass('open');
			jQuery(el).stop().animate({height:0},500);
		}
		else {
			jQuery(this).html(jQuery(this).html().replace('<i class="fa fa-plus"></i>','<i class="fa fa-close"></i>')).addClass('open');
			jQuery(el).stop().animate({height:jQuery(el).attr('data-height')},500);
		}
	});
	
	
	jQuery('.track-description-cont').each(function() {
		jQuery(this).css({height:'auto'});
		jQuery(this).attr('data-height',jQuery(this).height());
		jQuery(this).css({height:'0px'});
		
		jQuery(this).parent().find('.trackmeta').append('<li class="desc-toggle"><a href="javascript:void(0)" onclick="scShowDesc(this);" class="show-desc awesome grey"><i class="fa fa-caret-right"></i> Show Description</a></li>');
	});
	
	if (jQuery("#nssc-slslider").length) {
	
		jQuery('.settingscont').css({height:'auto'});
		jQuery('.settingscont').attr('data-height',jQuery('.settingscont').height());
		jQuery('.settingscont').css({height:'0px'});
		
		var slideval = parseInt(jQuery('#nssc-slslider').data('defleng'));
		if (!slideval) slideval = 1200;
		
		jQuery("#nssc-slslider").noUiSlider({
			start: slideval,
			connect: "lower",
			range: {
				'min': 240,
				'max': 2000
			},
			format: wNumb({
				decimals: 0
			}),
			step:60
		});
		jQuery("#nssc-slslider").Link('lower').to(jQuery('#nssc-songlength'),timeFormatter);
		
		jQuery("#nssc-slslider").on({
			change: function(){
			jQuery(this).attr('disabled','disabled').addClass('working');
			var leng = jQuery(this).val();
			var el = this;
			jQuery.ajax({
				type	: 'POST',
				url		: ssd+'/inc/ajax.php',
				data	:	{ 
								task		: 'songlength',
								length		: leng,
								_wpnonce	: jsn
							},
				
				success : function( data, status, xhr )	{ 
					
					data = jQuery.parseJSON(data);
					
					if (typeof(data.error) == 'undefined') {	jQuery(el).removeAttr('disabled').removeClass('working').addClass('success').delay(3000).removeClass('success');	
					}
					
					else if (typeof(data.error) == 'undefined') {
						jQuery(el).addClass('error').removeClass('working').removeAttr('disabled');
					}
					
				},
				error : function( xhr, status, error ) {
					jQuery(el).addClass('error').removeClass('working').removeAttr('disabled');
				}
			});
			}
		});
	}

	jQuery('.buylink a').each(function() {	
		var dishref = jQuery(this).attr('href');
		var disfa = jQuery(this).find('i');
		var newfa = false;
		if (dishref.indexOf("bit.ly/") > -1 || dishref.indexOf("bitly.com/") > -1 || dishref.indexOf("smarturl.it/") > -1 || dishref.indexOf("tinyurl.com/") > -1 || dishref.indexOf("j.mp/") > -1 || dishref.indexOf("po.st/") > -1 || dishref.indexOf("msclvr.co/") > -1 || dishref.indexOf("geni.us/") > -1) {
			queueURLCheck(dishref,disfa); 
		} else if (dishref.indexOf("xlr8r.com/") > -1) {
			rapeXlr8r(dishref,this);
		} else {
			newfa = faUpdate(dishref);
		}
		if (newfa) {
			disfa.removeAttr('class');
			disfa.addClass('fa '+newfa);	
		}
	});

	jQuery('.nssc-activator').click(function() {
		jQuery(this).parent().find('.nssc-sm').toggle();
	});
	
	// Start ajaxManager for Dropbox downloads	
	ajaxManager.run();
});

function rapeXlr8r(url,el) {
	var hutumel = jQuery(el).html();
	if (hutumel.indexOf("download") > -1 || hutumel.indexOf("Download") > -1) { 
		var elid = 'xlr8r'+Math.floor(Math.random() * 10000)
		jQuery(el).html('<i class="fa fa-close"></i> Visit XLR8R');
		jQuery(el).parent().after('<li class="download xlr8r working" id="'+elid+'"></li>');
		ajaxManager.addReq({
				type	: 'POST',
				url		: ssd+'/inc/ajax.php',
				data	:	{ 
								task		: 'rapexlr8r',
								url			: url,
								_wpnonce	: jsn
							},
				
				success : function( data, status, xhr )	{ 
					
					data = jQuery.parseJSON(data);
					
					if (typeof(data.success) != 'undefined') {
						jQuery('#'+elid).removeClass('working').html('<a href="'+data.url+'" target="_blank" class="awesome nssc"><i class="fa fa-cloud-download"></i> XLR8R D/L</a>');
					} else if (typeof(data.error) != 'undefined') {
						jQuery('#'+elid).remove();
						jQuery(el).html(hutumel);
						jQuery(el).find('i').removeAttr('class').addClass('fa fa-close');
					}
				},
				error : function( xhr, status, error ) {
					jQuery('#'+elid).remove();
					jQuery(el).html(hutumel);
					jQuery(el).find('i').removeAttr('class').addClass('fa fa-close');
				}
			});	
	}
}

function queueURLCheck(url,el) {
	ajaxManager.addReq({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: 'urlresolve',
							url			: url,
							_wpnonce	: jsn
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.success) != 'undefined') {
					var newfa = faUpdate(data.url);
					if (newfa) {
						el.removeAttr('class');
						el.addClass('fa '+newfa);	
					}
				} else if (typeof(data.error) != 'undefined') {
					return false;
				}
			},
			error : function( xhr, status, error ) {
				return false;
			}
		});	
}


function faUpdate(url) {
	if (url.indexOf("soundcloud.com/") > -1) {
		return 'fa-soundcloud';
	} else if (url.indexOf("facebook.com/") > -1 || url.indexOf("fb.me/") > -1) {
		return 'fa-facebook';
	} else if (url.indexOf("junodownload.com/") > -1) {
		return 'fa-junodownload';
	} else if (url.indexOf("juno.co.uk/") > -1) {
		return 'fa-juno';
	} else if (url.indexOf("bandcamp.com") > -1) {
		return 'fa-bandcamp';
	} else if (url.indexOf("traxsource.com/") > -1) {
		return 'fa-traxsource';
	} else if (url.indexOf("beatport.com/") > -1 || url.indexOf("btprt.dj/") > -1) {
		return 'fa-beatport';
	} /* else if (url.indexOf("sendspace.com/") > -1) {
		return 'fa-sendspace';
	} */ else if (url.indexOf("dropbox.com/") > -1 || url.indexOf("dropboxusercontent.com/") > -1) {
		return 'fa-dropbox';
	} else if (url.indexOf("artistintelligence.agency/") > -1) {
		return 'fa-aia';
	} else if (url.indexOf("emailunlock.com/") > -1) {
		return 'fa-envelope';
	} else if (url.indexOf("youtube.com/") > -1 || url.indexOf("youtu.be/") > -1) {
		return 'fa-youtube';
	} else if (url.indexOf("itunes.com/") > -1 || url.indexOf("itunes.apple.com/") > -1 || url.indexOf("itun.es/") > -1) {
		return 'fa-apple';
	} else {
		return false;
	}
}

function timeFormatter( value ) {
			var minutes = Math.floor(parseInt(value) / 60);
			jQuery(this).html(minutes+' minutes');
			}
    
function scShowDesc(em) {
		el = jQuery(em).parent().parent() .parent() .parent().find('.track-description-cont');
		if (jQuery(em).hasClass('open')) {
			jQuery(em).html('<i class="fa fa-caret-right"></i> Show Description').removeClass('open');
			jQuery(el).attr('data-height',jQuery(el).height());
			jQuery(el).stop().animate({height:0},500);
		}
		else {
			
			jQuery(em).html('<i class="fa fa-close"></i> Hide Description').addClass('open');
			jQuery(el).stop().animate({height:jQuery(el).attr('data-height')},500, function() {
				jQuery(el).css({height:'auto'});
			});
		}
}

function scSettingPanel() {
		el = jQuery('.settingscont');
		if (jQuery(el).hasClass('open')) {;
			jQuery(el).attr('data-height',jQuery(el).height()).removeClass('open');
			jQuery(el).stop().animate({height:0},500);
		}
		else {
			jQuery(el).addClass('open').stop().animate({height:jQuery(el).attr('data-height')},500, function() {
				jQuery(el).css({height:'auto'});
			});
		}
}

function nsscComment(scid,el) {
	var ttitle = jQuery(el).parent().parent().parent().find('.tracktitle a').text();
	 var lightbox = '<div id="commentlightbox">';
		lightbox += '	<h3>Leaving a comment on <span class="track-title">'+ttitle+'</h3>';
		lightbox += '	<form id="commentform">';
		lightbox += '		<textarea name="commenttext" class="commentbox" placeholder="Leave a comment on '+ttitle+'..."></textarea>';
		lightbox += '		<input type="hidden" name="scid" class="comment-scid" value="'+scid+'" />';
		lightbox += '		<a href="javascript:void(0)" class="awesome nssc" onclick="nsscCommentSit();return false;">Leave A Comment</a>';
		lightbox += '	</form>';
		lightbox += '</div>';
		jQuery.featherlight(lightbox);
}

function nsscCommentSit() {  
		jQuery('#commentlightbox').addClass('working');        
		jQuery.ajax({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: 'comment',
							_wpnonce	: jsn,
							scid		: jQuery('#commentlightbox .comment-scid').val(),
							commenttext	: jQuery('#commentlightbox .commentbox').val() 
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.success) != 'undefined') {
					jQuery('#commentlightbox').removeClass('working');
					jQuery.featherlight.close();
				}
				
				else if (typeof(data.error) == 'undefined') {
					jQuery('#commentlightbox').addClass('error').removeClass('working');
				}
				
			},
			error : function( xhr, status, error ) {
				jQuery('#commentlightbox').addClass('error').removeClass('working');
			}
		});
}

function nsscLike(scid,el,type) {
	var task = false;
	if ( jQuery(el).hasClass('liking') ) { 
		return false; 
	} else {
		jQuery(el).addClass('liking');
		if ( jQuery(el).hasClass('liked') ) { 
			task = 'unlike'; 
		} else { 
			task = 'like'; 
		}
		
		jQuery.ajax({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: task,
							_wpnonce	: jsn,
							scid		: scid,
							type		: type
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.error) == 'undefined') {
					if (task == 'like') {
						if (jQuery(el).hasClass('likebutts')) {
							jQuery(el).html('<i class="fa fa-heart"></i> Liked').removeClass('liking').addClass('liked');
						}
						else {
							jQuery(el).html('<i class="fa fa-heart"></i>').removeClass('liking').addClass('liked');	
						}
					} else {
						if (jQuery(el).hasClass('likebutts')) {
							jQuery(el).html('<i class="fa fa-heart-o"></i> Like').removeClass('liking').removeClass('liked');
						}
						else {
							jQuery(el).html('<i class="fa fa-heart-o"></i>').removeClass('liking').removeClass('liked');
						}
					}
				}
				
				else if (typeof(data.error) == 'undefined') {
					jQuery(el).addClass('error').removeClass('liking');
				}
				
			},
			error : function( xhr, status, error ) {
				jQuery(el).addClass('error').removeClass('liking');
			}
		});
	}
}

function nsscFollow(scid,el) {
	var task = false;
	if ( jQuery(el).hasClass('following') ) { 
		return false; 
	} else {
		jQuery(el).addClass('following');
		if ( jQuery(el).hasClass('followed') ) { 
			task = 'unfollow'; 
		} else { 
			task = 'follow'; 
		}
		
		jQuery.ajax({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: task,
							_wpnonce	: jsn,
							scid		: scid
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.error) == 'undefined') {
					if (task == 'follow') {
						jQuery(el).html('<i class="fa fa-eye" title="Unfollow"></i>').removeClass('following').addClass('followed');	
					} else {
						jQuery(el).html('<i class="fa fa-eye-slash" title="Follow"></i>').removeClass('following').removeClass('followed');
					}
				}
				
				else if (typeof(data.error) == 'undefined') {
					jQuery(el).addClass('error').removeClass('following');
				}
				
			},
			error : function( xhr, status, error ) {
				jQuery(el).addClass('error').removeClass('following');
			}
		});
	}
}

function limitChange(el) {
	if ( jQuery(el).hasClass('changing') ) { 
		return false; 
	} else {
		jQuery(el).addClass('changing').attr('disabled','disabled');
		var lim = jQuery(el).val();
		jQuery.ajax({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: 'limitchange',
							limitno		: lim,
							_wpnonce	: jsn
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.error) == 'undefined') {	jQuery(el).removeAttr('disabled').removeClass('changing').addClass('success').delay(3000).removeClass('success');	
				var url = location.protocol + "//" + document.domain + "/" + location.pathname.split('/')[1] + "/";
				window.location = url;
				}
				
				else if (typeof(data.error) == 'undefined') {
					jQuery(el).addClass('error').removeClass('changing').removeAttr('disabled');
				}
				
			},
			error : function( xhr, status, error ) {
				jQuery(el).addClass('error').removeClass('changing').removeAttr('disabled');
			}
		});
	}
}

function lengthChange(el) {
	if ( jQuery(el).hasClass('changing') ) { 
		return false; 
	} else {
		jQuery(el).addClass('changing').attr('disabled','disabled');
		var leng = jQuery(el).val();
		jQuery.ajax({
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: 'lengthchange',
							length		: leng,
							_wpnonce	: jsn
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.error) == 'undefined') {	jQuery(el).removeAttr('disabled').removeClass('changing').addClass('success').delay(3000).removeClass('success');	
				}
				
				else if (typeof(data.error) == 'undefined') {
					jQuery(el).addClass('error').removeClass('changing').removeAttr('disabled');
				}
				
			},
			error : function( xhr, status, error ) {
				jQuery(el).addClass('error').removeClass('changing').removeAttr('disabled');
			}
		});
	}
}

function deauthDB() {
	if ( jQuery('#dropboxpanel').hasClass('connecting') ) { 
		return false; 
	} else {
		var cm = confirm('Are you SURE you want to disconnect Dropbox?');
		if (cm) {
			jQuery('#dropboxpanel').addClass('connecting');
			jQuery.ajax({
				type	: 'POST',
				url		: ssd+'/inc/ajax.php',
				data	:	{ 
								task		: 'deauthdropbox',
								_wpnonce	: jsn
							},
				
				success : function( data, status, xhr )	{ 
					
					data = jQuery.parseJSON(data);
					
					if (typeof(data.success) != 'undefined') {
						jQuery('#dropboxpanel').removeClass('connecting');
						jQuery('#dropboxpanel').html('<p class="nssc-option"><a href="/?nssc-dbauth" class="awesome nssc"><i class="fa fa-dropbox"></i> Connect Dropbox</a></p>');
					}
				},
				error : function( xhr, status, error ) {
					jQuery('.connect-dropbox').addClass('error').removeClass('connecting');
				}
			});
		} else {
			return false;	
		}
	}	
}

function clearRepostCache() {
if ( jQuery('.clear-reposts').hasClass('clearing') ) { 
		return false; 
	} else {
		var cm = confirm('Are you SURE you want to clear your repost cache? There\'s no undo, so you\'ll have to re-import...');
		if (cm) {
			jQuery('.clear-reposts').addClass('clearing');
			jQuery.ajax({
				type	: 'POST',
				url		: ssd+'/inc/ajax.php',
				data	:	{ 
								task		: 'clearrepostcache',
								_wpnonce	: jsn
							},
				
				success : function( data, status, xhr )	{ 
					
					data = jQuery.parseJSON(data);
					
					if (typeof(data.success) != 'undefined') {
						jQuery('.clear-reposts').removeClass('clearing');
						alert('Cache Cleared');
					}
				},
				error : function( xhr, status, error ) {
					jQuery('.clear-reposts').addClass('error').removeClass('clearing');
				}
			});
		} else {
			return false;	
		}
	}
}

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};

function nsscDBUpload(scid,el,scsize) {
if ( jQuery(el).hasClass('uploading') ) { 
		return false; 
	} else {	
		jQuery(el).addClass('uploading');
		dbQueueItem(scid,jQuery(el).parent().parent().parent().find('.titlebox h3.tracktitle').text(),bytesToSize(scsize));
		ajaxManager.addReq({
			kickoff : function() {dbQueueItemBegin(scid);},
			type	: 'POST',
			url		: ssd+'/inc/ajax.php',
			data	:	{ 
							task		: 'dbupload',
							scid		: scid,
							_wpnonce	: jsn
						},
			
			success : function( data, status, xhr )	{ 
				
				data = jQuery.parseJSON(data);
				
				if (typeof(data.success) != 'undefined') {
					jQuery(el).removeClass('uploading');
					dbQueueItemComplete(scid);
				} else if (typeof(data.error) != 'undefined') {
					jQuery(el).removeClass('uploading');
					dbQueueItemFail(scid);
				}
			},
			error : function( xhr, status, error ) {
				jQuery(el).removeClass('uploading');
				dbQueueItemFail(scid);
			}
		});
	}
}

function dbQueueSetup() {
	if (jQuery('#dbqueue').length) {
		return false;
		} else {
			var queuebox = '<div id="dbqueue">';
			queuebox += '</div>';
		
			jQuery('body').append(queuebox);
			window.onbeforeunload = function() {
			  return 'You currently have items uploading to Dropbox. Are you sure you want to leave?' ;
			}
		}
}

function dbQueueItem(scid,sctitle,scsize) {
	if (!jQuery('#dbqueue').length) {
			dbQueueSetup();
		}
	
	var queueitem = '<div id="dbqi-'+scid+'" class="dbqueueitem">';
	queueitem += '<span class="dbqi-title">'+sctitle+'</span> <span class="dbqi-size">'+scsize+'</span>';
	queueitem += '</div>';
	
	jQuery('#dbqueue').append(queueitem);
}

function dbQueueItemBegin(scid) {
	jQuery('#dbqi-'+scid).addClass('working');
}

function dbQueueItemComplete(scid) {
	jQuery('#dbqi-'+scid).removeClass('working').html('<span class="dbun dbun-success">Upload Successful</span>').delay(3000).fadeOut(750,function() {
		jQuery(this).remove();
		if (!jQuery('#dbqueue .dbqueueitem').length) {
			jQuery('#dbqueue').remove();
			window.onbeforeunload = null;
		}
	});
}

function dbQueueItemFail(scid) {
	jQuery('#dbqi-'+scid).removeClass('working').html('<span class="dbun dbun-fail">Upload failed</span>').delay(3000).fadeOut(750,function() {
		jQuery(this).remove();
		if (!jQuery('#dbqueue .dbqueueitem').length) {
			jQuery('#dbqueue').remove();
			window.onbeforeunload = null;
		}
	});	
}


function nsscShare(el) {
	var earl = jQuery(el).parent().parent().parent().parent().find('.titlebox .tracktitle a').attr('href'),
	trackname = jQuery(el).parent().parent().parent().parent().find('.titlebox .tracktitle a').text(),
	artist = jQuery(el).parent().parent().parent().parent().find('.artist a:first-child').text(),
	source = mainurl,
	sourcetwit = 'MrBrainz',
	
	// Share URLs to use
	nsscSPUTwitURL = 'https://twitter.com/intent/tweet?original_referer='+source+'&source=nssc&text='+encodeURIComponent(artist+' - '+trackname)+'&url='+encodeURIComponent(earl)+'&via='+sourcetwit,
	nsscSPUFacebookURL = 'http://www.facebook.com/sharer.php?s=100&p[title]='+encodeURIComponent(artist+' - '+trackname) + '&p[summary]=' + encodeURIComponent(artist+' - '+trackname) + '&p[url]=' + encodeURIComponent(earl),
	nsscSPULinkedInURL = 'http://www.linkedin.com/shareArticle?mini=true&url='+encodeURIComponent(earl)+'&title='+encodeURIComponent(artist+' - '+trackname)+'&summary='+encodeURIComponent(artist+' - '+trackname);
	nsscSPUGPlusURL = 'https://plus.google.com/share?url='+encodeURIComponent(earl);
	
	nsscSPU(artist+' - '+trackname);


}

window.popupCenter=function(earl,wid,hei,targ){var leftt,topp;return leftt=screen.width/2-wid/2,topp=screen.height/2-hei/2,window.open(earl,targ,"menubar=no,toolbar=no,status=no,width="+wid+",height="+hei+",toolbar=no,left="+leftt+",top="+topp)}

function nsscSPU(getcopy) {
    if (!jQuery('#nsscSPU-cont').length) {                 
        var nsscSPUHTML = '<div id="nsscSPU-cont"><span class="nsscSPU-tl"></span><span class="nsscSPU-t"></span><span class="nsscSPU-tr"></span><span class="nsscSPU-l"></span><div id="nsscSPU-wrap"><span class="nsscSPU-close"></span><div class="nsscSPU-copy"><h2>Share This Track</h2><p>'+getcopy+'</p></div><div class="nsscSPU-social-cont"><ul class="nsscSPU-social"><li class="nsscSPU-twitter"><a href="#"></a></li><li class="nsscSPU-facebook"><a href="#"></a></li><li class="nsscSPU-linkedin"><a href="#"></a></li><li class="nsscSPU-gplus"><a href="#"></a></li></ul></div></div><span class="nsscSPU-r"></span><span class="nsscSPU-bl"></span><span class="nsscSPU-b"></span><span class="nsscSPU-br"></span><span class="nsscSPU-fill-tl"></span><span class="nsscSPU-fill-tr"></span><span class="nsscSPU-fill-bl"></span><span class="nsscSPU-fill-br"></span></div>';
        jQuery(function(){            
            jQuery('body').append(nsscSPUHTML);
            var nsscSPUh = (jQuery('#nsscSPU-cont').height())/2;
            jQuery('#nsscSPU-cont').css('marginTop','-'+nsscSPUh+'px');
            jQuery('#nsscSPU-cont .nsscSPU-l, #nsscSPU-cont .nsscSPU-r').height(jQuery('#nsscSPU-cont').height()-238);
            jQuery('#nsscSPU-cont .nsscSPU-close').click( function() {
                jQuery('#nsscSPU-cont').fadeOut( function() { jQuery('#nsscSPU-cont').remove(); });        
            });
            jQuery('.nsscSPU-facebook a').click( function() {
                popupCenter(nsscSPUFacebookURL,685,300);
                shareRegSocial('reg:facebook');
                return false;
            });
            jQuery('.nsscSPU-linkedin a').click( function() {
                popupCenter(nsscSPULinkedInURL,600,500);
                shareRegSocial('reg:linkedin');
                return false;
            });
            jQuery('.nsscSPU-twitter a').click( function() {
                popupCenter(nsscSPUTwitURL,685,260);
                shareRegSocial('reg:twitter');
                return false;
            });
            jQuery('.nsscSPU-gplus a').click( function() {
                popupCenter(nsscSPUGPlusURL,600,600);
                shareRegSocial('reg:googleplus');
                return false;
            });
            jQuery('#nsscSPU-cont').fadeIn();
        });
    }
}


// AJAX Manager
		
var ajaxManager = (function() {
	 var requests = [];

	 return {
		addReq:  function(opt) {
			requests.push(opt);
		},
		removeReq:  function(opt) {
			if( jQuery.inArray(opt, requests) > -1 )
				requests.splice(jQuery.inArray(opt, requests), 1);
		},
		run: function() {
			var self = this,
				oriSuc;

			if( requests.length ) {
				oriSuc = requests[0].complete;

				requests[0].complete = function() {
					 if( typeof(oriSuc) === 'function' ) oriSuc();
					 requests.shift();
					 self.run.apply(self, []);
				};   
				
				if('kickoff' in requests[0]) requests[0].kickoff();
				jQuery.ajax(requests[0]);
			} else {
			  self.tid = setTimeout(function() {
				 self.run.apply(self, []);
			  }, 1000);
			}
		},
		stop:  function() {
			requests = [];
			clearTimeout(this.tid);
		}
	 };
}());
