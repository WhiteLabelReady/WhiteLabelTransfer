<?php
/**
 * White Label Transfer
 * WeTransfer Site Permissions Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Tuesday, September 24, 2013, 10:55 AM GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Site_Themes
{
	public function fetchThemesForDisplay( $orderBy = 'display_name' )
	{
		$sql	= "SELECT `name`, `display_name` ";
		$sql   .= "FROM `".DB_TABLE_PREFIX."site_themes` ";
		$sql   .= "WHERE `active` = '1' ";
		$sql   .= "ORDER BY `".mysql_real_escape_string( $orderBy )."` ";
		
		$res	= mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = array();
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[$row['name']] = $row['display_name'];
			}
	
			return $data;
	
		} else {
			return array();
		}
	}
		
	public function fetchActiveThemes()
	{
		$sql = "SELECT * FROM `".DB_TABLE_PREFIX."site_themes` ";		 
		$res = mysql_query( $sql ) OR die( mysql_error() );
		 
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;				
			}

			return $data;
			 		
		} else {
			return array();
		}		
	}

	public function fetchAllThemes()
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."site_themes` ";
		$sql   .= "WHERE `active` = '1' ";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
			
			return $data;
				
		} else {
			return array();
		}
	}	
}