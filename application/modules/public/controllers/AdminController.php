<?php
/**
 * White Label Transfer
 * Admin Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Saturday, August 31, 2013, 19:48 GMT+1
 * @modified    $Date: 2011-11-16 18:27:16 +0100 (Wed, 16 Nov 2011) $ $Author: mknox $
 * @version     $Id: IndexController.php 5139 2011-11-16 17:27:16Z mknox $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

class AdminController extends Zend_Controller_Action
{ 
	protected $_WeTransfer_Admin;
	protected $_WeTransfer_Site_Config;
	protected $_WeTransfer_Site_Phrases;
	protected $_WeTransfer_Site_Permissions;
	protected $_WeTransfer_Users;
	protected $_WeTransfer_Usergroups;
	protected $_WeTransfer_Files;
		
    public function init() 
    {  
    	$this->_WeTransfer_Admin			= new WeTransfer_Admin;
    	$this->_WeTransfer_Files			= new WeTransfer_Files;
    	$this->_WeTransfer_Site_Config		= new WeTransfer_Site_Config;
    	$this->_WeTransfer_Site_Phrases		= new WeTransfer_Site_Phrases;
    	$this->_WeTransfer_Site_Permissions	= new WeTransfer_Site_Permissions;
    	$this->_WeTransfer_Usergroups		= new WeTransfer_Usergroups;
    	$this->_WeTransfer_Users			= new WeTransfer_Users;    	       	    	
    	
    	if( isset( $_SESSION['user']['logged_in'] ) ) {
    		$this->_WeTransfer_Users->updateUserSession();
    	}
    	
    	$actionName = strtolower( Zend_Controller_Front::getInstance()->getRequest()->getActionName() );
		switch( $actionName ) {
			case 'ajax':
			case 'login':
			case 'logout':
				break;
				
			default:
				if( !in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {
					// $this->_forward($action, $controller = null, $module = null, array $params = null)
					$this->_forward('login');
				}								
		}
    }
    
    public function profileAction() {}
    
    public function settingsAction()
    {
    	$this->_forward('index');
    }    
    
    public function usergroupPermissionsAction()
    {
    	$usergroupId = (int)$this->getRequest()->getParam('id');

    	if( empty( $usergroupId ) ) {
    		$this->_forward('usergroups');
    	} else {
    		if( !empty( $_POST['usergroup'] ) ) {
    			$this->_WeTransfer_Usergroups->updateUsergroupById( $usergroupId, $_POST['usergroup'] );
    			$this->view->update = true;
    		}
    		 
    		$usergroup = $this->_WeTransfer_Usergroups->fetchUsergroupById( $usergroupId );   		
    		
    		if( !empty( $usergroup ) ) {
    			$this->view->usergroup = $usergroup;
    		} else {
    			// display error
    		}    		
    	}    	
    }
    
    public function usergroupEditAction()
    {
    	$usergroupId = (int)$this->getRequest()->getParam('id');    			
    	
    	if( !empty( $_POST['usergroup'] ) ) {
    		$this->_WeTransfer_Usergroups->updateUsergroupById( $usergroupId, $_POST['usergroup'] );
    		$this->_WeTransfer_Usergroups->updateUsergroupPermissionsById( $usergroupId, $_POST['permissions'] );    		
    		$this->view->update = true;
    	}
    	 
    	$usergroup = $this->_WeTransfer_Usergroups->fetchUsergroupById( $usergroupId );
    	$this->view->allPerms = $this->_WeTransfer_Site_Permissions->fetchAllSitePermissions();
  	    	
    	if( !empty( $usergroup ) ) {  
    		$usergroup['permissions'] = $this->_WeTransfer_Site_Permissions->fetchUsergroupPermissionsByUsergroupId( array( $usergroup['id'] ) );    		  		    		    		    		
    		$this->view->usergroup = $usergroup;
    	} else {
    		// display error
    	}    	
    }    
    
    public function usergroupsAction()
    {
    	$usergroups = $this->_WeTransfer_Usergroups->fetchAllUsergroups();
    	
    	if( !empty( $usergroups ) ) {
    		foreach( $usergroups AS $key => $value ) {    	
    			$usergroups[$key]['edit'] = '<a href="'.PROTOCOL_RELATIVE_URL.'/admin/usergroup-edit/id/'.$value['id'].'" title="Edit" alt="Edit"><span style="float: left" class="ui-icon ui-icon-pencil"></span></a> <a data-name="'.$value['name'].'" data-id="'.$value['id'].'" class="noBlockUI deleteUsergroup" href="#" title="Delete" alt="Delete"><span style="float: left" class="ui-icon ui-icon-trash"></span></a>';
    		}
    	}
    	    	
    	$this->view->usergroups = $usergroups;
    }
    
    public function userEditAction()
    {    	    	
		$userId = (int)$this->getRequest()->getParam('id');
		if( $userId == 0 ) {
			$this->_forward('users');	
		} else {
			
			if( !empty( $_POST['user'] ) ) {
				$this->_WeTransfer_Users->updateUserById( $userId, $_POST['user'] );
				$this->view->update = true;
			}			
			
			$user = $this->_WeTransfer_Users->fetchUserDetailsById( $userId );
			if( !empty( $user ) ) {
				$this->view->user = $user;	
			} else {
				// display error	
			}	
		}    	
    }
    
    public function usersAction()
    {
    	if( !in_array( 'can_admin_users', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
    		$this->_forward('login');
    	}
    	    	
    	$users = $this->_WeTransfer_Users->fetchAllUsers( array('id',  
    															'email', 
    															'site_status', 
    															'last_ip',
    															'last_active'
    	));
    	
    	if( !empty( $users ) ) {
			foreach( $users AS $key => $value ) {
				if( isset( $value['avatar_url'] ) ) {
					$users[$key]['avatar_url'] = ( strlen( $value['avatar_url'] ) ) ? $value['avatar_url'] : SITE_DEFAULT_AVATAR_URL;
				}
								
				if( isset( $value['username'] ) ) {
					$users[$key]['username_raw'] 	= $value['username'];					
					$users[$key]['username']		= '<a class="noBlockUI" target="_blank" href="'.PROTOCOL_RELATIVE_URL.'/'.$value['username'].'">'.$value['username'].'</a>';
				}

				$users[$key]['edit'] = '<a href="'.PROTOCOL_RELATIVE_URL.'/admin/user-edit/id/'.$value['id'].'" title="Edit" alt="Edit"><span style="float: left" class="ui-icon ui-icon-pencil"></span></a> <a data-username="'.$value['username'].'" data-userid="'.$value['id'].'" class="noBlockUI deleteUser" href="#" title="Delete" alt="Delete"><span style="float: left" class="ui-icon ui-icon-trash"></span></a>';
			}    		
    	}
    	
    	$this->view->users = $users;
    }

    public function indexAction() 
    {    	
    	if( !in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
			$this->_forward('login');
    	} else {
    		if( !empty( $_POST['config'] ) ) {
    			$this->_WeTransfer_Site_Config->updateSiteConfig( $_POST['config'] );
    			$this->view->update = true;
    		}
    		    		
    		$siteConfig = $this->_WeTransfer_Site_Config->fetchSiteConfig();
    		$categories = $this->_WeTransfer_Site_Config->fetchSiteConfigCategories();

    		$this->view->categories = $categories;
    		$this->view->siteConfig = $siteConfig;
    	}
    }
    
    public function loginAction()
    {
    	if( !empty( $_POST ) ) {
    		$result = $this->_WeTransfer_Admin->login( $_POST['username'], $_POST['password'] );
    		if( $result == 'LOGIN_OK' ) {
				if( in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {    	
					header( 'Location: '.BASEURL.'/admin');
					exit;
    			}    			
    		}
    	} else {
    		if( in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {
				header('Location: '.BASEURL.'/admin');
				exit;
    		}    		
    	}    	    	
    } 
    
    public function logoutAction()
    {
		session_unset();
		session_destroy();
		
		header('Location: '.BASEURL.'/admin');		
    }    

    public function filesAction()
    {
    	if( !in_array('can_admin_files', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
    		$this->_forward('login');
    	}
    }

    public function filesOrphanedAction()
    {
    	if( !in_array('can_admin_files', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
    		$this->_forward('login');
    	}
    }    

    public function accountAction()
    {
    	if( !in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
    		$this->_forward('login');
    	}
    	    	
    	$this->view->me = $this->_WeTransfer_Users->fetchUserDetailsById( $_SESSION['user']['id'] );
    }
    
    public function phrasesAction()
    {
    	if( !in_array( 'can_admin_site_phrases', $_SESSION['site']['permissions']['admin'] ) ) {
    		// $this->_forward($action, $controller = null, $module = null, array $params = null)
    		$this->_forward('login');
    	}
    	    	
    	if( !empty( $_POST ) ) {
			// $_POST['id'] is an array of phrases    		
    		$this->_WeTransfer_Site_Phrases->updateSitePhrases( $_POST['id'] );
    		$this->view->update = true;
    	}
    	
    	$phrases = $this->_WeTransfer_Site_Phrases->fetchAllPhrases();
    	foreach( $phrases AS $key => $value ) {
    		$phrases[$key]['friendly_name'] = WeTransfer_Languages::fetchFriendlyNameById( $value['language_id'] );	
    	}
    	
    	// START:	sort by language name
    	$phrasesFinal = array();
    	foreach( $phrases AS $key => $value ) {
    		$phrasesFinal[$value['friendly_name']][] = $value;
    	}
    	// END:		sort by languaga name
    	
    	$this->view->phrases = $phrasesFinal;
    }
    
    public function ajaxAction()
    {
    	$this->_helper->viewRenderer->setNoRender( true );
    	    	
    	if( $_POST['method'] != 'login' ) {
    		if( !in_array( 'can_admin_site', $_SESSION['site']['permissions']['admin'] ) ) {
    			header( 'Location: '.BASEURL.'');
    			exit;
    		}    		
    	} else {
    		$result			= $this->_WeTransfer_Admin->login( $_POST['username'], $_POST['password'] );
    		$json			= array();
    		$json['status']	= $result;
    		
			exit( json_encode( $json ) );    		
    	}    	
    	    	
		if( empty( $_POST ) ) {
			header('Location: '.BASEURL.'/admin');
			return;			
		}
		
		header('Content-Type: application/json');

		switch( $_POST['method'] ) {
			case 'admin-deleteFile':
				if( isset( $_POST['multiple'] ) AND $_POST['multiple'] ) {
					$this->_WeTransfer_Files->deleteMultipleFilesById( $_POST['files'] );
				
					$json			= array();
					$json['status']	= 'OK';
						
					exit( json_encode( $json ) );
				}
				
				if( strlen( $_POST['id'] ) ) {
					$this->_WeTransfer_Files->deleteAllFilesByParentId( $_POST['id'] );
						
					$json			= array();
					$json['status']	= 'OK';
				
					exit( json_encode( $json ) );
				}
								
				break;
				
			case 'dataTablesFilesOrphaned':				
				$json = $this->_WeTransfer_Files->fetchOrphanedFilesForDataTables( $_POST );
				if( !empty( $json ) ) {
					exit( json_encode( $json ) );					
				}								
									
				break;				
				
			case 'dataTablesFiles':				
				$json = $this->_WeTransfer_Files->fetchDataForDataTables( $_POST );
				if( !empty( $json ) ) {
					exit( json_encode( $json ) );					
				}								
									
				break;
				
			case 'dataTablesUsers':
				$json = $this->_WeTransfer_Users->fetchDataForDataTables( $_POST );
				if( !empty( $json ) ) {
					exit( json_encode( $json ) );
				}
					
				break;				
				
			case 'ban-user':
				$userId	= (int)$_POST['id'];
				$result = (int)$this->_WeTransfer_Users->banUserById( $userId );
			
				if( $result > 0 ) {
					$json				= array();
					$json['status']		= 'OK';
			
					exit( json_encode( $json ) );
				}
					
				break;
							
			case 'delete-user':
				$userId	= (int)$_POST['id'];
				$result = (int)$this->_WeTransfer_Users->deleteUserById( $userId );
				
				if( $result > 0 ) {
					$json				= array();
					$json['status']		= 'OK';
						
					exit( json_encode( $json ) );
				}
			
				break;
				
			case 'delete-usergroup':
				$id		= (int)$_POST['id'];
				$result = (int)$this->_WeTransfer_Usergroups->deleteUsergroupById( $id );
			
				if( $result > 0 ) {
					$json				= array();
					$json['status']		= 'OK';
			
					exit( json_encode( $json ) );
				}
					
				break;				
							
			case 'delete-media':		
				if( isset( $_POST['multiple'] ) AND $_POST['multiple'] ) {
					$this->_WeTransfer_User_Media->deleteMultipleMediaById( $_POST['media'] );
						
					$json				= array();
					$json['status']		= 'OK';
					$json['mediaCount']	= $this->_WeTransfer_User_Media->fetchTotalMediaCount();
					
					exit( json_encode( $json ) );					
				}
				
				if( strlen( $_POST['mediaId'] ) ) {
					$this->_WeTransfer_User_Media->deleteMediaById( $_POST['mediaId'] );
					
					$json				= array();
					$json['status']		= 'OK';
					$json['mediaCount']	= $this->_WeTransfer_User_Media->fetchTotalMediaCount();

					exit( json_encode( $json ) );
				}
				
				break;	
				
			default:
				$json			= array();
				$json['status'] = 'ERROR';
				$json['error']	= 'UNHANDLED_EXCEPTION';
				
				exit( json_encode( $json ) );				
		}
    }
        
// END OF THIS CLASS    
}