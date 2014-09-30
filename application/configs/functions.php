<?php
/**
 * Various Functions
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 *
 * @since       Wednesday, April 20, 2011 / 10:30 PM GMT+1
 * @edited      $Date: 2012-02-14 12:20:54 +0100 (Tue, 14 Feb 2012) $
 * @version     $Id: functions.php 6605 2012-02-14 11:20:54Z mknox $
 *
 * @package     White Label Transfer
 */

function ini_flatten($config)
{
	$flat = array();
	foreach ($config as $key => $info) {
		$flat[$key] = $info['local_value'];
	}
	return $flat;
}

function ini_diff($config1, $config2) 
{
	return array_diff_assoc(ini_flatten($config1), ini_flatten($config2));
}

// http://php.net/manual/en/function.readfile.php#54295
function readfile_chunked( $filename, $retbytes = true ) 
{
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	// $handle = fopen($filename, 'rb');
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		ob_flush();
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if ($retbytes && $status) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}

function bytesToHumanReadable($bytes, $precision = 2) 
{
    $unit = array('B','KB','MB','GB','TB','PB','EB');
    return @round( $bytes / pow( 1024, ( $i = floor( log( $bytes, 1024 ) ) ) ), $precision ).' '.$unit[$i];
}

if (!function_exists('getallheaders'))
{	
	$headers = array();
	foreach ($_SERVER AS $name => $value) {
		if (substr($name, 0, 5) == 'HTTP_') {
			$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
			$headers[$name] = $value;
		} else if ($name == 'CONTENT_TYPE') {
			$headers['Content-Type'] = $value;
		} else if ($name == 'CONTENT_LENGTH') {
			$headers['Content-Length'] = $value;
		}
	}
	
	return $headers;	
}

/**
 * fetch a translation from the user's session
 *
 * @param	string	$phrase
 * @param	string	$case
 * @return  string
 */
function fetchTranslation( $phrase, $case = 'AS_IS' )
{
	if( array_key_exists( $phrase, $_SESSION['site']['phrases'] ) ) {		
		$phrase = $_SESSION['site']['phrases'][$phrase];		
	}
	
	switch( $case ) {
		// converts the first character of a string to lowercase
		case 'lcfirst':
			$phrase = lcfirst( $phrase );
			break;
				
			// converts a string to lowercase
		case 'strtolower':
			$phrase = strtolower( $phrase );
			break;
	
			// converts a string to uppercase
		case 'strtoupper':
			$phrase = strtoupper( $phrase );
			break;
	
			// converts the first character of a string to uppercase
		case 'ucfirst':
			$phrase = ucfirst( $phrase );
			break;
	
			// converts the first character of each word in a string to uppercase
		case 'ucwords':
			$phrase = ucwords( $phrase );
			break;
	}	
	
	return $phrase;	
}

function hoursToSeconds( $hours )
{
	return (int)( (int)$hours * 3600 );	
}

function delTree( $dir ) 
{
	$files = array_diff( scandir($dir), array('.', '..') );
	foreach( $files AS $file ) {
		( is_dir( $dir.'/'.$file ) ) ? delTree( $dir.'/'.$file ) : unlink( $dir.'/'.$file );
	}
	
	return rmdir( $dir );
}

function fetchColumnNames( $tableName, $returnAllData = false )
{
	$result = mysql_query("SHOW COLUMNS FROM `".mysql_real_escape_string( $tableName )."` ");
	if ( !$result ) {
		return mysql_error();
	}
	
	if( mysql_num_rows( $result ) > 0 ) {
		$columnNames = array();
		while ($row = mysql_fetch_assoc( $result ) ) {
			$columnNames[] = $row;
		}
		
		if( $returnAllData ) {
			return $columnNames;			
		} else {
			$columns = array();
			foreach( $columnNames AS $key => $value ) {
				$columns[] = $value['Field'];	
			}
			
			return $columns;
		}
	}		
}
	
function urlExists( $url )
{
	$handle = curl_init($url);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($handle);
	
	/* Check for 404 (file not found). */
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	
	if($httpCode >= 403) {
		$exists = false;
	} else {
		$exists = true;	
	}
	
	curl_close($handle);

	return $exists;
}

function randomString( $length = 10 )
{
	return substr( str_shuffle( md5( microtime() ) ), 0, $length );	
}

/**
 * Detect a mobile device
 *
 * @return  boolean
 */
