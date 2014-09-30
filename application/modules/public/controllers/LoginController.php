<?php
/**
 * White Label Transfer
 * Bootstrap
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
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

class LoginController extends Zend_Controller_Action
{
	protected $_WeTransfer_Users;
	
    public function init() 
    {
    	$this->_WeTransfer_Users = new WeTransfer_Users;
    	
    	if( IS_MOBILE ) {
    		$this->view->setScriptPath( VIEWS_DIR.'/mobile/' );
    	} 	
    	
    	if( @$_SESSION['user']['logged_in'] ) {
    		$returnTo = ( strlen( @$_GET['returnTo'] ) ) ? $returnTo : SITE_DEFAULT_LANDING_PAGE_AFTER_LOGIN;
    		header( 'Location: '.BASEURL.'/'.$returnTo );
    	}
    }
    
    public function ajaxAction()
    {
    	$this->_helper->viewRenderer->setNoRender( true );
    	    	
		if( !empty( $_POST ) ) {	
			$method = $_POST['method'];
			header('Content-Type: application/json');			
			
			switch( $method ) {
				case 'userLogin':					
					$data			= $this->_WeTransfer_Users->login( $_POST['username'], $_POST['password'] );						
					$json			= array();
					$json['data']	= $data;
					
					if( $data == 'LOGIN_OK' ) {
						$json['status'] = 'OK';
					} else {
						session_unset();
						session_destroy();
						
						$json['status'] = 'ERROR';
						
						switch( $data ) {								
							default:
								$json['error'] = $data;
						}
					}					
					
					break;

				default:										
					$json			= array();
					$json['status'] = 'ERROR';
					$json['error']	= 'UNHANDLED_EXCEPTION';										
			}		
			
			exit( json_encode( $json ) );
					
		} else {
			header( 'Location: '.BASEURL.'' );			
		}    	
    }

    public function indexAction() {}
    
    /**
     * User Login
     */    
    public function loginAction()
    {    	    	
    	$username = $this->getRequest()->getParam('username');
    	$password = $this->getRequest()->getParam('password');   	    	
    	    	    	    	
    	if( strlen( trim( $username ) ) AND strlen( trim( $password ) ) ) {    		    		
    		$WeTransfer_Users 	= new WeTransfer_Users;
    		$loggedIn			= $WeTransfer_Users->login( $username, $password );

    		if( $loggedIn == 'LOGIN_OK' ) {
    			$_SESSION['user']['login_attempted']	= false;    			
    			$_SESSION['user']['login_error']		= false;
    			
    			$returnTo = ( strlen( @$_GET['returnTo'] ) ) ? $returnTo : SITE_DEFAULT_LANDING_PAGE_AFTER_LOGIN;
    			header( 'Location: '.BASEURL.'/'.$returnTo );
    		} else {
    			$_SESSION['user']['login_attempted']	= true;    			
    			$_SESSION['user']['login_error']		= true;
    			$this->_forward( null, 'accounts' );
    		}    		
    	} else {   		
    		$_SESSION['user']['login_attempted']	= true;    		
    		$_SESSION['user']['login_error']		= true;
    		   		
    		$this->_forward( null, 'accounts' );    		
    	}    	        	    	
    }
}