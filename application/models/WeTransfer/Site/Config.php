<?php
/**
 * White Label Transfer
 * WeTransfer Site Config Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Tuesday, October 08, 2013, 04:39 PM GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Site_Config
{
	// START OF THIS CLASS	

	/**
	 * fetch site configuration from the DB
	 *
	 * @return  array
	 */
	public function fetchSiteConfig( $orderBy = 'name', $sortOrder = 'ASC' )
	{
		$data   = array();
	
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."site_config` ";
		$sql   .= "ORDER BY ".mysql_real_escape_string( $orderBy )." ";
		$sql   .= mysql_real_escape_string( $sortOrder );
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				if( $row['name'] != 'site_version' ) {
					$data[] = $row;					
				}
			}
		}		
	
		return $data;
	}
	
	public function fetchSiteConfigCategories( $orderBy = 'category', $sortOrder = 'ASC' )
	{
		$data   = array();
	
		$sql    = "SELECT DISTINCT `category` FROM `".DB_TABLE_PREFIX."site_config` ";
		$sql   .= "ORDER BY ".mysql_real_escape_string( $orderBy )." ";
		$sql   .= mysql_real_escape_string( $sortOrder );
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
	
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
					$data[] = $row['category'];
			}
		}
	
		return $data;
	}	
	
	/**
	 * define site configuration
	 */
	public function defineSiteConfig()
	{
		$config = $this->fetchSiteConfig();
	
		if( !empty( $config ) ) {
			foreach( $config AS $key => $value ) {
				define( strtoupper( $value['name'] ), $value['value'] );
			}
		}
	}

	public function updateSiteConfig( $config )
	{
		if( empty( $config ) ) {
			return false;
		}
	
		foreach( $config AS $key => $value ) {
			$sql  = "UPDATE `".DB_TABLE_PREFIX."site_config` SET ";
			$sql .= "`value` = '".mysql_real_escape_string( $value )."' ";
			$sql .= "WHERE `name` = '".mysql_real_escape_string( $key )."' ";
			$sql .= "LIMIT 1 ";
				
			$res = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		}
	
		return true;
	}	
	
    // END OF THIS CLASS
}