function is_mobile()
{
	require_once('Mobile_Detect.php');	
	$detect = new Mobile_Detect;
	
	// any mobile device (phones or tablets).
	if ( $detect->isMobile() ) {
		return true;	
	}	
	
	return false;
}

function is_tablet()
{
	require_once('Mobile_Detect.php');
	$detect = new Mobile_Detect;

	// any mobile device (phones or tablets).
	if ( $detect->isTablet() ) {
		return true;
	}

	return false;
}

/**
 * fetch file extension
 *
 * @param   string
 * @return  string
 */
function fetchFileExt($file)
{
    return strtolower( substr($file, strrpos($file, '.', -1) + 1) );
}

/**
 * recursive glob
 *
 * @author  arvin@sudocode.net
 * @param   string  $path       path of folder to search
 * @param   string  $pattern    glob pattern
 * @param   string  $flags      glob flags
 * @param   string  $depth      0 for current folder only,
 *                              1 to descend 1 folder down and so on.
 *                              -1 for no limit.
 * @link    http://www.php.net/manual/en/function.glob.php#101017
 * @return  array
 */
function bfglob($path, $pattern = '*', $flags = 0, $depth = 0)
{
    $matches = array();
    $folders = array(rtrim($path, DIRECTORY_SEPARATOR));

    while($folder = array_shift($folders)) {
        $matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
        if($depth != 0) {
            $moreFolders    = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
            $depth          = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
            $folders        = array_merge($folders, $moreFolders);
        }
    }

    return $matches;
}

/**
 * fetch filename
 *
 * @param   string
 * @return  string
 */
