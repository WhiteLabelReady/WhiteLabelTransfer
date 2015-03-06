<?php
/**
 * White Label Transfer
 * Index Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2015 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Tuesday, November 27, 2012, 04:18 PM GMT+1
 * @modified    $Date: 2011-11-16 18:27:16 +0100 (Wed, 16 Nov 2011) $ $Author: mknox $
 * @version     $Id: IndexController.php 5139 2011-11-16 17:27:16Z mknox $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

class IndexController extends Zend_Controller_Action
{
	private $_WeTransfer_Users;
	
    public function init() 
    {
    	$this->_WeTransfer_Users = new WeTransfer_Users;
 
    	if( IS_MOBILE ) {    		
			$this->view->setScriptPath( VIEWS_DIR.'/mobile/' );
    	}
    }

    public function indexAction() 
    {    	
    	$backgrounds = glob( BASEDIR.'/images/backgrounds/*.{jpeg,jpg,png}', GLOB_BRACE );
    	
    	if( !empty( $backgrounds ) ) {
    		foreach( $backgrounds AS $key => $value ) {
    			$backgrounds[$key] = PROTOCOL_RELATIVE_URL.'/images/backgrounds/'.basename( $value );	
    		}	
    	}
    	
    	$this->view->bg = $backgrounds;	
    }
}