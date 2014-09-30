<?php
/**
 * White Label Transfer
 * Error Controller
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 *
 * @since  	    Tuesday, November 27, 2012, 05:35 PM GMT+1
 * @version     $Id: ErrorController.php 71 2013-10-13 03:55:47Z dev@whitelabeltransfer.com $
 * @edited      $Date: 2013-10-13 05:55:47 +0200 (So, 13 Okt 2013) $
 *
 * @package     White Label Transfer
 */

define('THIS_PAGE', 'ERROR');
class ErrorController extends Zend_Controller_Action
{
	private $_WeTransfer_Users;
	
	public function init()
	{
		$this->_WeTransfer_Users = new WeTransfer_Users();		
	}
	
    public function errorAction()
    {
        $errors     = $this->_getParam('error_handler');
        $exception  = $errors->exception;
        $message    = $exception->getMessage();
        $trace      = $exception->getTraceAsString();
        
        $controller	= trim( $this->getRequest()->getParam('controller') );

        switch ( $errors->type ) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                // ... get some output to display...
                break;
				
            default:
            	if( !preg_match( '/Invalid controller specified/', $exception->getMessage() ) ) {
            		// application error; display error page, but don't change
            		// status code
            		// Log the exception:
            		$log = new Zend_Log(
            				new Zend_Log_Writer_Stream(
            						LOG_DIR.'/php-exceptions-'.date('m-d-Y').'.log'
            				)
            		);
            		$log->debug($exception->getMessage() . "\n" .
            				$exception->getTraceAsString());            		
            	}
        }

        // clear previous content
        $this->getResponse()->clearBody();
        
        if( is_array( @$_SESSION['site']['permissions']['admin'] ) ) {
        	if( in_array('can_view_debug_messages', $_SESSION['site']['permissions']['admin'] ) ) {
        		$this->view->message    = $message;
        		$this->view->trace      = $trace;
        	} else {
        		$this->view->message    = fetchTranslation('http_error_404_text');
        		$this->view->trace      = null;
        	}        	
        } else {
        	$this->view->message	= fetchTranslation('http_error_404_text');
        	$this->view->trace      = null;        	
        }
    }
}