function fetchFilename($file)
{
    return substr($file, 0, strrpos($file, '.', -1));
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

/**
 * convert a string to a valid MySQL datetime value
 *
 * @param   string  $str
 * @return  string
 */
function stringToMySQLDateTime($str)
{
    return date('Y-m-d H:i:s', strtotime($str));
}

/**
 * convert a string to a valid MySQL date value
 *
 * @param   string  $str
 * @return  string
 */
function stringToMySQLDate($str)
{
    return date('Y-m-d', strtotime($str));
}

/**
 * convert a string to the month year
 *
 * @param   string  $str
 * @return  string
 */
function stringToMonthYear($str)
{
    return date('m/Y', strtotime($str));
}

/**
 * detect URLs in text
 *
 * @param   string  $text
 * @return  array
 */
function detectFullURLs($text)
{
    $pattern    = '(((http)(s?)\:\/\/))';
    $pattern   .= '[A-Za-z0-9][A-Za-z0-9.-]+(:\d+)?(\/[^ ]*)?';

    if(preg_match_all('/'.$pattern.'/', $text, $matches)) {
        return $matches[0];
    }
}

/**
 * detect partial URLs in text
 *
 * @param   string  $text
 * @return  array
 */
function detectAllUrls($text)
{
    $pattern = '(http|https)(:\/\/)?+[^\s)]*';

    if(preg_match_all('/'.$pattern.'/', $text, $matches)) {
        return $matches[0];
    }
    
    $pattern = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
    if(preg_match_all($pattern, $text, $matches)) {
        return $matches[0];
    }
}

/**
 * remove URLs from text
 *
 * @param   string  $text
 * @return  string
 */
function removeUrls($text)
{
    $urls = detectURLs($text);
    if(!empty($urls)) {
        foreach($urls AS $key => $value) {
            $text = str_ireplace($value, '', $text);
        }
    }

    return trim($text);
}

/**
 * remove all URLs, including partial
 * ones from text
 *
 * @param   string  $text
 * @return  string
 */
function removePartialHttpUrls($text)
{
    $urls = detectPartialHttpUrls($text);
    if(!empty($urls)) {
        foreach($urls AS $key => $value) {
            $text = str_ireplace($value, '', $text);
        }
    }

    return trim($text);
}

/**
 * get_redirect_url()
 * Gets the address that the provided URL redirects to,
 * or FALSE if there's no redirect.
 *
 * @link    http://w-shadow.com/blog/2008/07/05/how-to-get-redirect-url-in-php/
 * @param   string  $url
 * @return  string
 */
function get_redirect_url($url)
{
	$redirect_url = null;

    if(!preg_match('/http/', $url) AND !preg_match('/https/', $url)) {
        $url = 'http://'.$url;
    }

	$url_parts = @parse_url($url);
	if (!$url_parts) return false;
	if (!isset($url_parts['host'])) {
        // can't process relative URLs
        return false;
	}
	if (!isset($url_parts['path'])) $url_parts['path'] = '/';

	$sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
	if (!$sock) return false;

	$request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
	$request .= 'Host: ' . $url_parts['host'] . "\r\n";
	$request .= "Connection: Close\r\n\r\n";
	fwrite($sock, $request);
	$response = '';
	while(!feof($sock)) $response .= fread($sock, 8192);
	fclose($sock);

	if (preg_match('/^Location: (.+?)$/m', $response, $matches)) {
		if ( substr($matches[1], 0, 1) == "/" )
			return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
		else
			return trim($matches[1]);

	} else {
		return false;
	}
}

/**
 * get_all_redirects()
 * Follows and collects all redirects, in order, for the given URL.
 *
 * @param   string  $url
 * @return  array
 */
function get_all_redirects($url)
{
	$redirects = array();
	while ($newurl = get_redirect_url($url)) {
		if (in_array($newurl, $redirects)) {
			break;
		}
		$redirects[] = $newurl;
		$url = $newurl;
	}
	return $redirects;
}

/**
 * get_final_url()
 * Gets the address that the URL ultimately leads to.
 * Returns $url itself if it isn't a redirect.
 *
 * @param   string $url
 * @return  string
 */
function get_final_url($url)
{
	$redirects = get_all_redirects($url);
	if (count($redirects) > 0) {
		return array_pop($redirects);
	} else {
		return $url;
	}
}

/**
 * fetch the response of a URL via cURL
 *
 * @param   string  $url
 * @param   boolean $returnCurlInfo
 * @param   int     $timeout
 * @param   int     $maxRedirs
 * @return  string
 */
function fetchUrlWithCurl($url, $returnCurlInfo = false, $timeout = 60, $maxRedirs = 10)
{
    if(!strlen($url)) {
        return;
    }

    $referers   = array('www.google.com',
                        'yahoo.com',
                        'msn.com',
                        'ask.com',
                        'live.com'
                    );
    $referer    = array_rand($referers);
    $referer    = 'http://' . $referers[$referer];

    $browsers   = array('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
                        'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3',
                        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0',
                        'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3',
                        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)',
                        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
                        'Googlebot/2.1 (+http://www.google.com/bot.html)'
                    );
    $browser    = array_rand($browsers);
    $browser    = $browsers[$browser];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERAGENT, $browser);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRedirs);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    if($returnCurlInfo) {
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    $response = curl_exec($ch);

    if($returnCurlInfo) {
        $originalResponse   = $response;
        $response           = array();
        $response['info']   = curl_getinfo($ch);
        $response['html']   = $originalResponse;
    }

    if($error = curl_error($ch)) {
        $response['error']      = $error;
        $response['errorno']    = curl_errno($ch);
    }

    curl_close($ch);

    return $response;
}

/**
 * fetch the final URL of a URL via cURL
 *
 * @param   string  $url
 * @param   boolean $returnCurlInfo
 * @param   int     $timeout
 * @param   int     $maxRedirs
 * @return  string
 */
function curl_fetch_final_url($url, $returnCurlInfo = false, $timeout = 60, $maxRedirs = 10)
{
    if(!strlen($url)) {
        return;
    }

    if(!preg_match('/http/', $url) AND !preg_match('/https/', $url)) {
        $url = 'http://'.$url;
    }

    $referers   = array('www.google.com',
                        'yahoo.com',
                        'msn.com',
                        'ask.com',
                        'live.com'
                    );
    $referer    = array_rand($referers);
    $referer    = 'http://' . $referers[$referer];

    $browsers   = array('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
                        'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3',
                        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0',
                        'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3',
                        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)',
                        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
                        'Googlebot/2.1 (+http://www.google.com/bot.html)'
                    );
    $browser    = array_rand($browsers);
    $browser    = $browsers[$browser];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERAGENT, $browser);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRedirs);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    if($returnCurlInfo) {
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    $response = curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

    if($returnCurlInfo) {
        $originalResponse   = $response;
        $response           = array();
        $response['info']   = curl_getinfo($ch);
        $response['html']   = $originalResponse;
    }

    if($error = curl_error($ch)) {
        $response['error']      = $error;
        $response['errorno']    = curl_errno($ch);
    }

    curl_close($ch);

    return $finalUrl;
}

