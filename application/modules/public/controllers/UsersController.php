<?php
/**
 * White Label Transfer
 * Users Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Thursday, November 29, 2012, 12:52 PM GMT+1
 * @modified    $Date: 2013-10-19 18:33:08 +0200 (Sa, 19 Okt 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: UsersController.php 92 2013-10-19 16:33:08Z dev@whitelabeltransfer.com $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

class UsersController extends Zend_Controller_Action
{
	private $_requestObj;
	private $_requestUri;
	private $_WeTransfer_Users;
	
    public function __call( $method, $args ) {}

    public function init()
    {
    	if( IS_MOBILE ) {
    		$this->view->setScriptPath( VIEWS_DIR.'/mobile/' );
    	}
    	    	
    	$this->_WeTransfer_Users = new WeTransfer_Users;    	
    	    	
    	if( isset( $_SESSION['user']['logged_in'] ) ) {
    		$this->_WeTransfer_Users->updateUserSession();
    	}
    	    	
        $this->_requestObj	= $this->getRequest();
        $this->_requestUri	= $this->_requestObj->getRequestUri();
        
        $action = $this->getRequest()->getParam('action');                
    }
    
    public function ajaxAction()
    {
		$this->_helper->viewRenderer->setNoRender( true );
    	    	
		if( !empty( $_POST ) ) {
			header('Content-Type: application/json');						
			
			$method = $_POST['method'];
			$json	= array();			
			
			switch( $method ) {
				case 'removeTempAvatar':
					$result = (int)$this->_WeTransfer_Users->removeOwnTempAvatar();
					
					if( $result > 0 ) {
						$json['status'] = 'OK';
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= 'TEMP_DELETE_ERROR';
					}
					 
					break;
					
				case 'changeOwnAvatar':
					$result	= $this->_WeTransfer_Users->changeOwnAvatar();
					if( $result['status'] == 'OK' ) {
						$json['status'] = 'OK';
						$json['url']	= $result['url'];
					} else {
						$json['status'] = 'ERROR';
					}
				
					break;
											
				case 'uploadOwnAvatar':					
					$result	= $this->_WeTransfer_Users->uploadOwnAvatar();
					if( $result['status'] == 'OK' ) {
						$json['status'] = 'OK';
						$json['url']	= $result['url'];
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= $result['error'];
					}
				
					break;
									
				case 'unblockUser':
					$result	= (int)$this->_WeTransfer_Users->unblockUserById( $_POST['requesterId'], $_POST['id'] );
					if( $result > 0 ) {
						$json['status'] = 'OK';
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= $result;
					}
				
					break;
									
				case 'blockUser':
					$result	= (int)$this->_WeTransfer_Users->blockUserById( $_POST['requesterId'], $_POST['id'] );
					if( $result > 0 ) {
						$json['status'] = 'OK';
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= $result;
					}
				
					break;
					
				case 'resetOwnPassword':
					$result	= $this->_WeTransfer_Users->resetOwnPassword( $_POST['username'], $_POST['challenge'], $_POST['response'] );
					if( $result == 'OK' ) {
						$json['status'] = 'OK';
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= $result;
					}
				
					break;
									
				case 'updateTheme':
					setcookie( 'theme', $_POST['theme'], 315360000 );
					$json['status'] = 'OK';
				
					break;
									
				case 'changeOwnPassword':
					$result	= $this->_WeTransfer_Users->changeOwnPassword( $_POST['password'], $_POST['new_password'] );
					if( $result ) {
						$json['status'] = 'OK';
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= 'PASSWORD_ERROR';
					}
				
					break;
									
				case 'changeLang':
					$result	= $this->_WeTransfer_Users->changeUserLangByLangId( (int)$_POST['langId'] );
					if( $result ) {
						$json['status'] = 'OK';						
					} else {
						$json['status'] = 'ERROR';
						$json['error']	= 'LANG_ID_DOES_NOT_EXIST';
					}
										
					break;

				default:
					$json['status'] = 'ERROR';
					$json['error']	= 'UNHANDLED_EXCEPTION';
			}	
					
			exit( json_encode( $json ) );			
		} else {
			header( 'Location: '.BASEURL.'');
		}     	
    }

    public function indexAction() {}

    public function editAction() {}
}