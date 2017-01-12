<?php
/**
 * User: Raaghu
 * Date: 17-09-2015
 * Time: PM 10:22
 */

namespace PhpPlatform\Config;

use PhpPlatform\JSONCache\Cache;

/**
 * Singleton implementation for caching the settings
 * 
 * @author raghavendra
 */
final class SettingsCache extends Cache{

    private static $cacheObj = null;
    protected $cacheFileName = "settingscache236512233125"; // cache for settings

    public static function getInstance(){
        if(self::$cacheObj == null){
            self::$cacheObj = new SettingsCache();
        }
        return self::$cacheObj;
    }
}

?>
