<?php
/**
 * White Label Transfer
 * Index
 *
 * @author      BizLogic <code@whitelabeltransfer.com>
 * @copyright   2012 - 2014 BizLogic
 * @link        http://whitelabeltransfer.com
 * @license     GNU Affero General Public License v3
 * @link        https://www.gnu.org/licenses/agpl-3.0.txt
 *
 * @since       Wednesday, July 06, 2011 / 10:17 AM GMT+1
 * @edited      $Date: 2011-11-03 14:59:43 +0100 (Thu, 03 Nov 2011) $
 * @version     $Id: index.php 4898 2011-11-03 13:59:43Z mknox $
 *
 * @package     White Label Transfer
*/

define( 'PATH', dirname(__FILE__) );
set_include_path(   
    PATH.'/application/'.PATH_SEPARATOR.
    PATH.'/application/configs'.PATH_SEPARATOR.
    PATH.'/application/models'.PATH_SEPARATOR.
    PATH.'/library/'.PATH_SEPARATOR.
    get_include_path()
 );

require_once('Bootstrap.php');
$Bootstrap = new Bootstrap('');
$Bootstrap->run();