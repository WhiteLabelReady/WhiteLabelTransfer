/**
 * White Label Transfer
 * Custom JavaScript
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Friday, September 27, 2013, 20:37 GMT+1
 * @modified    $Date: 2013-09-27 02:57:45 -0700 (Fri, 27 Sep 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: jquery.observor.js 12 2013-09-27 09:57:45Z dev@whitelabeltransfer.com $
 *
 * @category    JavaScript
 * @package     White Label Transfer
*/

function checkDestinationPerms()
{
	var returnVal = null;
	
	$.ajax({
		type: 'POST',
		url: BASEURL + '/media/ajax',
		data: { method: 'checkDestinationPerms' },
		complete: function( jqXHR, textStatus ) {
			
		},
		success: function( response, textStatus, jqXHRresponse ) {
			
			if( response.status == 'OK' ) {
				returnVal = response.message;					
			}
			
			return returnVal;						
		},
		error: function(  jqXHR, textStatus, errorThrown ) {

		},		
		dataType: 'json'
	});	
}

function isValidEmailAddress(emailAddress) 
{
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}

function bytesToSize(bytes) 
{
	   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	   if (bytes == 0) return '0 Bytes';
	   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

/**
 * Convert number of seconds into time object
 *
 * @param integer secs Number of seconds to convert
 * @return object
 */
function secondsToTime( secs, returnObj )
{
    var hours = Math.floor(secs / (60 * 60));
   
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
 
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);
   
    if( returnObj || returnObj === 'undefined' ) {
        var obj = {
                "hours": parseInt( hours ),
                "minutes": parseInt( minutes ),
                "seconds": parseInt( seconds )
            };
            
        return obj;    	
    } else {
    	var returnValue = '';
    	if( hours > 0 ) {
    		var hourString = ( hours != 1 ) ? ' hours ' : ' hour ';    		
    		returnValue = returnValue + hours + hourString; 
    	}
    	
    	if( minutes > 0 ) {
    		var minuteString = ( minutes != 1 ) ? ' minutes ' : ' minute ';
    		returnValue = returnValue + minutes + minuteString; 
    	}
    	
    	if( seconds > 0 ) {
    		var secondsString = ( seconds != 1 ) ? ' seconds ' : ' second ';    		
    		returnValue = returnValue + seconds + secondsString; 
    	}
    	
    	return returnValue;
    }
}

function pluploadLog( plupload ) 
{
    var str = "";

    plupload.each(arguments, function(arg) {
        var row = "";

        if (typeof(arg) != "string") {
            plupload.each(arg, function(value, key) {
                // Convert items in File objects to human readable form
                if (arg instanceof plupload.File) {
                    // Convert status to human readable
                    switch (value) {
                        case plupload.QUEUED:
                            value = 'QUEUED';
                            break;

                        case plupload.UPLOADING:
                            value = 'UPLOADING';
                            break;

                        case plupload.FAILED:
                            value = 'FAILED';
                            break;

                        case plupload.DONE:
                            value = 'DONE';
                            break;
                    }
                }

                if (typeof(value) != "function") {
                    row += (row ? ', ' : '') + key + '=' + value;
                }
            });

            str += row + " ";
        } else {
            str += arg + " ";
        }
    });

    console.log(str + "\n");
}