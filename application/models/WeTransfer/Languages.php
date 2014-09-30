<?php
/**
 * White Label Transfer
 * WeTransfer Languages
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Tuesday, November 27, 2012, 08:00 AM GMT+1 mknox
 * @edited      $Date: 2011-09-02 20:15:11 +0200 (Fri, 02 Sep 2011) $ $Author: mknox $
 * @version     $Id: Account.php 4115 2011-09-02 18:15:11Z mknox $
 */

class WeTransfer_Languages
{
    /**
     * fetch language ID via locale string
     *
     * @param   string  $locale
     * @return  int
    */
    public function fetchLanguageIdByLocale( $locale )
    {
        $sql    = "SELECT `id` FROM `".DB_TABLE_PREFIX."languages` ";
        $sql   .= "WHERE `iso_639` = '".mysql_real_escape_string( $locale )."' ";
        $sql   .= "LIMIT 1 ";

        $res    = mysql_query( $sql ) OR die( mysql_error() );

        if( mysql_num_rows( $res ) > 0 ) {
            $data = mysql_fetch_assoc( $res );
            return $data['id'];
        }
    }
    
    public function languageExistsById( $id )
    {
    	$sql    = "SELECT `id` FROM `".DB_TABLE_PREFIX."languages` ";
    	$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
    	$sql   .= "LIMIT 1 ";
    	
    	$res    = mysql_query( $sql ) OR die( mysql_error() );
    	
    	if( mysql_num_rows( $res ) > 0 ) {
			return true;
    	}    	
    }
    
    public function fetchFriendlyNameById( $id )
    {
    	$sql    = "SELECT `friendly_name` FROM `".DB_TABLE_PREFIX."languages` ";
    	$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
    	$sql   .= "LIMIT 1 ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error() );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		$data = mysql_fetch_assoc( $res );
    		return $data['friendly_name'];
    	}
    }
        
    public function fetchLanguageIdByIso31661( $locale )
    {
    	$sql    = "SELECT `id` FROM `".DB_TABLE_PREFIX."languages` ";
    	$sql   .= "WHERE `iso_3166_1` = '".mysql_real_escape_string( $locale )."' ";
    	$sql   .= "LIMIT 1 ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error() );
    
    	if( mysql_num_rows( $res ) > 0 ) {
    		$data = mysql_fetch_assoc( $res );
    		return $data['id'];
    	}
    }    

    /**
     * fetch all phrases by language ID
     *
     * @param   int     $locale
     * @return  array
    */
    public function fetchPhrasesByLanguageId( $id )
    {
        $data   = array();

        $sql    = "SELECT `name`, `text` FROM `".DB_TABLE_PREFIX."phrases` ";
        $sql   .= "WHERE `language_id` = '".mysql_real_escape_string( $id )."' ";

        $res    = mysql_query( $sql ) OR die( mysql_error() );

        if( mysql_num_rows( $res ) > 0 ) {
            while( $row = mysql_fetch_assoc( $res ) ) {
                $data[ $row['name'] ] = $row['text'];
            }

            return $data;
        }
    }
}