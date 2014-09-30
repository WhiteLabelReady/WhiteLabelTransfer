<?php
/**
 * Constants
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2013 BizLogic
 * @link        http://whitelabeltransfer.com
 *
 * @since       Saturday, April 23, 2011 / 02:00 PM GMT+1
 * @version     $Id: constants.php 50 2013-10-10 00:10:30Z dev@whitelabeltransfer.com $
 * @edited      $Date: 2013-10-10 02:10:30 +0200 (Do, 10 Okt 2013) $
 *
 * @package     White Label Transfer
 */

define( 'BASE_DIR', dirname( dirname( dirname(__FILE__) ) ) );
define( 'BASEDIR', BASE_DIR );
define( 'BASE_URL', fetchServerURL() );
define( 'BASEURL', BASE_URL );

$protocolRelativeUrl = str_replace('http://', '//', BASEURL );
$protocolRelativeUrl = str_replace('https://', '//', $protocolRelativeUrl );
define( 'PROTOCOL_RELATIVE_URL', $protocolRelativeUrl );

define( 'ADMIN_URL', BASEURL.'/admin' );
define( 'IMG_URL', BASEURL.'/img' );
define( 'IMGURL', IMG_URL );
define( 'ROOT_DIR', BASEDIR );
define( 'LOG_DIR', ROOT_DIR.'/data/logs' );
define( 'TMP_DIR', ROOT_DIR.'/data/temp' );
define( 'APP_DIR', ROOT_DIR.'/application' );
define( 'MODULES_DIR', APP_DIR.'/modules' );
define( 'VIEWS_DIR', MODULES_DIR.'/public/views/scripts' );
define( 'PARTIAL_TEMPLATE_DIR', APP_DIR.'/modules/public/views/templates/shared/partials' );
define( 'SHARED_TEMPLATE_DIR', APP_DIR.'/modules/public/views/templates/shared/scripts' );
define( 'IS_MOBILE', is_mobile() );
define( 'IS_TABLET', is_tablet() );
define( 'ENABLE_FIREPHP', true );