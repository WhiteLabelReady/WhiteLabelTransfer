<?php
/**
 * White Label Transfer
 * WeTransfer Usergroups Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Staturday, August 31, 2013, 23:03 GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Usergroups
{
	public $tableName;
	
	public function __construct()
	{
		$this->tableName = DB_TABLE_PREFIX.'usergroups';
	}
	
	public function fetchUsergroupPermissionsById( $id )
	{
		$data = array();
		
		$sql = "SELECT * FROM `".DB_TABLE_PREFIX."usergroup_permissions` ";
		$sql .= "WHERE `usergroup_id` = '".mysql_real_escape_string( (int)$id )."' ";
			
		$res = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
			
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
			
			return $data;
		} else {
			return array();
		}		
	}
	
	public function updateUsergroupPermissionsById( $id, $data = array() )
	{
		$id = (int)$id;
		
		if( empty( $data ) ) {
			$sql = "DELETE FROM `".DB_TABLE_PREFIX."usergroup_permissions` ";
			$sql .= "WHERE `usergroup_id` = '".mysql_real_escape_string( $id )."' ";
			
			$res = mysql_query( $sql ) OR die( mysql_error() );
			
			return true;
		}
		
		$existingPerms	= $this->fetchUsergroupPermissionsById( $id );
		$permIds		= array();
		$newPermIds 	= array();
		
		if( !empty( $existingPerms ) ) {
			// START:	fetch existing permissions			
			foreach( $existingPerms AS $key => $value ) {
				$permIds[] = $value['permission_id'];
			}
			// END:		fetch existing permissions

			// START:	fetch new permissions
			foreach( $data AS $key => $value ) {
				$newPermIds[] = $value;
			}
			// END:		fetch new permissions
			
			// compare arrays
			$diff = array_diff( $permIds, $newPermIds );
			
			// START:	remove perms
			if( !empty( $diff ) ) {
				foreach( $diff AS $key => $value ) {
					$sql = "DELETE FROM `".DB_TABLE_PREFIX."usergroup_permissions` ";
					$sql .= "WHERE `permission_id` = '".mysql_real_escape_string( (int)$value )."' ";
					$sql .= "AND `usergroup_id` = '".mysql_real_escape_string( $id )."' ";
					$sql .= "LIMIT 1";
						
					$res = mysql_query( $sql ) OR die( mysql_error() );					
				}
			}
			// END:		remove perms
		}
			
	
		foreach( $data AS $key => $value ) {		
			$sql = "INSERT IGNORE INTO `".DB_TABLE_PREFIX."usergroup_permissions` ( ";
			$sql .= "`usergroup_id`, `permission_id` ";
			$sql .= ") VALUES ( ";
			$sql .= "'".mysql_real_escape_string( $id )."', ";
			$sql .= "'".mysql_real_escape_string( (int)$value )."' ";
			$sql .= ") ";
			
			$res = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );			
		}
	
		return mysql_affected_rows();
	}	
	
	public function updateUsergroupById( $id, $data = array() )
	{
		if( empty( $data ) ) {
			return false;	
		}
		
		$count	= count( $data );
		$i		= 1;
		
		$sql    = "UPDATE `".DB_TABLE_PREFIX."usergroups` SET ";
		
		foreach( $data AS $key => $value ) {
			$sql .= "`".mysql_real_escape_string( $key )."` = '".mysql_real_escape_string( $value )."' ";
			
			if( $i < $count ) {
				$sql .= ", ";	
			}
			
			$i++;			
		}
		
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( (int)$id )."' ";
		$sql   .= "LIMIT 1 ";
	
		$res    = mysql_query( $sql ) OR die( mysql_error() );
	
		return mysql_affected_rows();
	}
		
	public function deleteUsergroupById( $id )
	{
		$sql    = "DELETE FROM `".mysql_real_escape_string( $this->tableName )."` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error()."\n".$sql );
		
		return mysql_affected_rows();		
	}
	
	public function fetchAllUsergroups( $orderBy = 'name' )
	{
		$sql = "SELECT * FROM `".mysql_real_escape_string( $this->tableName )."` ";
		$res = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = array();
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
	
			return $data;
	
		} else {
			return array();
		}
	}

	public function fetchUsergroupById( $id )
	{
		$sql    = "SELECT * FROM `".mysql_real_escape_string( $this->tableName )."` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );				
			return $data;
		} else {
			return array();
		}
	}	
		
	public function fetchUsergroupsByUserId( $userId )
	{
		$sql    = "SELECT `usergroup_id` FROM `".mysql_real_escape_string( $this->tableName )."` ";
		$sql   .= "WHERE `user_id` = '".mysql_real_escape_string( $userId )."' ";
		 
		$res    = mysql_query( $sql ) OR die( mysql_error() );
		 
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row['usergroup_id'];				
			}
			
			return $data;
			 		
		} else {
			return array();
		}		
	}

	public function setUsersUsergroupById( $userId, $usergroupId )
	{
		$sql    = "UPDATE `".DB_TABLE_PREFIX."usergroup_members` ";
		$sql   .= "SET `usergroup_id` = '".mysql_real_escape_string( (int)$usergroupId )."' ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( (int)$userId )."' ";
		$sql   .= "LIMIT 1 ";
		
		$res    = mysql_query( $sql ) OR die( mysql_error() );		
		
		return mysql_affected_rows();
	}	
}