/**
 * fetch the HTTP response code of a URL via cURL
 *
 * @param   string  $url
 * @param   int     $timeout
 * @param   int     $maxRedirs
 * @return  string
 */
function getHttpResponse($url, $timeout = 60, $maxRedirs = 10)
{
    if(!strlen($url)) {
        return;
    }

    $referers   = array('www.google.com',
                        'yahoo.com',
                        'msn.com',
                        'ask.com',
                        'live.com'
                    );
    $referer    = array_rand($referers);
    $referer    = 'http://' . $referers[$referer];

    $browsers   = array('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
                        'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3',
                        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0',
                        'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3',
                        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)'
                    );
    $browser    = array_rand($browsers);
    $browser    = $browsers[$browser];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $browser);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $maxRedirs);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);

    return curl_getinfo($ch, CURLINFO_HTTP_CODE);
}

/**
 * determine if the content type is an image
 *
 * @param   string  $contentType
 * @return  boolean
 */
function isImage($contentType)
{
    if(preg_match('/image/i', $contentType)) {
        return true;
    }
}

/**
 * determine if the content type is HTML
 *
 * @param   string  $contentType
 * @return  boolean
 */
function isHtml($contentType)
{
    if(preg_match('/html/i', $contentType)) {
        return true;
    }
}

/**
 * convert a unix timestamp to MySQL DATETIME
 *
 * @param   int     $timestamp
 * @return  string
 */
function timestamp_to_mysql_datetime($timestamp = null)
{
    $timestamp = is_null($timestamp) ? time() : $timestamp;
    return date('Y-m-d H:i:s', $timestamp);
}

/**
 * convert a unix timestamp to MySQL DATE
 *
 * @param   int     $timestamp
 * @return  string
 */
function timestamp_to_mysql_date($timestamp = null)
{
    $timestamp = is_null($timestamp) ? time() : $timestamp;
    return date('Y-m-d', $timestamp);
}

/**
 * convert a MySQL timestamp to a unix timestamp
 *
 * @param   int     $timestamp
 * @return  string
 */
function mysql_timestamp_to_unix_timestamp($timestamp = null)
{
    return strtotime($timestamp);
}

/**
 * flatten an array
 *
 * @link    http://chriswa.wordpress.com/2011/04/25/array_flatten-in-php/
 * @param   array   $array
 */
function flatten_array($array)
{
    return call_user_func_array('array_merge', $array);
}

/**
 * initialize the initial session values if
 * they are not stored in the end-user's session
 */
function setInitialSessionValues()
{

}

/**
 * str_word_count does not count numbers
 * this is a workaround
 *
 * @param   string  $text
 * @return  int
 */
function strWordCount($text)
{
    return count(explode(' ', $text));
}

/**
 * calculate percent increase / decrease
 *
 * @link    http://www.onemathematicalcat.org/algebra_book/online_problems/calc_percent_inc_dec.htm
 * @link    http://www.google.com/search?q=percent+increase+from+zero
 * @param   int     $start
 * @param   int     $end
 * @return  string
 */
function percentChange($start, $end)
{
    $start  = (int)$start;
    $end    = (int)$end;

    if($start == 0) {
        // infinity
        return 'INFINITY';
    }

    if($start < $end) {
        $change = (($start - $end) / $start) * 100;
        // remove the negative sign
        $change = str_replace('-', '', $change);
    } else {
        $change = (($end - $start) / $start) * 100;
    }

    $change = round($change, 2);

    return $change;
}

/**
 * determine if a number is negative
 *
 * @param   int     $number
 * @return  boolean
 */
function isNegativeNumer($number)
{
    if($number < 0) {
        return true;
    }
}

/**
 * determine if a string is JSON
 *
 * @param   string  $string
 * @return  boolean
 */
function is_json($string)
{
    return (is_string($string) && is_object(json_decode($string))) ? true : false;
}

/**
 * convert seconds to days
 *
 * @param   int     $seconds
 * @return  int
 */
function secondsToDays($seconds)
{
    // 86400 seconds = 24 hours
    $data = ($seconds / 86400);
    $data = floor($data);

    return $data;
}

/**
 * replace a word using preg_replace
 *
 * @link    http://chumby.net/?p=44
 * @param   string  $needle
 * @param   string  $replacement
 * @param   string  $haystack
 * @param   boolean $caseInsensitive
 * @return  string  $haystack
 */
