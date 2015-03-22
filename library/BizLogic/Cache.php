<?php
/**
 * White Label Transfer
 * Class to Extend Zend_Cache
 *
 * @author      BizLogic <hire@whitelabeltransfer.com>
 * @copyright   2012 - 2015 BizLogic
 * @link        http://whitelabeltransfer.com
 * @link        http://bizlogicdev.com
 * @license     GNU Affero General Public License v3
 *
 * @since       Tuesday, November 27, 2012, 08:00 AM GMT+1 mknox
 * @edited      $Date: 2012-01-06 16:30:53 +0100 (Fri, 06 Jan 2012) $ $Author: mknox $
 * @version     $Id: Cache.php 5997 2012-01-06 15:30:53Z mknox $
 *
 * @package     White Label Transfer
*/

class BizLogic_Cache extends Zend_Cache
{
    /**
     * setup Zend_Cache
     *
     * @link    http://framework.zend.com/manual/en/zend.cache.introduction.html
     * @param   int     $lifetime
     * @param   string  $cacheObj
     */
    public function setupCache( $lifetime, $cacheObj )
    {
        $frontendOptions = array(
            'lifetime' => $lifetime,
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => ROOT_DIR.'/data/cache/'            
        );

        $cache = Zend_Cache::factory(   
            'Core', 
            'File',
            $frontendOptions,
            $backendOptions
        );

        Zend_Registry::set( $cacheObj, $cache );
    }
}