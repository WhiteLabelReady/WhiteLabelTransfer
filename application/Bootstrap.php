<?php
/**
 * White Label Transfer
 * Bootstrap
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 * @link        https://www.gnu.org/licenses/agpl-3.0.txt
 *
 * @since       Thursday, April 21, 2011 / 01:04 AM GMT+1
 * @edited      $Date: 2013-10-19 18:37:52 +0200 (Sa, 19 Okt 2013) $
 * @version     $Id: Bootstrap.php 93 2013-10-19 16:37:52Z dev@whitelabeltransfer.com $
 *
 * @category    Bootstrap
 * @package     White Label Transfer
*/

error_reporting( E_ALL );
ini_set( 'display_errors', false );

set_time_limit( 0 );
date_default_timezone_set( 'UTC' );

// common functions
require_once( 'functions.php' );
// global constants
require_once( 'constants.php' );

ini_set( 'error_log', LOG_DIR.'/php/php-errors-'.date('m-d-Y').'.log' );

require_once( 'Zend/Loader/Autoloader.php' );
$Zend_Loader_Autoloader = Zend_Loader_Autoloader::getInstance();
$Zend_Loader_Autoloader->setFallbackAutoloader( true );

// DB
require_once( 'db.php' );

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private $_BizLogic_Cache;

    /*
     * Bootstrap constructor
     *
     * @param   string  $env    application environment
     */
    public function __construct( $env )
    {
    	$this->_checkInstall();
    	$this->_setRunEnv();    	
    	$this->_checkFolderPerms();
    	$this->_setupFirePHP();
        $this->_BizLogic_Cache = new BizLogic_Cache;
        $this->_setupSession();
        $this->_setupLocale();
        $this->_setupCache();
        $this->_setupMySQL();
        $this->_setupSiteConfig();
        $this->_deleteExpiredDownloads();
        $this->_setupLanguage();
        $this->_setupThemes();
        $this->_updateUserSession();
    }
    
    private function _checkInstall()
    {
    	if( file_exists( BASEDIR.'/install' ) ) {
    		if( !file_exists( BASEDIR.'/install/install.lock' ) ) {
    			header('Location: '.BASEURL.'/install');    			
    		}	
    	}	
    }
    
    private function _checkFolderPerms()
    {
    	$required	= array();
    	$required[]	= BASEDIR.'/data';
    	$required[]	= BASEDIR.'/data/cache';
    	$required[]	= BASEDIR.'/data/logs';
    	$required[]	= BASEDIR.'/data/sessions';
    	$required[]	= BASEDIR.'/data/temp';
    	$required[]	= BASEDIR.'/data/uploads';
    	$required[]	= BASEDIR.'/data/uploads/users';
    	
    	$errors		= array();
    	
    	foreach( $required AS $key => $value ) {
    		if( !is_writeable( $value ) ) {
				$errors[] = $value;    			
    		}	
    	}
    	
    	if( !empty( $errors ) ) {
    		$errorMessage = 'The following file permission errors are preventing the application from functioning properly:<ol type="">';    		
    		foreach( $errors AS $key => $value ) {
    			$errorMessage .= '<li>'.$value.' is not writeable. chmod it to 0777</li>'; 
    		}    		
    		
    		$errorMessage .= '</ol>';
    		
    		exit( $errorMessage );
    	}
    }

    private function _setupThemes()
    {
    	global $SITE_THEMES;
    	
    	$WeTransfer_Site_Themes	= new WeTransfer_Site_Themes;
    	$SITE_THEMES			= $WeTransfer_Site_Themes->fetchThemesForDisplay();
    }
    
    private function _setupSessionParams()
    {
        // 10 years
        if(!defined('COOKIE_TIMEOUT')) {
            define('COOKIE_TIMEOUT', 315360000);
        }

        if(!defined('GARBAGE_TIMEOUT')) {
            define('GARBAGE_TIMEOUT', COOKIE_TIMEOUT);
        }

        ini_set('session.gc_maxlifetime', GARBAGE_TIMEOUT);
        session_set_cookie_params(COOKIE_TIMEOUT, '/');

        // setting session dir
        if(isset($_SERVER['HTTP_HOST'])) {
            $sessdir = '/tmp/'.$_SERVER['HTTP_HOST'];
        } else {
            $sessdir = '/tmp/instagram';
        }

        // if session dir not exists, create directory
        if (!is_dir($sessdir)) {
            @mkdir($sessdir, 0777);
        }

        //if directory exists, then set session.savepath otherwise let it go as is
        if(is_dir($sessdir)) {
            ini_set('session.save_path', $sessdir);
        }
    }

    private function _setupSiteConfig()
    {
        $BizLogic_WeTransfer = new BizLogic_WeTransfer;
        $BizLogic_WeTransfer->defineSiteConfig();
    }

    private function _setupSession()
    {
        $this->_setupSessionParams();

        if( !isset( $_SESSION ) ) {
            session_start();
        }

        setInitialSessionValues();        
    }

    /**
     * setup Zend_Cache
     *
     * @link    http://framework.zend.com/manual/en/zend.cache.introduction.html
     * @todo    move cache lifetime to ini file
     */
    private function _setupCache()
    {
        $this->_BizLogic_Cache->setupCache( 86400, 'cache' );
        $this->_BizLogic_Cache->setupCache( 3600, 'cacheOneHour' );
        $this->_BizLogic_Cache->setupCache( 900, 'cacheFifteenMin' );
    }

    /**
     * setup a MySQL connection
     */
    protected function _setupMySQL()
    {
        $config     = new Zend_Config_Ini( APP_DIR.'/configs/db.ini', 'live' );
        $db         = $config->params->toArray();

        $mysql      = mysql_connect( $db['host'], $db['username'], $db['password'] ) OR die( mysql_error() );
        $mysqldb    = mysql_select_db( $db['dbname'], $mysql ) OR die( mysql_error() );

        // set charset
        mysql_set_charset( 'utf8', $mysql );
    }

    protected function _setupLocale()
    {
        $_SESSION['user']['locale'] = determineUserLocale();
    }

    protected function _setupLanguage()
    {        
        $WeTransfer_Languages = new WeTransfer_Languages();
        
        if( isset( $_SESSION['user']['lang_override'] ) ) {
        	if( $_SESSION['user']['lang_override'] ) {
        		$_SESSION['user']['language_id'] = $_SESSION['user']['selected_lang_id'];
        	}	
        }
        
        // we merge the default language w/ the detected language, in case 
        // phrases do not exist in the detected language
        // @TODO:	there should be no merge if the selected language matches the site default
        $siteDefaultLanguageId = $WeTransfer_Languages->fetchLanguageIdByIso31661( SITE_DEFAULT_LANGUAGE );
        
        if( !@$_SESSION['user']['lang_override'] ) {
        	$_SESSION['user']['language_id']	= $WeTransfer_Languages->fetchLanguageIdByLocale( $_SESSION['user']['locale'] );
        	$_SESSION['user']['site_language']	= $_SESSION['user']['language_id'];        	        	
        } else {
        	$_SESSION['user']['site_language'] = $_SESSION['user']['language_id']; 
        }
        
        $siteDefaultPhrases				= $WeTransfer_Languages->fetchPhrasesByLanguageId( $siteDefaultLanguageId );
        $_SESSION['site']['phrases']	= $WeTransfer_Languages->fetchPhrasesByLanguageId( $_SESSION['user']['language_id'] );       
        $_SESSION['site']['phrases']	= array_merge( $siteDefaultPhrases, $_SESSION['site']['phrases'] );
    }

    protected function _setRunEnv()
    {
        $env = determineRunEnvironment();
        setRunEnvironment( $env );

        Zend_Registry::set( 'RUN_ENV', $env );
    }
    
    protected function _updateUserSession()
    {
    	if( IS_MOBILE ) {
    		exit( 'Mobile devices are not yet supported' );	
    	}   
    	 	
		// we want to update the user session on every page hit
    	$WeTransfer_Users = new WeTransfer_Users();		
    	$WeTransfer_Users->updateUserSession();

    	if( @$_SESSION['user']['logged_in'] ) {
    		if( empty( $_SESSION['site']['permissions'] ) ) {
    			$this->noPerms();
    		}    		
    	}

		$siteStatus = @$_SESSION['user']['site_status'];
		switch( $siteStatus ) {
			case 'banned':
				$html = file_get_contents( VIEWS_DIR.'/error/static/error.phtml' );
				$html = str_replace( '__SITE_NAME__', SITE_NAME, $html );
				$html = str_replace( '__ERROR_MESSAGE__', 'Your user account is banned', $html );
				$html = str_replace( '__THEME_PATH__', PROTOCOL_RELATIVE_URL.'/'.SITE_LOCAL_THEME_URL_ROOT.'/'.SITE_DEFAULT_TEMPLATE, $html );
				$html = str_replace( '__JS_PATH__', PROTOCOL_RELATIVE_URL.'/js', $html );
				
				exit( $html );
				
				break;
				
			case 'pending':				
				$html = file_get_contents( VIEWS_DIR.'/error/static/error.phtml' );
				$html = str_replace( '__SITE_NAME__', SITE_NAME, $html );
				$html = str_replace( '__ERROR_MESSAGE__', 'Please check your e-mail for information on how to activate your account', $html );
				$html = str_replace( '__THEME_PATH__', PROTOCOL_RELATIVE_URL.'/'.SITE_LOCAL_THEME_URL_ROOT.'/'.SITE_DEFAULT_TEMPLATE, $html );
				$html = str_replace( '__JS_PATH__', PROTOCOL_RELATIVE_URL.'/js', $html );
								
				exit( $html );				

				break;
		}
    }
    
    protected function _setupFirePHP()
    {    	
    	if( (int)ENABLE_FIREPHP == 1 ) {
    		require_once('FirePHP/FirePHP.class.php');
    		$firephp = FirePHP::getInstance( true );
    		require_once('FirePHP/fb.php');
    		 
    		// disable for non-admin users
    		$firephp->setEnabled( true );
    		
    		$firephp->registerErrorHandler( $throwErrorExceptions = false );
    		$firephp->registerExceptionHandler();
    		$firephp->registerAssertionHandler(
    				$convertAssertionErrorsToExceptions=true,
    				$throwAssertionExceptions=false);
    		 
    		// START:	FirePHP
    		$firebugWriter = new Zend_Log_Writer_Firebug();
    		$firebugLogger = new Zend_Log( $firebugWriter );
    		Zend_Registry::set( 'firebugLogger', $firebugLogger );
    		// END:		FirePHP    		
    	}    	
    }
    
    protected function noPerms()
    {
		define( 'NO_SITE_PERMS', true );		
		$_SESSION['user']['logged_in'] = false;	

		$html = file_get_contents( VIEWS_DIR.'/error/static/error.phtml' );
		$html = str_replace( '__SITE_NAME__', SITE_NAME, $html );
		$html = str_replace( '__ERROR_MESSAGE__', 'Your account has not been granted permissions for this site', $html );
		$html = str_replace( '__THEME_PATH__', PROTOCOL_RELATIVE_URL.'/'.SITE_LOCAL_THEME_URL_ROOT.'/'.SITE_DEFAULT_TEMPLATE, $html );
		$html = str_replace( '__JS_PATH__', PROTOCOL_RELATIVE_URL.'/js', $html );
		$html = str_replace( '__PROTOCOL_RELATIVE_URL__', PROTOCOL_RELATIVE_URL, $html );
		$html = str_replace( '__SITE_DEFAULT_PRELOADER_IMAGE_PATH__', SITE_DEFAULT_PRELOADER_IMAGE_PATH, $html );
		
		exit( $html );
    }
    
    protected function _deleteExpiredDownloads()
    {
    	$WeTransfer_Files = new WeTransfer_Files;
    	$WeTransfer_Files->deleteExpiredFiles();	
    }

    public function run()
    {      		    	
        $front = Zend_Controller_Front::getInstance();
        $front->throwExceptions( false );
        $front->setControllerDirectory(
                        array('default' => PATH.'/application/modules/public/controllers')
                    );
        $front->setParam( 'useDefaultControllerAlways', false );
        $front->setParam( 'displayExceptions', true );

        try {
            $front->dispatch();
        } catch(Exception $e) {
            $request = $front->getRequest();
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');

            $error              = new Zend_Controller_Plugin_ErrorHandler();
            $error->type        = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request     = clone($request);
            $error->exception   = $e;
            $request->setParam('error_handler', $error);
        }
    }
}