function str_replace_word($needle, $replacement, $haystack, $caseInsensitive = true)
{
    $needle     = str_replace('/', '\/', $needle);
    $needle     = str_replace('(', '\(', $needle);
    $needle     = str_replace(')', '\)', $needle);

    if($caseInsensitive) {
        $pattern = "/\b".$needle."\b/i";
    } else {
        $pattern = "/\b".$needle."\b";
    }
    $haystack   = preg_replace($pattern, $replacement, $haystack);

    return $haystack;
}

/**
 * return JSONP data
 *
 * @param   string  $callback
 * @param   string  $json
 * @return  string
 */
function outputJsonP($callback, $json)
{
    return $callback.'('.$json.');';
}

/**
 * signal handler function
 *
 * @link    http://www.php.net/manual/en/function.pcntl-signal.php
 * @link    http://tuxradar.com/practicalphp/16/1/6
 * @link    http://www.php.net/manual/en/pcntl.constants.php
 * @link    http://www.php.net/manual/en/pcntl.example.php
 * @param   string  $signo
 */
function signal_handler($signo)
{
    switch ($signo) {
        case SIGCHLD:
            while (pcntl_waitpid(0, $status) != -1) {
                $status = pcntl_wexitstatus($status);
                echo "Child ".$status." completed\n";
            }

            exit;
            break;

        case SIGTERM:
            // handle shutdown tasks
            exit;
            break;

         case SIGHUP:
            // handle restart tasks
            break;

         case SIGUSR1:
            echo "Caught SIGUSR1...\n";
            break;

         default:
             // handle all other signals
     }
}

/**
 * determine the time elapsed since a Unix timestamp
 *
 * @param   int     $timestamp
 * @return  string
 */
function elapsedTime($timestamp, $returnAgo = false)
{
    $difference = time() - $timestamp;

    // if more than a year ago
    if ($difference >= 60*60*24*365) {
        $int    = intval($difference / (60*60*24*365));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' year' . $s;
    // if more than five weeks ago
    } elseif ($difference >= 60*60*24*7*5) {
        $int    = intval($difference / (60*60*24*30));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' month' . $s;
    // if more than a week ago
    } elseif ($difference >= 60*60*24*7) {
        $int    = intval($difference / (60*60*24*7));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' week' . $s;
    // if more than a day ago
    } elseif ($difference >= 60*60*24) {
        $int    = intval($difference / (60*60*24));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' day' . $s;
    // if more than an hour ago
    } elseif ($difference >= 60*60) {
        $int    = intval($difference / (60*60));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' hour' . $s;
    // if more than a minute ago
    } elseif($difference >= 60) {
        $int    = intval($difference / (60));
        $s      = ($int > 1) ? 's' : '';
        $r      = $int . ' minute' . $s;
    // if less than a minute ago
    } else {
        $r = 'moments';
    }

    if($returnAgo) {
        $r .= ' ago';
    }

    return $r;
}

function get_elapsed_time($ts, $datetime = true)
{
    if($datetime) {
        $ts = date('U', strtotime($ts));
    }

    $mins   = floor((time() - $ts) / 60);
    $hours  = floor($mins / 60);
    $mins  -= $hours * 60;
    $days   = floor($hours / 24);
    $hours -= $days * 24;
    $months = floor($days / 30);
    $weeks  = floor($days / 7);
    $days  -= $weeks * 7;
    $t      = '';

    if ($months > 0) {
        return $months.' month' . ($months > 1 ? 's ago' : ' ago');
    }

    if ($weeks > 0) {
        return $weeks.' week' . ($weeks > 1 ? 's ago' : ' ago');
    }

    if ($days > 0) {
        return $days.' day' . ($days > 1 ? 's ago' : ' ago');
    }

    if ($hours > 0) {
        return $hours. ' hour' . ($hours > 1 ? 's ago' : ' ago');
    }

    if ($mins > 0) {
        return $mins. ' min' . ($mins > 1 ? 's ago' : ' ago');
    }

    return '< 1 min';
}

/**
 * return the first element of an array
 *
 * @param   array    $array
 * @return  mixed
 */
function fetchFirstArrayElement($array)
{
    return array_shift( $array );
}

