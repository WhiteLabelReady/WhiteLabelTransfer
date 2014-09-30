<?php
/**
 * White Label Transfer
 * WeTransfer Admin Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Thursday, November 21, 2013, 14:54 GMT+1 mknox
 * @edited      $Date: 2013-10-19 20:27:38 +0200 (Sa, 19 Okt 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: Users.php 97 2013-10-19 18:27:38Z dev@whitelabeltransfer.com $
 */

class WeTransfer_Admin
{
	public $tableName;
	private $columnNames;
	protected $_WeTransfer_Languages;
	protected $_WeTransfer_Usergroups;
	protected $_WeTransfer_Site_Permissions;
	
	public function __construct()
	{
		$this->tableName					= DB_TABLE_PREFIX.'users';
		$this->_WeTransfer_Languages		= new WeTransfer_Languages;
		$this->_WeTransfer_Usergroups		= new WeTransfer_Usergroups;
		$this->_WeTransfer_Site_Permissions	= new WeTransfer_Site_Permissions;		
	}
	
	public function removeOwnTempAvatar()
	{
		$files = glob( BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/avatar-wideImageTemp.*' );
		
		if( !empty( $files ) ) {
			foreach( $files AS $filename ) {
				@unlink( $filename );
			}			
		}

		$files = glob( BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/avatar-temp.*' );
		
		if( !empty( $files ) ) {
			foreach( $files AS $filename ) {
				@unlink( $filename );
			}
		}		
		
		return true;
	}	
	
	public function changeOwnAvatar()
	{
		$dir	= BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'];
		$files	= glob( $dir.'/avatar-temp.*' );
				
		if( !empty( $files ) ) {
			$fileExt = fetchFileExt( $files[0] );
			rename( $files[0], $dir.'/avatar.'.$fileExt );

			if( file_exists( $dir.'/avatar.'.$fileExt ) ) {
				$url = PROTOCOL_RELATIVE_URL.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/avatar.'.$fileExt;
				$this->updateUserById( $_SESSION['user']['id'], array( 'avatar_url' => $url ) );
				$this->removeOwnTempAvatar();
				
				return array('status' => 'OK', 'url' => $url );
			}
		}
	
		return false;
	}	
	
	public function uploadOwnAvatar() 
	{		
		if( !empty( $_FILES ) ) {
			if( $_FILES['myNewAvatar']['error'] == UPLOAD_ERR_OK ) {
				if( !is_image( $_FILES['myNewAvatar']['tmp_name'] ) ) {
					return array( 'status' => 'error', 'error' => 'NOT_IMAGE' );
				}
				
				$fileExt			= strtolower( fetchFileExt( $_FILES['myNewAvatar']['name'] ) );
				$wideImage			= 'avatar-wideImageTemp.'.$fileExt;
				$filename			= 'avatar-temp.'.$fileExt;
				$destinationTemp	= BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/'.$wideImage;				
				$destination		= BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/'.$filename;
				
				$moved = move_uploaded_file( $_FILES['myNewAvatar']['tmp_name'], $destinationTemp );
				
				if( $moved ) {
					if( $fileExt != 'gif' ) {
						require_once('WideImage/WideImage.php');						
						WideImage::load( $destinationTemp )->resize( SITE_AVATAR_WIDTH, SITE_AVATAR_HEIGHT, 'inside' )->saveToFile( $destination );												
					} else {
						rename( $destinationTemp, $destination );	
					}
					
					unlink( $destinationTemp );
					
					return array('status' => 'OK',
								 'url' => BASEURL.'/'.SITE_UPLOAD_DIR_USERS.'/'.$_SESSION['user']['id'].'/'.$filename 
					);						
				} else {
					return array( 'status' => 'error', 'error' => 'UPLOAD', 'error_code' => 'UPLOAD_FAILED' );
				}				
			}			
		} else {
			return array( 'status' => 'error', 'error' => 'NO_FILE_UPLOADED', 'error_code' => $_FILES['myNewAvatar']['error'] );
		}
	}
	
	public function fetchColumnNames( $tableName )
	{
		return fetchColumnNames( $tableName );		
	}	
	
	public function banUserById( $userId )
	{
		$sql    = "UPDATE `".mysql_real_escape_string( $this->tableName )."` ";
		$sql   .= "SET `site_status` = 'banned' ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( (int)$userId )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		return $this->_WeTransfer_Usergroups->setUsersUsergroupById( $userId, 0 );
	}
	
	public function deleteUserById( $userId )
	{
		$userId = (int)$userId;
		if( $userId == 0 ) {
			return;	
		}
		
		$media = $this->_WeTransfer_User_Media->fetchAllMediaIdsByUserId( $userId );
		
		if( !empty( $media ) ) {
			$this->_WeTransfer_User_Media->deleteMultipleMediaById( $media );			
		}
		
		@delTree( BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$userId );
				
		$sql    = "DELETE FROM `".mysql_real_escape_string( $this->tableName )."` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $userId )."' ";
		$sql   .= "LIMIT 1 ";
	
		$res    = mysql_query( $sql ) OR die( mysql_error()."\n".$sql );
	
		return mysql_affected_rows();		
	}
	
	public function fetchAllUsers( $columns = '*', $offset = 0, $limit = 20, $orderBy = 'username', $sortOrder = 'ASC' )
	{
		$data		= array();		
		$columns	= ( is_array( $columns ) ) ? implode(',', $columns ) : '*';
		
		$sql    = "SELECT ".mysql_real_escape_string( $columns )." FROM `".DB_TABLE_PREFIX."users` ";
		$sql   .= "ORDER BY ".mysql_real_escape_string( $orderBy )." ".mysql_real_escape_string( $sortOrder ) ." ";
		$sql   .= "LIMIT ".mysql_real_escape_string( (int)$offset ).", ".mysql_real_escape_string( (int)$limit );
		
		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {							
				$data[] = $row;
			}
		}
		
		return $data;		
	}	
	
	private function confirmAccountById( $userId )
	{
		$sql    = "UPDATE `".DB_TABLE_PREFIX."users` ";
		$sql   .= "SET `site_status` = 'email_confirmed' ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $userId )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_affected_rows() > 0 ) {
			return true;
		}
		
		return false;		
	}
	
