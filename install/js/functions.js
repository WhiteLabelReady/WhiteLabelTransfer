/**
 * Various JavaScript Functions
 *
 * @author      BizLogic.com <hire@whitelabeltransfer.com>
 * @license     GNU Affero General Public License v3
 * @link        http://whitelabeltransfer.com
 * @link		http://bizlogicdev.com
 *
 * @since       Wednesday, April 25, 2012 / 01:47 AM GMT+1 mknox
 * @edited      $Date: 2011-03-10 12:38:09 +0100 (Thu, 10 Mar 2011) $ $Author: mknox $
 * @version     $Revision: 1 $
 *
 * @package     White Label Transfer
 * @category	Installer
*/

$('#logo').live('click', 
		function() {
			blockUIWithMessage( 'Loading...', 'Loading, please wait... <i class="fa fa-spinner fa-pulse"></i>' );	
			window.location = BASEURL; 
		}
);

function blockUIWithMessage( title, message, timeout )
{ 	
	title	= ( typeof title !== 'undefined' && strlen( title ) ) ? title : 'Loading...';
	message = ( typeof message !== 'undefined' && strlen( message ) ) ? message : 'Loading, please wait... <i class="fa fa-spinner fa-pulse"></i>';
	timeout	= ( typeof timeout !== 'undefined' && is_numeric( timeout ) ) ? timeout : 0;
	
	if( timeout > 0 ) {		
		$.blockUI({ 
			theme:		true, 
			title:    	title, 
			message:	message,
			timeout:	timeout
		});	
	} else {
		$.blockUI({ 
			theme:		true, 
			title:    	title, 
			message:	message			
		});	
	}
}

function reloadPage()
{
	window.location.reload();
}