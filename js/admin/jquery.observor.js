/**
 * White Label Transfer
 * jQuery Observor
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Wednesday, July 10, 2013, 20:18 GMT+1
 * @modified    $Date: 2013-10-19 18:37:52 +0200 (Sa, 19 Okt 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: jquery.observor.js 93 2013-10-19 16:37:52Z dev@whitelabeltransfer.com $
 *
 * @category    JavaScript
 * @package     White Label Transfer
*/

$(document).ready(function() {	
	$('#dropdown-langChange').on('click', function(event) {
		var langId = $(this).data('langid');
		if( strlen( trim( langId ) ) ) {
			langId = parseInt( langId );	
		} else {
			return false;			
		}
		
		$.blockUI({ baseZ: 2014, message: '<img border="0" src="' + BASEURL + '/images/preloader/168.gif">' });
		$.ajax({
			type: 'POST',
			url: BASEURL + '/users/ajax',
			data: { method: 'changeLang',
					langId: langId
			},
			complete: function( jqXHR, textStatus ) {
				
			},
			success: function( response, textStatus, jqXHRresponse ) {
				if( response.status == 'OK' ) {
					window.location.reload();					
				} else {
					$.unblockUI();
				}
			},
			error: function(  jqXHR, textStatus, errorThrown ) {

			},		
			dataType: 'json'
		});
	});
	
	// START:	tab persistence
	// @link	http://stackoverflow.com/a/10524697
	$('a[data-toggle="tab"]').on('shown', function (e) {
		//save the latest tab; use cookies if you like 'em better:
		localStorage.setItem('lastTab', $(e.target).attr('id'));
	});

	// go to the latest tab, if it exists:
	var lastTab = localStorage.getItem('lastTab');
	if (lastTab) {
		$('#'+lastTab).tab('show');
	}	
	// END:		tab persistence
	
	$('.windowReload').on('click', function(event) {
		window.location.reload();
	});
	
	$('.blockUI-trigger, a').on('click', function(event) {	
		var target = $(this).attr('target');
		if( typeof target !== 'undefined' ) {
			if( target == '_blank' || target == 'new' ) {
				return;				
			}
		}
		
		var toggle = $(this).data('toggle');
		if( typeof toggle !== 'undefined' ) {
			return;
		}
		
		var elementClass = $(this).attr('class');
		if( typeof elementClass !== 'undefined' ) {
			elementClass = explode( ',', elementClass );
			
			$.each(elementClass, function( index, value ) {
				if( preg_match( '/disabled/', value ) ) {
					return;
				}
			});				
			
			if( in_array( 'blockUI-trigger', elementClass ) ) {
				$.blockUI({ message: '<img border="0" src="' + BASEURL + '/' + DEFAULT_PRELOADER_IMAGE + '" />', 
					overlayCSS: { backgroundColor: '#000000' }
				});
				
				return;
			}
		}
		
		var excludedClass = ['dropdown-toggle', 
		                     'fg-button', 
		                     'noBlockUI', 
		                     'Button', 
		                     'close', 
		                     'glyphicons'
		];
						
		// START:	check class
		if( typeof elementClass !== 'undefined' ) {
			var intersect	= new Array();			
			elementClass	= explode( ',', elementClass );
			intersect		= array_intersect( excludedClass, elementClass );
			
			if( !empty( intersect ) ) {
				return;
			}				
		}
		// END:		check class
		
		// START:	check id
		var excludedId = ['recaptcha_reload_btn', 
		                  'recaptcha_switch_audio_btn', 
		                  'recaptcha_switch_img_btn',
		                  'recaptcha_whatsthis_btn'
		];
		
		var elementId = $(this).attr('id');
		if( typeof elementId !== 'undefined' ) {						
			if( in_array( elementId, excludedId ) ) {
				return;
			}			
		}		
		// END:		check id
		
		// START:	check href
		var href = $(this).attr('href');
		if( typeof href !== 'undefined' ) {
			if( href == '#' ) {
				return;
			}
		}
		// END:		check href
		
		$.blockUI({ message: '<img border="0" src="' + BASEURL + '/' + DEFAULT_PRELOADER_IMAGE + '" />', 
					overlayCSS: { backgroundColor: '#000000' }
		});
	});
	
	$('#langSelect').change(function() {
		var langId = $(this).val();
		if( strlen( trim( langId ) ) ) {
			langId = parseInt( langId );	
		} else {
			return false;			
		}
		
		$.blockUI({ baseZ: 2014, message: '<img border="0" src="' + BASEURL + '/images/preloader/168.gif">' });
		$.ajax({
			type: 'POST',
			url: BASEURL + '/users/ajax',
			data: { method: 'changeLang',
					langId: langId
			},
			complete: function( jqXHR, textStatus ) {
				
			},
			success: function( response, textStatus, jqXHRresponse ) {
				if( response.status == 'OK' ) {
					window.location.reload();					
				} else {
					$.unblockUI();
				}
			},
			error: function(  jqXHR, textStatus, errorThrown ) {

			},		
			dataType: 'json'
		});	
	});	
	
	$('#frmAdminLogin').submit(function( event ) {
		$.blockUI();
		var password = trim( $('#passwordHolder').val() );
		if( strlen( password ) ) {
			event.preventDefault();								
			$.ajax({
				type: 'POST',
				url: BASEURL + '/admin/ajax',
				data: { method: 'login',
						username: $('#username').val(),
						password: sha1( password )
				},
				complete: function( jqXHR, textStatus ) {
					
				},
				success: function( response, textStatus, jqXHRresponse ) {
					if( response.status == 'LOGIN_OK' ) {
						window.location.reload();					
					} else {
						$.unblockUI();
					}
				},
				error: function(  jqXHR, textStatus, errorThrown ) {

				},		
				dataType: 'json'
			});	
		}
	});
	
	$('.cbCheckAllFiles').on('click', function(event) {
	     var checkedStatus = this.checked;
		$('.cbDataTables').prop('checked', checkedStatus);
	});
	
	$('.adminFileDelete').live('click', function(event) {
		var targetId = $(this).data('id');
		bootbox.confirm('Are you sure that you want to delete this upload?', function(result) {
			if( result ) {
				$.blockUI();
				$.ajax({
					'dataType': 'json',
					'type': 'POST',
					'url': BASEURL + '/admin/ajax',
					'data': {id: targetId, method: 'admin-deleteFile'},
					'complete': function() {

					},
					'success': function() {
						window.location.reload();
					}	
		        });				
			}
		}); 
	});
	
});