<?php
/**
 * White Label Transfer
 * Transfer Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Tuesday, November 27, 2012, 04:18 PM GMT+1
 * @modified    $Date: 2011-11-16 18:27:16 +0100 (Wed, 16 Nov 2011) $ $Author: mknox $
 * @version     $Id: IndexController.php 5139 2011-11-16 17:27:16Z mknox $
 *
 * @category    Controllers
 * @package     White Label Transfer
*/

class TransferController extends Zend_Controller_Action
{
	protected $_WeTransfer_Files;	
	protected $_WeTransfer_Upload;
	protected $_WeTransfer_Users;
	
    public function init() 
    {
    	$this->_WeTransfer_Files	= new WeTransfer_Files;
    	$this->_WeTransfer_Upload	= new WeTransfer_Upload;
    	$this->_WeTransfer_Users	= new WeTransfer_Users;
 
    	if( IS_MOBILE ) {    		
			$this->view->setScriptPath( VIEWS_DIR.'/mobile/' );
    	}
    }
    
    public function __call($method, $args)
    {
    	$token = trim( $this->getRequest()->getParam('action') );
    	    	
    	if( strlen( $token ) ) {
    		$ownerDownload = false;  
    		// owner download  		
    		$data = $this->_WeTransfer_Files->fetchFileDataByToken( $token );

    		if( empty( $data ) ) {
    			// recipient download
    			$data = $this->_WeTransfer_Files->fetchFileDataByCustomToken( $token );
    		} else {
    			$ownerDownload = true;	
    		}
    	}
    	
    	if( empty( $data ) ) {
    		if ('Action' == substr($method, -6)) {
    			// If the action method was not found, render the error
    			// template
    			return $this->render('error');
    		}    		
    	}

    	if( (int)$data['local'] != 1 ) {
    		// notify uploader
    		if( !$ownerDownload ) {
    			$this->_WeTransfer_Upload->notifyUploaderOfDownload( $data['id'], $data['recipient_id'] );
    			$this->_WeTransfer_Files->logDownload( $data['id'], $data['recipient_id'] );
    		}
    		    		
			header( 'Location: '.$data['direct_url'].'' );
			exit;
    	} else {    		
    		$file = $data['file_path'];
    		
    		if( file_exists( $file ) ) {
    			
    			// notify uploader
    			if( !$ownerDownload ) {
    				$this->_WeTransfer_Upload->notifyUploaderOfDownload( $data['id'], $data['recipient_id'] );
    				$this->_WeTransfer_Files->logDownload( $data['id'], $data['recipient_id'] );
    			}    			
    			
    			header('Content-Description: File Transfer');
    			header('Content-Type: application/octet-stream');
    			header('Content-Disposition: attachment; filename='.basename( $file ) );
    			header('Content-Transfer-Encoding: binary');
    			header('Expires: 0');
    			header('Cache-Control: must-revalidate');
    			header('Pragma: public');
    			header('Content-Length: ' . filesize( $file ) );
    			
    			ob_clean();
    			flush();
    				
    			readfile_chunked( $file );    			
    			    			
    			    			
    			/*if( in_array('mod_xsendfile', apache_get_modules() ) AND ( SITE_USE_XSEND == 1 ) ) {
    				header('X-Sendfile: '.$file);    			
    				header('Content-type: application/octet-stream');
    				header('Content-Disposition: attachment; filename="' . basename( $file ) . '"');
    			} else {
    				header('Content-Description: File Transfer');
    				header('Content-Type: application/octet-stream');
    				header('Content-Disposition: attachment; filename='.basename( $file ) );
    				header('Content-Transfer-Encoding: binary');
    				header('Expires: 0');
    				header('Cache-Control: must-revalidate');
    				header('Pragma: public');
    				header('Content-Length: ' . filesize( $file ) );
    				 
    				ob_clean();
    				flush();
    				 
    				readfile_chunked( $file );    				
    			} */   			 			    			   			
    			
    			exit;
    		}    		
    	}   
    }    

    public function indexAction() {}
}