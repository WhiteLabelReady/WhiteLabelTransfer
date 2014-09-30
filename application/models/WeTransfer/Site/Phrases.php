<?php
/**
 * White Label Transfer
 * WeTransfer Site Phrases Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Tuesday, October 08, 2013, 04:55 PM GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Site_Phrases
{
	public function fetchPhraseFromSession( $phrase )
	{	
		if( !isset( $_SESSION['site']['phrases'][$phrase] ) ) {
			return $phrase;	
		}	
		
		return $_SESSION['site']['phrases'][$phrase];
	}
	
	public function fetchAllPhrases( $orderBy = 'name', $sortOrder = 'ASC' )
	{
		$data	= array();
		
		$sql	= "SELECT * FROM `".DB_TABLE_PREFIX."phrases` ";
		$sql   .= "ORDER BY ".mysql_real_escape_string( $orderBy )." ";
		$sql   .= mysql_real_escape_string( $sortOrder );
		
		$res	= mysql_query( $sql ) OR die( mysql_error() );
		
		if( mysql_num_rows( $res ) > 0 ) {			
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}	
		}
		
		return $data;
	}
	 
	public function updateSitePhrases( $phrases )
	{
		if( empty( $phrases ) ) {
			return false;	
		}
						
		foreach( $phrases AS $key => $value ) {
			$sql  = "UPDATE `".DB_TABLE_PREFIX."phrases` SET ";			
			$sql .= "`text` = '".mysql_real_escape_string( $value )."' ";
			$sql .= "WHERE `id` = '".mysql_real_escape_string( $key )."' ";
			$sql .= "LIMIT 1 ";
			
			$res = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );			
		}		

		return true;
	}	
    // END OF THIS CLASS
}