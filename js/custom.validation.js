/**
 * White Label Transfer
 * Custom JavaScript
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Wednesday, September 30, 2013, 12:55 PM GMT+1
 * @modified    $Date: 2013-09-27 02:57:45 -0700 (Fri, 27 Sep 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: jquery.observor.js 12 2013-09-27 09:57:45Z dev@whitelabeltransfer.com $
 *
 * @category    JavaScript
 * @package     White Label Transfer
*/

function uploadFormIsValid()
{
	var errors	= new Array();		
	var email	= trim( $('#email').val() );
	var sendTo	= trim( $('#recipients').val() );
	if( strlen( sendTo ) ) {
		sendTo = explode(',', sendTo);
	} else {
		sendTo = new Array();
	}
	
	if( !strlen( email ) ) {							
		errors.push('email');							
	}
	
	if( empty( sendTo ) ) {							
		errors.push('recipients');							
	}		
	
	if( empty( errors ) ) {
		return true;
	}
	
	return false;
}

function formIsValid( formId )
{
	var errors		= new Array();	
	var required	= $('#' + formId).find('input[data-required="1"]');
	
	if( !empty( required ) ) {
		required.each( function( index, value ) {
			if( !strlen( trim( $(this).val() ) ) ) {
				var id = $(this).attr('id');
				$('#' + id).addClass('inputError');
				errors.push( id );
			}
		});		
	}
	
	if( !empty( errors ) ) {
		return false;
	} else {
		required.each( function( index, value ) {
			var id = $(this).attr('id');
			$('#' + id).removeClass('inputError');
		});
		
		return true;		
	}	
}