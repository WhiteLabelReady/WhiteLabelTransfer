<?php
/**
 * White Label Transfer
 * Logout Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2013 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Friday, November 29, 2013, 05:33 PM GMT+1
 * @modified    $Date: 2011-11-16 18:27:16 +0100 (Wed, 16 Nov 2011) $ $Author: mknox $
 * @version     $Id: IndexController.php 5139 2011-11-16 17:27:16Z mknox $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

define('THIS_PAGE', 'LOGOUT');
class LogoutController extends Zend_Controller_Action
{	
    public function init() 
    {    	    	
    	if( !@$_SESSION['user']['logged_in'] ) {
    		header( 'Location: '.BASEURL.'/login' );
    	}
    	 
    	session_unset();
    	session_destroy();
    	 
    	header( 'Location: '.BASEURL.'' );    	
    }

    public function indexAction() {}
}