/**
 * Replace all linebreaks with one whitespace.
 *
 * @link    http://www.php.net/manual/en/function.str-replace.php#97374
 * @access  public
 * @param   string    $string The text to be processed.
 * @return  string   The given text without any linebreaks.
 */
function remove_newlines($string)
{
    return (string)str_replace(array("\r", "\r\n", "\n"), '', $string);
}

/**
 * determine if a script is running by name
 *
 * @param   string    $scriptName
 * @return  boolean
 */
function isScriptRunningByName($scriptName)
{
    $psArray    = array();
    $output     = array();
    $return     = '';
    $ps         = exec('pgrep -f '.$scriptName, $psArray, $return);
    $count      = count($psArray);

    if(empty($psArray) OR $count < 1) {
        return false;
    } else {
        return true;
    }
}

/**
 * determine if a script is running with specific command-line paramters
 *
 * @param   string    $scriptName
 * @return  boolean
 */
function isScriptRunningWithArgs($scriptName)
{
    $psArray    = array();
    $output     = array();
    $return     = '';
    $ps         = exec('ps -fp $(pgrep -d, -x php)', $psArray, $return);

    if(!empty($psArray)) {
        foreach($psArray AS $key => $value) {
            if( preg_match('/'.$scriptName.'/', $value) ) {
                return true;
            }
        }
    }
}

function fetchScriptRunCount($scriptName)
{
    $psArray = fetchScriptPids($scriptName);
    if( !empty($psArray) ) {
        $myPid = getmypid();
        // remove self
        foreach($psArray AS $key => $value) {
            if($value == $myPid) {
                unset($psArray[$key]);
            }
        }

        return count($psArray);
        
    } else {
        return 0;
    }
}

function fetchScriptPids($scriptName)
{
    $psArray    = array();
    $output     = array();
    $return     = '';
    $ps         = exec("ps -eo pid,command | grep ".$scriptName." | grep -v grep | grep -v /bin/sh | grep -v 'sh -c' | awk '{print $1}'", $psArray, $return);

    return $psArray;
}

function isNegative($int)
{
    if(preg_match('/-/', $int)) {
        return true;
    }
}

/**
 * a recursive function which adds the values of two multidimensional
 * arrays with the same key structure:
 *
 * @author  George Pligor
 * @link    http://www.php.net/manual/en/function.array-sum.php#104222
 * @param   array   $left
 * @param   array   $right
 * @return  array
 */
function multiDimArrayAdd(& $left, $right)
{
    if(is_array($left) && is_array($right)) {
        foreach($left as $key => $val) {
            if( is_array($val) ) {
                multiDimArrayAdd($left[$key], $right[$key]);
            }
            $left[$key] += $right[$key];
        }
    }
}

/**
 * determine if a URL exists within a string
 *
 * @link    http://daringfireball.net/2009/11/liberal_regex_for_matching_urls
 * @link    http://stackoverflow.com/questions/3539009/preg-match-to-domain-tld
 * @param   string  $string
 * @return  boolean
 */
function containsUrl($string)
{
    if(preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $string, $match)) {
        return true;
    }

    if (preg_match('/^[-a-z0-9]+\.a[cdefgilmnoqrstuwxz]|b[abdefghijmnorstvwyz]|c[acdfghiklmnoruvxyz]|d[ejkmoz]|e[cegrstu]|f[ijkmor]|g[abdefghilmnpqrstuwy]|h[kmnrtu]|i[delmnoqrst]|j[emop]|k[eghimnprwyz]|l[abcikrstuvy]|m[acdeghklmnopqrstuvwxyz]|n[acefgilopruz]|om|p[aefghklmnrstwy]|qa|r[eosuw]|s[abcdeghijklmnortuvyz]|t[cdfghjklmnoprtvwz]|u[agksyz]|v[aceginu]|w[fs]|y[et]|z[amw]|biz|cat|co|com|edu|gov|int|mil|net|org|pro|tel|aero|arpa|asia|to|tv|coop|info|jobs|mobi|name|museum|travel|arpa|xn--[a-z0-9]+$/', strtolower($string))) {
        return true;
    }

}

/**
 * remove an item from an array by value
 *
 * @link    http://dev-tips.com/featured/remove-an-item-from-an-array-by-value
 * @param   string  $value
 * @param   array   $array
 * @return  array
 */
function removeArrayElementByValue($value, $array)
{
    return array_diff( $array, array($value) );
}

