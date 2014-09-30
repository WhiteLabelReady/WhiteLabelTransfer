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
});