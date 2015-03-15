<?php
/**
 * White Label Transfer
 * Various Functions
 *
 * @author      BizLogic <hire@whitelabeltransfer.com>
 * @license     GNU Affero General Public License v3
 * @link        http://whitelabeltransfer.com
 * @link        http://bizlogicdev.com
 *
 * @since       Wednesday, October 21, 2009 / 07:01 PM UTC+1 (mknox)
 * @edited      $Date: 2011-05-03 12:36:13 +0200 (Tue, 03 May 2011) $ $Author: mknox $
 * @version     $Revision: 2 $
 *
 * @package     White Label Transfer
 * @subpackage  Common Functions
*/

function updateAdminAccount( $username, $password )
{
	$sql = "UPDATE `".mysql_real_escape_string( DB_TABLE_PREFIX )."users` ";
	$sql .= "SET `username` = '".mysql_real_escape_string( $username )."', ";
	$sql .= "`password` = '".mysql_real_escape_string( sha1( $password ) )."' ";
	$sql .= "WHERE `id` = '1' ";
	$sql .= "LIMIT 1 ";
	
	$res = mysql_query( $sql ) OR die( mysql_error() );
}

function updateDbConfig()
{
	$source		= BASEDIR.'/includes/templates/db.ini';
	$target		= ROOT_DIR.'/application/configs/db.ini';
		
	$dbConfig	= file_get_contents( $source );
	$dbConfig 	= str_replace('__DB_HOST__', '"'.$_SESSION['dbHost'].'"', $dbConfig );
	$dbConfig 	= str_replace('__DB_USERNAME__', '"'.$_SESSION['dbUsername'].'"', $dbConfig );
	$dbConfig 	= str_replace('__DB_PASSWORD__', '"'.$_SESSION['dbPassword'].'"', $dbConfig );
	$dbConfig 	= str_replace('__DB_NAME__', '"'.$_SESSION['dbName'].'"', $dbConfig );
	$dbConfig 	= str_replace('__DB_TABLE_PREFIX__', DB_TABLE_PREFIX, $dbConfig );

	// save
	file_put_contents( $target, $dbConfig );
}

function checkDbConnection( $hostname, $username, $password, $dbName, $port = 3306 )
{
	$mysqli = new mysqli($hostname, $username, $password, $dbName, $port);
	if ( $mysqli->connect_errno ) {
		return 'MySQL Error:  '. $mysqli->connect_errno . ' ' . $mysqli->connect_error;
	}
	
	return true;	
}

function checkDirPerms( $dirs = array() )
{
	if( empty( $dirs ) ) {
		return array();
	}

	$invalid = array();
	$required = $dirs;

	foreach( $required AS $key => $value ) {
		if( !is_writable( $value ) ) {
			$invalid[] = $value;
		}
	}

	return $invalid;
}

function checkFolderPermissions()
{
	$invalid   = array();
	$required  = array(
	    'application/configs/db.ini',
        'data/logs', 
        'data/logs/errors',
        'data/logs/php',
        'data/logs/zip',			
        'data/sessions',
        'data/temp',
        'data/uploads',
        'data/uploads/users',
        'install/logs',
        'install'
	);

	foreach( $required AS $key => $value ) {
		if( !is_writable( ROOT_DIR.'/'.$value ) ) {			
			$invalid[] = ROOT_DIR.'/'.$value;
		}		
	}
	
	return $invalid;
}

function getFilePerms( $file )
{
	return substr( sprintf('%o', fileperms( $file ) ), -4 );	
}

/**
 * determine the server URL
 *
 * @return  string
 */
function fetchServerURL()
{
	$url = fetchCurrentURL();

	if(preg_match('/phpunit/', $url)) {
		return 'phpunit';
	}

	$url = parse_url($url);

	if(!strlen(@$url['path'])) {
		return;
	}

	$pathinfo   = pathinfo($url['path']);
	$serverURL  = 'http';

	if (@$_SERVER['HTTPS'] == 'on') {
		$serverURL .= 's';
	}

	$serverURL    .= "://";
	$serverURL    .= @$_SERVER['HTTP_HOST'];
	$dirname		= array_filter( explode( '/', $pathinfo['dirname'] ) );

	if( empty( $dirname ) ) {
		$pathinfo['dirname'] = '';
	}
		
	$serverURL    .= $pathinfo['dirname'];

	return $serverURL;
}

/**
 * determine the current URL
 *
 * @return  string
 */
function fetchCurrentURL()
{
	if(strlen(@$_SERVER['SHELL'])) {
		return $_SERVER['PHP_SELF'];
	}

	$pageURL = 'http';

	if (@$_SERVER['HTTPS'] == 'on') {
		$pageURL .= 's';
	}

	$pageURL    .= "://";
	$pageURL    .= (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
	$pageURL    .= $_SERVER['PHP_SELF'];
	$queryString = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';

	if(strlen($queryString)) {
		$pageURL .= '?'.$queryString;
	}

	return $pageURL;
}

function GetBaseDir()
{
    $pathParts 	    = pathinfo( $_SERVER['SCRIPT_FILENAME'] );
    $basedirPath 	= $pathParts['dirname'];
    
    return $basedirPath;
}

function GetBaseURL()
{
	// subdomain check
	$dir = basename( GetBaseDir() );
	
	if( !preg_match( '/'.$dir.'/', $_SERVER['HTTP_HOST'] ) ) {
		return GetServerURL().'/'.$dir;		
	} else {
		return GetServerURL().'/';		
	}
}

function WriteLog($filename, $msg, $mode = 'a')
{
    $fd = fopen($filename, $mode);
    fwrite($fd, "[" .date('l, F j, Y / h:i:s A T (\G\M\TO)'). "]\n");
	fwrite($fd, $msg. "\n");
	fclose($fd);
}

function GetServerURL()
{
	if( defined( 'IN_PHPUNIT' ) ) {
		return;
	}
		
    return GetServerProtocol().$_SERVER['HTTP_HOST'];
}

function curPageURL()
{
	if( defined( 'IN_PHPUNIT' ) ) {
		return;
	}
		
    $pageURL = 'http';

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        $pageURL .= "s";
    }

    $pageURL       .= "://";
    $pageURL       .= $_SERVER['SERVER_NAME'];
    $pageURL       .= $_SERVER['PHP_SELF'];
    $queryString    = $_SERVER['QUERY_STRING'];

    if(strlen($queryString)){
        $pageURL .= '?'.$queryString;
    }

    return $pageURL;
}

function GetServerProtocol()
{
    if( defined( 'IN_PHPUNIT' ) ) {
    	return;	
    }
    
	if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
        return 'https://';
    } else {
        $protocol = preg_replace( '/^([a-z]+)\/.*$/', '\\1', strtolower( $_SERVER['SERVER_PROTOCOL'] ) );
        $protocol .= '://';

        return $protocol;
    }
}

function checkInstall()
{
	if( file_exists( BASEDIR.'/includes/IS_INSTALLED' ) ) {
		exit('APPLICATION IS ALREADY INSTALLED');	
	}	
}

function jQueryUIStringToTemplateName( $string )
{
	$string = str_replace( ' ', '-', $string );
	$string = strtolower( $string );

	return $string;
}

function inPHPUnit()
{
	if( basename( $_SERVER['PHP_SELF'] ) == 'phpunit' ) {
		return true;	
	}	
}