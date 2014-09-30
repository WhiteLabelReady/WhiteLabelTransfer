<?php
/**
 * White Label Transfer
 * WeTransfer Site Permissions Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Staturday, August 31, 2013, 22:54 GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Site_Permissions
{
	public function fetchSitePermissionsByUserId( $userId )
	{
		$sql    = "SELECT `usergroup_id` FROM `".DB_TABLE_PREFIX."usergroup_members` ";
		$sql   .= "WHERE `user_id` = '".mysql_real_escape_string( $userId )."' ";
		 
		$res    = mysql_query( $sql ) OR die( mysql_error() );
		 
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row['usergroup_id'];				
			}
			
			if( !empty( $data ) ) {
				return $this->fetchUsergroupPermissionsByUsergroupId( $data );				
			}
			 		
		} else {
			return array();
		}		
	}
	
	public function fetchAllSitePermissions()
	{
		$data = array();
		
		$sql = "SELECT * FROM `".DB_TABLE_PREFIX."site_permissions` ";			
		$res = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
		} 
		
		return $data;
	}	

	public function fetchUsergroupPermissionsByUsergroupId( $usergroupId = array() )
	{
		if( !empty( $usergroupId ) ) {
			$groups	= implode ( ',', $usergroupId );			
		}
					
		$sql    = "SELECT `permission_id` FROM `".DB_TABLE_PREFIX."usergroup_permissions` ";
		$sql   .= "WHERE `usergroup_id` IN (".mysql_real_escape_string( $groups )."); ";
			
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
			
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row['permission_id'];
			}
						
			if( !empty( $data ) ) {
				$perms	=  implode ( ',', $data );
 				$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."site_permissions` ";
 				$sql	.= "WHERE `id` IN (".mysql_real_escape_string( $perms )."); ";

 				$res	= mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
 				
 				if( mysql_num_rows( $res ) > 0 ) {
 					$sitePerms = array();
 					while( $row = mysql_fetch_assoc( $res ) ) {
 						$sitePerms[$row['permission_type']][] = $row['permission_name'];
 					}
 					 					
 					return $sitePerms;
 				} 				
			}	
		} else {
			return array();
		}
	}	
}