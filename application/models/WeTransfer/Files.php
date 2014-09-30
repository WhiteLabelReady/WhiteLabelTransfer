<?php
/**
 * White Label Transfer
 * Files Model
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Monday, December 02, 2013, 18:25 GMT+1
 * @modified    $Date: 2011-11-16 18:27:16 +0100 (Wed, 16 Nov 2011) $ $Author: mknox $
 * @version     $Id: IndexController.php 5139 2011-11-16 17:27:16Z mknox $
 *
 * @category    Models
 * @package     White Label Transfer
*/

class WeTransfer_Files
{
	protected $_WeTransfer_Users;
	
	public function __construct() {}	

	public function logDownload( $uploadId, $recipientId )
	{
		$sql = "INSERT INTO `".DB_TABLE_PREFIX."logs_download` ";
		$sql .= " (`upload_id`, `recipient_id`, `date`, `ip` ";
		$sql .= " ) VALUES ( ";
		$sql .= "'".mysql_real_escape_string( $uploadId )."', ";
		$sql .= "'".mysql_real_escape_string( $recipientId )."', ";
		$sql .= "'".mysql_real_escape_string( time() )."', ";
		$sql .= "'".mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] )."' ";		
		$sql .= "); ";
			
		$res = mysql_query( $sql ) OR die( mysql_error() );		
	}
	
	public function fetchOrphanedFilesForDataTables( $params )
	{
		if( empty( $params ) ) {
			return array();
		}
		
		extract( $params );
		$this->_WeTransfer_Users = new WeTransfer_Users;
		
		// START:	fetch file list
		$rootDir	= BASEDIR.'/'.SITE_UPLOAD_DIR_USERS;
		$userDirs	= glob( $rootDir.'/*' );
		// END:		fetch file list
		
		if( !empty( $userDirs ) ) {
			foreach( $userDirs AS $key => $value ) {
				$subDirs = glob( $value.'/*' );
				if( is_array( $subDirs ) ) {
					$subFiles[] = glob( $value.'/*' );					
				}
			}	
		}		
	}
	
	/*
	 * Fetch DB Data for DataTables
	 * 
	 * @link	http://datatables.net/release-datatables/examples/data_sources/server_side.html
	 * @param	array	$params
	 * @return	array
	 */
	public function fetchDataForDataTables( $params )
	{
		if( empty( $params ) ) {
			return array();	
		}
		
		extract( $params );
		$this->_WeTransfer_Users = new WeTransfer_Users;		
		
		/* Array of database columns which should be read and sent back to DataTables. Use a space where
		 * you want to insert a non-database field (for example a counter or static image)
		*/
		$aColumns = array(	'uuid', 
							'token', 
							'uploader', 
							'direct_url', 
							'upload_date',
							'expiration_date',
							'comment',
							'uploader_ip',
							'id'
		);
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = 'id';
		
		/* DB table to use */
		$sTable = DB_TABLE_PREFIX.'uploads';		

		/*
		 * Paging
		*/
		$sLimit = '';
		if ( isset( $iDisplayStart ) && $iDisplayLength != '-1' ) {
			$sLimit = "LIMIT ".mysql_real_escape_string( $iDisplayStart ).", ".
					mysql_real_escape_string( $iDisplayLength );
		}
		
		/*
		 * Ordering
		*/
		$sOrder = '';
		if ( isset( $params['iSortCol_0'] ) ) {
			$sOrder = "ORDER BY  ";
			for ( $i=0; $i < intval( $params['iSortingCols'] ); $i++ ) {
				if ( $params[ 'bSortable_'.intval( $params['iSortCol_'.$i] ) ] == 'true' ) {
					$sOrder .= "`".$aColumns[ intval( $params['iSortCol_'.$i] ) ]."` ".
						( $params['sSortDir_'.$i] === 'asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, '', -2 );
			if ( $sOrder == 'ORDER BY' ) {
				$sOrder = '';
			}
		}
				
		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		*/
		$sWhere = '';
		if ( $sSearch != '' ) {
			$sWhere = "WHERE (";
			for ( $i = 0; $i < count( $aColumns ); $i++ ) {
				$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $sSearch )."%' OR ";
			}
			
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i = 0; $i < count( $aColumns ); $i++ ) {
			if ( isset( $params['bSearchable_'.$i] ) && 
					$params['bSearchable_'.$i] == 'true' && 
					$params['sSearch_'.$i] != '' ) {
				if ( $sWhere == '' ) {
					$sWhere = 'WHERE ';
				} else {
					$sWhere .= ' AND ';
				}
				
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $params['sSearch_'.$i] )."%' ";
			}
		}
		
		/*
		 * SQL queries
		 * Get data to display
		*/
		$sQuery	= "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns) )." FROM ";   
		$sQuery .= $sTable." ";
		$sQuery .= $sWhere." ";
		$sQuery .= $sOrder." ";
		$sQuery .= $sLimit;

		$rResult = mysql_query( $sQuery ) OR die( mysql_error() );
		
		/* Data set length after filtering */
		$sQuery				= "SELECT FOUND_ROWS()";
		$rResultFilterTotal = mysql_query( $sQuery ) OR die( mysql_error() );
		$aResultFilterTotal = mysql_fetch_array( $rResultFilterTotal );
		$iFilteredTotal		= $aResultFilterTotal[0];
		
		/* Total data set length */
		$sQuery			= "SELECT COUNT(".$sIndexColumn.") FROM $sTable";
		$rResultTotal	= mysql_query( $sQuery ) OR die( mysql_error() );
		$aResultTotal	= mysql_fetch_array( $rResultTotal );
		$iTotal			= $aResultTotal[0];
				
		/*
		* Output
		*/
		$output = array('sEcho'					=> intval( $sEcho ),
						'iTotalRecords' 		=> $iTotal,
						'iTotalDisplayRecords'	=> $iFilteredTotal,
						'aaData'				=> array()
		);
	
		while ( $aRow = mysql_fetch_array( $rResult ) ) {
			$row = array();
			for ( $i = 0; $i < count( $aColumns ); $i++ ) {
				if ( $aColumns[$i] == 'uploader' ) {
					// special output formatting for 'uploader' column
					$row[] = $this->_WeTransfer_Users->fetchEmailById( $aRow[ $aColumns[$i] ] );
				} elseif ( $aColumns[$i] != ' ' ) {
					// general output
					$row[] = $aRow[ $aColumns[$i] ];
				}
			}
			
			$output['aaData'][] = $row;
		}		
		
		return $output;
	}
	
	public function updateFileById( $id, $data = array() )
	{
		if( empty( $data ) ) {
			return false;	
		}
		
		$count	= count( $data );
		$i		= 1;
		
		$sql = "UPDATE `".DB_TABLE_PREFIX."uploads` SET ";
		
		foreach( $data AS $key => $value ) {
			$sql .= "`".mysql_real_escape_string( $key )."` = '".mysql_real_escape_string( $value )."' ";
			
			if( $i < $count ) {
				$sql .= ", ";	
			}
			
			$i++;	
		}
		
		$sql .= "WHERE `id` = '".mysql_real_escape_string( (int)$id ) ."' ";
		$sql .= "LIMIT 1 ";
	
		$res = mysql_query( $sql ) OR die( mysql_error() );
	
		return mysql_affected_rows();
	}	
	
	public function deleteAllFilesByParentId( $id )
	{
		$id		= (int)$id;
		$parent	= $this->fetchParentUploadDataById( $id );
		
		if( empty( $parent ) ) {
			return false;	
		}
		
		if( $parent['local'] == 1 ) {
			$parentDir = BASEDIR.'/'.SITE_UPLOAD_DIR_USERS.'/'.$parent['uploader'].'/'.$parent['uuid'];
			// START:	delete all files
			foreach( glob( $parentDir.'/*') AS $filename ) {
				@unlink( $filename );
			}			
			// END:		delete all files
			
			// delete parent directory
			delTree( $parentDir );			
		} else {
			// handle remote data deletion here...	
		}
		
		// delete child files
		$this->deleteChildFilesByParentId( $id );

		// START:	delete self
		$sql    = "DELETE FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1";		
		$res    = mysql_query( $sql ) OR die( mysql_error() );
		// END:		delete self		
		
		return true;
	}
	
	private function deleteChildFilesByParentId( $id )
	{
		$id		= (int)$id;
		
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploaded_files` ";
		$sql   .= "WHERE `parent_id` = '".mysql_real_escape_string( $id )."' ";
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = array();
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
				
			foreach( $data AS $key => $value ) {
				// delete file
				@unlink( $value['file_path'] );
				// deltree				
				$base = dirname( $value['file_path'] );
				delTree( $base );
		
				$sql    = "DELETE FROM `".DB_TABLE_PREFIX."uploaded_files` ";
				$sql   .= "WHERE `id` = '".mysql_real_escape_string( $value['id'] )."' ";
				$sql   .= "LIMIT 1";
		
				$res    = mysql_query( $sql ) OR die( mysql_error() );
			}
		
			return true;
		}		
	} 
	
	public function fetchParentUploadDataById( $id )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );
			return $data;
		} else {
			return array();
		}
	}	
	
	public function fetchFileDataByUUID( $uuid )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `uuid` = '".mysql_real_escape_string( $uuid )."' ";
		$sql   .= "LIMIT 1";
		 
		$res    = mysql_query( $sql ) OR die( mysql_error() );
		 
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );
			return $data; 		
		} else {
			return array();
		}		
	}

	public function fetchFileDataByToken( $token )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `token` = '".mysql_real_escape_string( $token )."' ";
		$sql   .= "LIMIT 1";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );
			return $data;
		} else {
			return array();
		}
	}
	
	public function fetchFileDataByCustomToken( $customToken )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."recipients` ";
		$sql   .= "WHERE `custom_token` = '".mysql_real_escape_string( $customToken )."' ";
		$sql   .= "LIMIT 1";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data	= mysql_fetch_assoc( $res );			
			$parent = $this->fetchFileDataById( $data['parent_id'] );
			$parent['recipient_id'] = $data['id'];
			
			return $parent;
		} else {
			return array();
		}
	}

	public function fetchRecipientDataById( $id )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."recipients` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );				
			return $data;
		} else {
			return array();
		}
	}	

	public function fetchFileDataById( $id )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `id` = '".mysql_real_escape_string( $id )."' ";
		$sql   .= "LIMIT 1";
			
		$res    = mysql_query( $sql ) OR die( mysql_error() );
			
		if( mysql_num_rows( $res ) > 0 ) {
			$data = mysql_fetch_assoc( $res );
			return $data;
		} else {
			return array();
		}
	}	
	
	public function fetchAllFileIdsByUserId( $userId )
	{
		$sql    = "SELECT `id` FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `owner_id` = '".mysql_real_escape_string( (int)$userId )."' ";
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
			
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row['id'];
			}
	
			return $data;
	
		} else {
			return array();
		}
	}
		
	public function fetchAllFilesByUserId( $userId )
	{
		$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
		$sql   .= "WHERE `owner_id` = '".mysql_real_escape_string( (int)$userId )."' ";		 
		$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
		 
		if( mysql_num_rows( $res ) > 0 ) {
			while( $row = mysql_fetch_assoc( $res ) ) {
				$data[] = $row;
			}
	
			return $data;
	
		} else {
			return array();
		}
	}
		
    public function fetchFilesByUserId( $userId, $limit = SITE_DEFAULT_MEDIA_FETCH_LIMIT, $orderBy = 'upload_date', $sortOrder = 'DESC', $offset = 0 )
    {    	
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
    	$sql   .= "WHERE `owner_id` = '".mysql_real_escape_string( (int)$userId )."' ";
    	$sql   .= "ORDER BY `".mysql_real_escape_string( $orderBy )."` ".mysql_real_escape_string( $sortOrder )." ";
    	$sql   .= "LIMIT ".mysql_real_escape_string( (int)$offset ).", ".mysql_real_escape_string( (int)$limit );
    	
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    	
    	if( mysql_num_rows( $res ) > 0 ) {
    		while( $row = mysql_fetch_assoc( $res ) ) {
    			$data[] = $row;
    		}
    		
    		return $data;
    		
    	} else {
    		return array();
    	}    	
    }
    
    public function fetchUploadCountByUserId( $userId )
    {
    	$sql    = "SELECT COUNT(*) AS `count` FROM `".DB_TABLE_PREFIX."uploads` ";
    	$sql   .= "WHERE `owner_id` = '".mysql_real_escape_string( (int)$userId )."' ";
    	 
    	$res    = mysql_query( $sql ) OR die( mysql_error() );    	
    	$data	= mysql_fetch_assoc( $res );
    	
    	return $data['count'];    	
    }

    public function fetchTotalUploadCount()
    {
    	$sql    = "SELECT COUNT(*) AS `count` FROM `".DB_TABLE_PREFIX."uploads` ";
    
    	$res    = mysql_query( $sql ) OR die( mysql_error() );
    	$data	= mysql_fetch_assoc( $res );
    	 
    	return $data['count'];
    }    
    
    public function fetchAllFiles( $limit = null, $offset = null, $orderBy = 'upload_date', $sortOrder = 'DESC' )
    {
    	if( is_null( $limit ) ) {
    		$limit = 60;
    	}
    	
    	if( is_null( $offset ) ) {
    		$offset = 0;
    	}    	
    	    	
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
    	$sql   .= "ORDER BY `".mysql_real_escape_string( $orderBy )."` ".mysql_real_escape_string( $sortOrder )." ";
    	$sql   .= "LIMIT ".mysql_real_escape_string( (int)$offset ).", ".mysql_real_escape_string( (int)$limit );
    	 
    	$res    = mysql_query( $sql ) OR die( mysql_error().'<br>'.$sql );
    	 
    	if( mysql_num_rows( $res ) > 0 ) {
    		while( $row = mysql_fetch_assoc( $res ) ) {
    			$WeTransfer_Users	= new WeTransfer_Users;
    			$row['owner_name']	= $WeTransfer_Users->fetchUsernameById( $row['owner_id'] );
    			$data[] = $row;
    		}
    
    		return $data;
    
    	} else {
    		return array();
    	}
    }

    public function fetchAllFilesByParentId( $parentId )
    {
    	$sql    = "SELECT * FROM `".DB_TABLE_PREFIX."uploaded_files` ";
    	$sql   .= "WHERE `parent_id` = '".mysql_real_escape_string( $parentId )."' ";    		
    	$res    = mysql_query( $sql ) OR die( mysql_error() );
    		
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

    public function deleteExpiredFiles()
    {
    	$sql = "SELECT * FROM `".DB_TABLE_PREFIX."uploads` ";
    	$sql .= "WHERE `expiration_date` <= '".mysql_real_escape_string( time() )."' ";
    	$res = mysql_query( $sql ) OR die( mysql_error() );
    	
    	if( mysql_num_rows( $res ) > 0 ) {
    		$data = array();
    		while( $row = mysql_fetch_assoc( $res ) ) {
    			$data[] = $row;
    		}
    		
			foreach( $data AS $key => $value ) {
				$this->deleteAllFilesByParentId( $value['id'] );				
			}
			
			return true;
    	} else {
    		return false;
    	}    	
    }
	 
    // END OF THIS CLASS
}