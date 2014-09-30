<?php
/**
 * White Label Transfer
 * Upload Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Friday, June 28, 2013, 02:38 PM GMT+1
 * @modified    $Date: 2013-10-19 01:25:51 +0200 (Sa, 19 Okt 2013) $ $Author: dev@whitelabeltransfer.com $
 * @version     $Id: UploadController.php 91 2013-10-18 23:25:51Z dev@whitelabeltransfer.com $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

class UploadController extends Zend_Controller_Action
{
	protected $_accountStatus;
	protected $_WeTransfer_Upload;
	
    public function init() 
    {    	
    	$this->_WeTransfer_Upload = new WeTransfer_Upload;
    	
    	if( IS_MOBILE ) {
    		$this->view->setScriptPath( VIEWS_DIR.'/mobile/' );
    	}

		// START:	chunk handling
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
    				 
		$uuid				= trim( $_POST['uuid'] );
		$cleanupTargetDir	= true;
		$targetDir			= BASEDIR.'/data/temp/'.$uuid;
    	// Temp file age in seconds
		$maxFileAge			= 5 * 3600;
		$chunk				= isset( $_REQUEST['chunk'] ) ? intval( $_REQUEST['chunk'] ) : 0;
		$chunks				= isset( $_REQUEST['chunks'] ) ? intval( $_REQUEST['chunks'] ) : 0;
    				
		// create target directory
		if( !file_exists( $targetDir ) ) {
			@mkdir( $targetDir );
		}
    				
    	// check for filename
		if (isset($_REQUEST['name'])) {
			$fileName = $_REQUEST['name'];
		} elseif (!empty($_FILES)) {
			$fileName = $_FILES['file']['name'];
		} else {
			$fileName = uniqid('file_');
		}    				

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
    				    				
    	// Remove old temp files
		if( $cleanupTargetDir ) {
			if ( !is_dir( $targetDir ) || !$dir = opendir( $targetDir ) ) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}
    				
			while ( ( $file = readdir( $dir ) ) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
    							
    			// if temp file is current file proceed to the next
				if($tmpfilePath == "{$filePath}.part") {
					continue;
				}
				    						
    			// remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
    					
			closedir($dir);
		}    				
    				
    	// open temp file
		if(!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
    				 
		if( !empty( $_FILES ) ) {
			if($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
    					 
    		// read binary input stream and append it to temp file
			if( !$in = @fopen( $_FILES["file"]["tmp_name"], "rb" ) ) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}
    				 
		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}
    				 
		@fclose($out);
		@fclose($in);
    				 
    	// check if file has been uploaded
		if( !$chunks || $chunk == $chunks - 1 ) {
    		// strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
		} else {
    		// return Success JSON-RPC response
			exit( json_encode( array('chunk'		=> $_REQUEST['chunk'], 
									 'totalChunks'	=> $_POST['chunks'] 
								) 
							)
			);    					
		}
		// END:		chunk handling    			    	
    	
		if( !empty( $_POST ) AND !empty( $_FILES ) ) {			
			if ( $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
				$fileData					= array();				
				$fileData['userId']			= (int)$_SESSION['user']['id'];
				$fileData['tmp_name']		= $_FILES['file']['tmp_name'];
				$fileData['uuid']			= $_POST['uuid'];
				$fileData['filename']		= fetchFilename( $filePath );
				$fileData['fileExt']		= fetchFileExt( $filePath );
				$fileData['uploader_ip'] 	= $_SERVER['REMOTE_ADDR'];
	
				if( isAllowedFileType( $fileData['fileExt'] ) ) {
					header( 'Content-type: application/json' );	
					$json			= array();
					$json['status'] = 'OK';
						
					exit( json_encode( $json ) );
																
					//exit( $this->_WeTransfer_Upload->handleTempFileUpload( $filePath, $fileData ) );
					//exit( $this->_WeTransfer_Upload->handleTempFileUpload( $fileData['tmp_name'], $fileData ) );			
				} else {
					$json			= array();
					$json['status']	= 'ERROR';
					$json['error']	= 'FILE_TYPE_NOT_PERMITTED';
	
					header( 'Content-type: application/json' );
					exit( json_encode( $json ) );
				}
			}
		} elseif( !empty( $_POST['formParams']['email'] ) && !empty( $_POST['formParams']['recipients'] ) ) {
			header( 'Content-type: application/json' );
			exit( $this->_WeTransfer_Upload->completeFileUpload( $_POST['formParams'] ) );			
		} else {
			$json			= array();
			$json['status']	= 'ERROR';
			$json['error']	= 'FILE_UPLOAD_ERROR';
			
			header( 'Content-type: application/json' );
			exit( json_encode( $json ) );			
		}
    }

    public function indexAction() 
    {
    	if( !in_array('can_upload', $_SESSION['site']['permissions']['upload'] ) ) {
    		$this->_helper->viewRenderer->setRender('no-rights');    		
    	} 

    	if( $this->_accountStatus != 'OK' ) {
    		$this->view->error = $this->_accountStatus;
    		$this->_helper->viewRenderer->setRender('no-rights');    		
    	}
    }
}