	public function addUserToUsergroup( $userId, $usergroupId )
	{
		$sql	= "INSERT INTO  `".DB_TABLE_PREFIX."usergroup_members` ";
		$sql   .= "( ";
		$sql   .= "`user_id`, ";
		$sql   .= "`usergroup_id` ";
		$sql   .= ") VALUES ( ";
		$sql   .= "'".mysql_real_escape_string( $userId )."', ";
		$sql   .= "'".mysql_real_escape_string( $usergroupId )."' ";
		$sql   .= "); ";

		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	}
	
	public function confirmAccountByCode( $code )
	{
		$sql	= "SELECT * FROM `".DB_TABLE_PREFIX."user_confirm` ";
		$sql   .= "WHERE `code` = '".mysql_real_escape_string( trim( $code ) )."' ";
		$sql   .= "LIMIT 1";
		
		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );
			
			$this->addUserToUsergroup( $data['user_id'], SITE_DEFAULT_USERGROUP );
			$this->deleteConfirmCodeById( $data['id'] );
			return $this->confirmAccountById( $data['user_id'] );
		}
		
		return false;		
	}
	
	public function deleteConfirmCodeById( $id )
	{
		$sql    = "DELETE FROM `".DB_TABLE_PREFIX."user_confirm` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_affected_rows() > 0 ) {
			return true;
		}
		
		return false;		
	}
	
	public function createUser( $data = array() )
	{
		if( empty( $data ) ) {
			return false;
		}
		
		$this->columnNames = $this->fetchColumnNames( $this->tableName );
		
		// START:	filter input
		foreach( $data AS $key => $value ) {
			if( !in_array( $key, $this->columnNames ) ) {
				unset( $data[$key] );	
			}	
		}
		// END:		filter input
		
		if( !strlen( trim( $data['email'] ) ) ) {
			return 'EMAIL_NOT_SPECIFIED';	
		} 
		
		if( $this->emailExists( $data['email'] ) ) {
			return 'EMAIL_EXISTS';			
		}
		
		if( $this->usernameExists( $data['username'] ) ) {
			return 'USERNAME_EXISTS';
		}		
		
		// START:	set user status
		if( (int)SITE_MODERATE_NEW_USERS == 1 ) {
			$data['site_status'] = 'pending';
		} else if( (int)SITE_REQUIRE_EMAIL_CONFIRM == 1 ) {
			$data['site_status'] = 'unconfirmed';
		} else {
			$data['site_status'] = 'auto_confirmed';
		}		
		// END:		set user status
		
		$data['date_created'] = time();
		
		// START:	hash password
		if( isset( $data['password'] ) ) {
			$data['password'] = sha1( $data['password'] );			
		}		
		// END:		hash password

		// START:	insert user into DB
		$count	= count( $data );
		$i		= 0;
		 
		$sql	= "INSERT INTO `".DB_TABLE_PREFIX."users` ( ";
		foreach( $data AS $key => $value ) {
			$i++;
			$comma = ( $i < $count ) ? ', ' : ' ';
			$key = mysql_real_escape_string( $key );
			$sql .= "`".mysql_real_escape_string( $key )."` ".$comma;
		}
		$sql .= ") VALUES ( ";
		
		$i = 0;
		foreach( $data AS $key => $value ) {
			$i++;
			$comma = ( $i < $count ) ? ', ' : ' ';
			$value = mysql_real_escape_string( $value );
			$sql .= "'".mysql_real_escape_string( $value )."' ".$comma;
		}
		 
		$sql   .= ");";		
		$res    = mysql_query( $sql ) OR die( mysql_error()."\nSQL:  ".$sql );
		
		$userId	= mysql_insert_id();
		// END:		insert user into DB
		
		// START:	notify moderator, if required
		if( (int)SITE_MODERATE_NEW_USERS == 1 ) {
			// send e-mail to site admin
		}
		// END:		notify moderator, if required
		
		// START:	send e-mail, if required
		if( (int)SITE_REQUIRE_EMAIL_CONFIRM == 1 ) {
			// send e-mail to user
			$code = randomString( 60 );
			$this->addEmailConfirmCode( $userId, $code );
			$this->sendNewRegistrationEmail( $userId, $code );
		}
		// END:		send e-mail, if required
		
		return 'OK';		
	}
	
	public function resetOwnPassword( $username, $recaptchaChallenge, $recaptchaResponse )
	{
		require_once('recaptcha-php/recaptchalib.php');
		
		$response = recaptcha_check_answer( SITE_RECAPTCHA_PRIVATE_KEY,
										$_SERVER['REMOTE_ADDR'],
										$recaptchaChallenge,
										$recaptchaResponse
		);
		
		if ( !$response->is_valid ) {
			return $response->error;
		}
				
		if( $this->usernameExists( $username ) ) {
			$this->sendPasswordResetEmail( $username );
			return 'OK';	
		}	

		return 'USER_404';
	}
	
	protected function sendNewRegistrationEmail( $userId, $code )
	{
		require_once('PHPMailer/PHPMailerAutoload.php');
	
		$mail = new PHPMailer;
		$user = $this->fetchUserDetailsById( $userId );
	
		$mail->From		= SITE_EMAIL_ADDRESS;
		$mail->FromName = SITE_NAME;
		$mail->addAddress( $user['email'], $user['first_name'].' '.$user['last_name'] );
		$mail->addReplyTo( SITE_EMAIL_ADDRESS, SITE_NAME );
	
		$mail->WordWrap = 50;
		$mail->isHTML( true );
	
		$mail->Subject	= '['.SITE_NAME.'] Your New Account ('.$user['username'].')';
	
		$body			= 'Hello '.$user['first_name'].' '.$user['last_name'].', ';
		$body    	   .= '<br><br>Please confirm your new account by following this URL:  ';
		$body    	   .= BASEURL.'/accounts/confirm/'.$code;
	
		$mail->Body		= $body;
	
		$body			= "Hello ".$user['first_name']." ".$user['last_name'].", ";
		$body    	   .= "\r\n\r\nPlease confirm your new account by following this URL:  ";
		$body    	   .= BASEURL."/accounts/confirm/".$code;
	
		$mail->AltBody = $body;
	
		if( !$mail->send() ) {
			return $mail->ErrorInfo;
		}
	
		return true;
	}
		
	private function sendPasswordResetEmail( $username )
	{
		require_once('PHPMailer/PHPMailerAutoload.php');
		
		$mail = new PHPMailer;
		$user = $this->fetchUserDetailsByUsername( $username );
		
		$code = randomString( 60 );
		$this->addPasswordResetCode( $user['id'], $code );
		
		$mail->From		= SITE_EMAIL_ADDRESS;
		$mail->FromName = SITE_NAME;
		$mail->addAddress( $user['email'], $user['first_name'].' '.$user['last_name'] );
		$mail->addReplyTo( SITE_EMAIL_ADDRESS, SITE_NAME );
		
		$mail->WordWrap = 50;
		$mail->isHTML( true );
		
		$mail->Subject = '['.SITE_NAME.'] Password Reset';
		
		$body			= 'Hello '.$user['first_name'].' '.$user['last_name'].', ';
		$body    	   .= '<br><br>A request was made to reset your password. ';
		$body    	   .= '<br><br>Please click here if you wish to proceed:  ';
		$body    	   .= BASEURL.'/accounts/password/email-verify/'.$code;
		
		$mail->Body		= $body;
		
		$body			= "Hello ".$user['first_name']." ".$user['last_name'].", ";
		$body    	   .= "\r\n\r\nA request was made to reset your password. ";
		$body    	   .= "\r\n\r\nPlease click here if you wish to proceed:  ";
		$body    	   .= BASEURL."/accounts/password/email-verify/".$code;
				
		$mail->AltBody = $body;
		
		if( !$mail->send() ) {
			return $mail->ErrorInfo;
		}
		
		return true;		
	}
	
	public function sendNewPasswordEmail( $userId, $newPassword )
	{
		require_once('PHPMailer/PHPMailerAutoload.php');
	
		$mail = new PHPMailer;
		$user = $this->fetchUserDetailsById( $userId );
	
		$mail->From		= SITE_EMAIL_ADDRESS;
		$mail->FromName = SITE_NAME;
		$mail->addAddress( $user['email'], $user['first_name'].' '.$user['last_name'] );
		$mail->addReplyTo( SITE_EMAIL_ADDRESS, SITE_NAME );
	
		$mail->WordWrap = 50;
		$mail->isHTML( true );
	
		$mail->Subject = '['.SITE_NAME.'] Password Change';
	
		$body			= 'Hello '.$user['first_name'].' '.$user['last_name'].', ';
		$body    	   .= '<br><br>Per your request, your password has been changed to: '.$newPassword;
	
		$mail->Body		= $body;
	
		$body			= "Hello ".$user['first_name']." ".$user['last_name'].", ";
		$body    	   .= "\r\n\r\nPer your request, your password has been changed to: ".$newPassword;
	
		$mail->AltBody = $body;
	
		if( !$mail->send() ) {
			return $mail->ErrorInfo;
		}
	
		return true;
	}

	private function addEmailConfirmCode( $userId, $code )
	{
		$sql	= "INSERT IGNORE INTO `".DB_TABLE_PREFIX."user_confirm` ( ";
		$sql   .= "`user_id` , `code` , `date` ";
		$sql   .= ") VALUES ( ";
		$sql   .= "'".mysql_real_escape_string( $userId )."', ";
		$sql   .= "'".mysql_real_escape_string( $code )."', ";
		$sql   .= "'".mysql_real_escape_string( time() )."' ";
		$sql   .= ") ";
	
		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	
		if( mysql_affected_rows() > 0 ) {
			return true;
		}
	
		return false;
	}
		
	private function addPasswordResetCode( $userId, $code )
	{
		$sql	= "INSERT IGNORE INTO `".DB_TABLE_PREFIX."password_reset` ( ";
		$sql   .= "`user_id` , `code` , `date` ";
		$sql   .= ") VALUES ( ";
		$sql   .= "'".mysql_real_escape_string( $userId )."', ";
		$sql   .= "'".mysql_real_escape_string( $code )."', ";
		$sql   .= "'".mysql_real_escape_string( time() )."' ";
		$sql   .= ") ";

		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_affected_rows() > 0 ) {
			return true;
		}
		
		return false;		
	}
	
	public function resetPasswordByResetCode( $code )
	{
		$sql	= "SELECT * FROM `".DB_TABLE_PREFIX."password_reset` ";
		$sql   .= "WHERE `code` = '".mysql_real_escape_string( trim( $code ) )."' ";
		$sql   .= "LIMIT 1";
	
		$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	
		if( mysql_num_rows( $res ) > 0 ) {
			require_once( 'randlib.class.php' );
			$data = mysql_fetch_assoc( $res );
			$this->deletePasswordResetCodeById( $data['id'] );
			return $this->changePasswordById( $data['user_id'], random::generateRandomString() );
		}
	
		return false;
	}

	public function deletePasswordResetCodeById( $id )
	{
		$sql    = "DELETE FROM `".DB_TABLE_PREFIX."password_reset` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1 ";
	
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	
		if( mysql_affected_rows() > 0 ) {
			return true;
		}
	
		return false;
	}	
	
	public function changePasswordById( $id, $newPassword, $notifyUser = true )
	{
		$sql    = "UPDATE `".DB_TABLE_PREFIX."users` ";
		$sql   .= "SET `password` = '".mysql_real_escape_string( sha1( $newPassword ) )."' ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1 ";
	
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );	
	
		if( mysql_affected_rows() > 0 ) {
			if( $notifyUser ) {
				$this->sendNewPasswordEmail( $id, $newPassword );				
			}
			
			return true;
		}
	
		return false;
	}
		
	public function changeOwnPassword( $password, $newPassword )
	{
		$sql    = "UPDATE `".DB_TABLE_PREFIX."users` ";		
		$sql   .= "SET `password` = '".mysql_real_escape_string( sha1( $newPassword ) )."' ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $_SESSION['user']['id'] )."' ";
		$sql   .= "AND `password` = '".mysql_real_escape_string( sha1( $password ) )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		
		if( mysql_affected_rows() > 0 ) {
			return true;	
		}
		
		return false;		
	}	
	
    /**
     * User Login
     *
     * @param   string  $username
     * @param	string	$password
     * @return  string
    */
    public function login( $username, $password )
    {
    	if( !$this->usernameExists( $username ) ) {
    		return 'LOGIN_USERNAME_DOES_NOT_EXIST';	
    	} 
    		    	
        $sql    = "SELECT * FROM `".$this->tableName."` ";
        $sql   .= "WHERE `username` = '".mysql_real_escape_string( $username )."' ";
        $sql   .= "AND `password` = '".mysql_real_escape_string( $password )."' ";        
        $sql   .= "OR `email` = '".mysql_real_escape_string( $username )."' ";
        $sql   .= "AND `password` = '".mysql_real_escape_string( $password )."' ";
        $sql   .= "LIMIT 1 ";       

        $res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );

        if( mysql_num_rows( $res ) > 0 ) {
            $data = mysql_fetch_assoc( $res );            
            
            $_SESSION['user'] = array();
            
            foreach( $data AS $key => $value )  {
            	$_SESSION['user'][$key] = $value;	
            }
            
            $_SESSION['site']['permissions'] = $this->fetchSitePermissionsByUserId( $data['id'] );
            
            if( empty( $_SESSION['site']['permissions'] ) ) {
            	return 'NO_SITE_PERMISSIONS';	
            }
            
            if( !in_array( 'can_view_site', @$_SESSION['site']['permissions']['site'] ) ) {
            	return 'NO_SITE_VIEW';
            }          
            
            $_SESSION['user']['logged_in'] = true;
			if( !strlen( trim(  $_SESSION['user']['avatar_url'] ) ) ) {
            	 $_SESSION['user']['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
            } elseif ( !urlExists(  $_SESSION['user']['avatar_url'] ) ) {
            	 $_SESSION['user']['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
            }
            $_SESSION['user']['profile_url']	= BASEURL.'/'.$_SESSION['user']['username'];
            $_SESSION['user']['full_name']		= $_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name'];
            
            // remove the user's password from the session
            unset( $_SESSION['user']['password'] );
            
            return 'LOGIN_OK';
        } else {
       		return 'LOGIN_INVALID_PASSWORD'; 	
        }      
    }
    
    /**
     * Fetch User Data via User ID
     *
     * @param   string  $userId
     * @return  array
     */
    public function fetchUserDetailsById( $userId )
    {
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."users` ";
    	$sql   .= "WHERE `id` = '".mysql_real_escape_string( $userId )."' ";
    	$sql   .= "LIMIT 1 ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		$data = mysql_fetch_assoc( $res );  
    		
    		if( !strlen( trim( $data['avatar_url'] ) ) ) {
    			$data['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
    		} elseif ( !urlExists( $data['avatar_url'] ) ) {
    			$data['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
    		}    		
    		
    		return $data;
    	} else {
    		return array();
    	}
    } 
    
    /**
     * Fetch User Data via Username
     *
     * @param   string  $username
     * @return  array
     */
    public function fetchUserDetailsByUsername( $username )
    {    		
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."users` ";
    	$sql   .= "WHERE `username` = '".mysql_real_escape_string( $username )."' ";
    	$sql   .= "OR `email` = '".mysql_real_escape_string( $username )."' ";
    	$sql   .= "LIMIT 1";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		$now	= time();
    		$data	= mysql_fetch_assoc( $res );
    		
			if( !strlen( trim( $data['avatar_url'] ) ) ) {
    			$data['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
    		} elseif ( !urlExists( $data['avatar_url'] ) ) {
    			$data['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
    		} 
    		
    		$updateData = array( 'last_active' => $now );    	
    		$this->updateUserById( $data['id'], $updateData );    		
    		    		
    		return $data;
    	} else {
    		return array();	
    	}
    }

    /**
     * Fetch Username via User ID
     *
     * @param   string  $userId
     * @return  string
     */
    public function fetchUsernameById( $userId )
    {
    	$sql    = "SELECT `username` FROM `".DB_TABLE_PREFIX."users` ";
    	$sql   .= "WHERE `id` = '".mysql_real_escape_string( $userId )."' ";
    	$sql   .= "LIMIT 1 ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		$data = mysql_fetch_assoc( $res );
    		return $data['username'];
    	}
    }    

    /**
     * Determine if a username exists or not
     *
     * @param   string	$username
     * @return  boolean	
    */
    public function usernameExists( $username )
    {
        $sql    = "SELECT * FROM `".$this->tableName."` ";
        $sql   .= "WHERE `email` = '".mysql_real_escape_string( $username )."' ";
        $sql   .= "OR `username` = '".mysql_real_escape_string( $username )."' ";
        $sql   .= "LIMIT 1 ";

        $res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );

        if( mysql_num_rows( $res ) > 0 ) { 	
            return true;
        }
        
        return false;
    }
    
    /**
     * Determine if an e-mail address exists or not
     *
     * @param   string	$email
     * @return  boolean
     */
    public function emailExists( $email )
    {
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."users` ";
    	$sql   .= "WHERE `email` = '".mysql_real_escape_string( $email )."' ";
    	$sql   .= "LIMIT 1 ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		return true;
    	}
    
    	return false;
    }    
    
    /**
     * Update a user's session
     */    
    public function updateUserSession()
    {
		if( isset( $_SESSION['user']['id'] ) ) {
			$data = $this->fetchUserDetailsById( $_SESSION['user']['id'] );
			if( empty( $data ) ) {
				
				session_destroy();
				setcookie('PHPSESSID', '', time() - 3600 );
				
				return;	
			}
			
			$newData					= array();
			$newData['last_active'] 	= time();
			$newData['last_ip'] 		= $_SERVER['REMOTE_ADDR'];
			
			$this->updateUserById( $_SESSION['user']['id'], $newData );
			            
            foreach( $data AS $key => $value )  {
            	$_SESSION['user'][$key] = $value;	
            }
            
            $_SESSION['site']['permissions']	= $this->fetchSitePermissionsByUserId( $data['id'] );            
            $_SESSION['user']['logged_in']		= true;
            $_SESSION['user']['avatar_url'] 	= ( strlen( $_SESSION['user']['avatar_url'] ) ) ? $_SESSION['user']['avatar_url'] : SITE_DEFAULT_AVATAR_URL;
            
            if( !strlen( trim(  $_SESSION['user']['avatar_url'] ) ) ) {
            	 $_SESSION['user']['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
            } elseif ( !urlExists(  $_SESSION['user']['avatar_url'] ) ) {
            	 $_SESSION['user']['avatar_url'] = SITE_DEFAULT_AVATAR_URL;
            }            
                        
            $_SESSION['user']['profile_url'] 	= BASEURL.'/'.$_SESSION['user']['username'];
            $_SESSION['user']['full_name'] 		= $_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name'];
            
            $_SESSION['SITE_DEBUG'] 			= true;
            
            // remove the user's password from the session
            unset( $_SESSION['user']['password'] );			
		} else {
			$_SESSION['user']['logged_in']	= false;
			$_SESSION['user']['username']	= 'Guest';
			$_SESSION['user']['avatar_url']	= SITE_DEFAULT_AVATAR_URL;
		}   	
    }
    
    public function updateUserById($id, $data)
    {
    	if( !empty( $data ) ) {
    		$count	= count( $data );
    		
	        $sql    = "UPDATE `".DB_TABLE_PREFIX."users` ";
	        $sql   .= "SET ";
	        $i		= 0;
	        foreach( $data AS $key => $value ) {
				$i++;	        	
	        	if( !is_numeric( $key ) ) {
	        		$sql .= "`".mysql_real_escape_string( $key )."` = '".mysql_real_escape_string( $value )."' ";
	        		if( $i < $count ) {
	        			$sql .= ", ";	
	        		}	
	        	}	        	
	        }
	        
	        $sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
	        $sql   .= "LIMIT 1 ";  

	        $res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    	} 
    }  

    public function fetchSitePermissionsByUserId( $userId )
    {
    	return $this->_WeTransfer_Site_Permissions->fetchSitePermissionsByUserId( $userId );    	
    }
    
    public function changeUserLangByLangId( $langId )
    {
    	// @TODO:	verify that language exists
    	$langId = (int)$langId;
    	
    	if( !$this->_WeTransfer_Languages->languageExistsById( $langId ) ) {
    		return false;	
    	}
    	
    	$_SESSION['user']['lang_override']		= true;
		$_SESSION['user']['selected_lang_id']	= $langId;
		$_SESSION['user']['site_language']		= $langId;
		
		if( $_SESSION['user']['logged_in'] ) {
			$this->updateUserById( $_SESSION['user']['id'], array( 'site_language' => $langId ) );			
		}
		
		return true;
    }
}