/**
 * determine the run environment based on the server IP
 *
 * @return  string
 */
function determineRunEnvironment()
{
	$config = parse_ini_file( BASEDIR.'/application/configs/config.ini', true );
	
	if( strlen( $config['env']['run_env'] ) ) {
		return $config['env']['run_env'];
	}

    return 'UNKNOWN';
}

/**
 * set the run environment
 */
function setRunEnvironment($env)
{
    if( !defined('RUN_ENV') ) {
        define('RUN_ENV', $env);
    }
}

function fetchLocalServerIP()
{
    $ip = ( isset($_SERVER['SERVER_ADDR']) ) ? $_SERVER['SERVER_ADDR'] : fetchLocalServerIPViaBash();
    return $ip;
}

function fetchLocalServerIPViaBash()
{
    exec("ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'", $output, $return);
    if( !empty($output) ) {
        return $output[0];
    }
}

function determineUserLocale()
{
    $locale = new Zend_Locale();
    return $locale->getLanguage();
}

function preserveFormat()
{
	echo '<pre>';	
}

function file_upload_error( $errorCode )
{
	switch ( $errorCode ) {
		case UPLOAD_ERR_OK:
			$response = 'UPLOAD_ERR_OK';
			break;
			
		case UPLOAD_ERR_INI_SIZE:
			$response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			break;
		case UPLOAD_ERR_FORM_SIZE:
			$response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			break;
		case UPLOAD_ERR_PARTIAL:
			$response = 'The uploaded file was only partially uploaded.';
			break;
		case UPLOAD_ERR_NO_FILE:
			$response = 'No file was uploaded.';
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$response = 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.';
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$response = 'Failed to write file to disk. Introduced in PHP 5.1.0.';
			break;
		case UPLOAD_ERR_EXTENSION:
			$response = 'File upload stopped by extension. Introduced in PHP 5.2.0.';
			break;
		default:
			$response = 'Unknown error';
			break;
	}

	return $response;
}

/**
 * determine if a file is an image
 *
 * @link	www.php.net/manual/en/function.image-type-to-mime-type.php#refsect1-function.image-type-to-mime-type-returnvalues
 * @param	string	$filePath
 * @return  boolean	
 */
function is_image( $filePath )
{
	$size = getimagesize( $filePath );
	if( !$size ) {
		return false;
	}
	
	$validImageTypes = array(	IMAGETYPE_GIF,
								IMAGETYPE_JPEG,
								IMAGETYPE_PNG,
								IMAGETYPE_SWF,
								IMAGETYPE_PSD,
								IMAGETYPE_BMP,
								IMAGETYPE_TIFF_II,
								IMAGETYPE_TIFF_MM,
								IMAGETYPE_JPC,
								IMAGETYPE_JP2,
								IMAGETYPE_JPX,
								IMAGETYPE_JB2,
								IMAGETYPE_SWC,
								IMAGETYPE_IFF,
								IMAGETYPE_WBMP,
								IMAGETYPE_XBM,
								IMAGETYPE_ICO								
	);
	
	if( in_array( $size[2],  $validImageTypes ) ) {
		return true;
	} else {
		return false;
	}	
}

function is_video( $filePath )
{
	$type = mime_content_type( $filePath );

	if( preg_match('/video/', $type ) ) {
		return true;	
	}
	
	return false;
}

function isAllowedFileType( $fileExt )
{	
	if( trim( SITE_ALLOWED_FILE_TYPES ) == '*' ) {
		return true;	
	}
	
	$allowedFileTypes = array_map('trim', explode( ',', SITE_ALLOWED_FILE_TYPES ) );
	
	if( !in_array( $fileExt, $allowedFileTypes ) ) {
		return false;
	} else {
		return true;
	}	
}

/**
 * determine if a media type is allowed
 * for upload 
 *
 * @param	string	$filePath
 * @return  boolean
 */
function isAllowedMediaType( $fileExt )
{
	// trim spaces
	$allowedImageTypes = array_map('trim', explode( ',', SITE_ALLOWED_IMAGE_TYPES ) );
	$allowedVideoTypes = array_map('trim', explode( ',', SITE_ALLOWED_VIDEO_TYPES ) );	
	
	if( !in_array( $fileExt, $allowedImageTypes ) AND !in_array( $fileExt, $allowedVideoTypes ) ) {
		return false;	
	} else {
		return true;		
	}
}