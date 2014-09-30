<?php
/**
 * White Label Transfer
 * Installer, Constants
 *
 * @author      BizLogic.com <dev@whitelabeltransfer.com>
 * @license     GNU Affero General Public License v3
 * @link        http://whitelabeltransfer.com
 *
 * @since       Thursday, October 22, 2009 / 12:05 PM GMT+1 mknox
 * @edited      $Date: 2011-05-09 08:14:43 +0200 (Mon, 09 May 2011) $ $Author: mknox $
 * @version     $Revision: 4 $
 *
 * @package     White Label Transfer
 */

define('BASEDIR', dirname( dirname( __FILE__ ) ) );
define('ROOT_DIR', dirname( BASEDIR ) );
define('BASEURL', fetchServerURL() );
define('DEFAULT_PRELOADER_IMAGE', BASEURL.'/img/preloader/default.gif');
define('SMARTY_CACHE_DIR', BASEDIR.'/templates_cache' );
define('SMARTY_COMPILE_DIR', BASEDIR.'/templates_c' );
define('SMARTY_TEMPLATE_BASEDIR', BASEDIR.'/templates/' );
define('SMARTY_TEMPLATENAME','default' );
define('SMARTY_TEMPLATE_DIR', SMARTY_TEMPLATE_BASEDIR.SMARTY_TEMPLATENAME );
define('SMARTY_TEMPLATE_HTML', SMARTY_TEMPLATE_DIR.SMARTY_TEMPLATENAME.'/html' );
define('SMARTY_TEMPLATE_ROOT', BASEURL.'/templates/'.SMARTY_TEMPLATENAME );
define('SMARTY_TEMPLATE_CSS', BASEURL.'/templates/'.SMARTY_TEMPLATENAME.'/css' );
define('SMARTY_TEMPLATE_IMG', BASEURL.'/templates/'.SMARTY_TEMPLATENAME.'/images' );
define('SMARTY_TEMPLATE_JS', BASEURL.'/templates/'.SMARTY_TEMPLATENAME.'/js' );
define('THIS_URL', curPageURL() );
define('CURRENT_SCRIPT', ltrim( $_SERVER['SCRIPT_NAME'],'/' ) );
define('DEFAULT_JQUERY_UI_THEME', 'Delta');
define('INSTALL_SQL', ROOT_DIR.'/scripts/build/db/wt_clone.sql');