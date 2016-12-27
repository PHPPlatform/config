<?php
/**
 * User: Raaghu
 * Date: 17-09-2015
 * Time: PM 10:22
 */

namespace PhpPlatform\Config;

/**
 * Singleton implementation for caching the settings
 * 
 * @author raghavendra
 */
final class SettingsCache{

	private static $cacheObj = null;
	
	private $settings = array();
	private $cacheFileName = "e412523shyugtr345";
	
	
	private function __construct(){
		$this->cacheFileName = sys_get_temp_dir()."/".$this->cacheFileName;
		if(is_file($this->cacheFileName)){
			$fileContents = file_get_contents($this->cacheFileName);
			$this->settings = json_decode($fileContents,true);
			if($this->settings === NULL){
				$this->settings = array();
			}
		}
	}
	
	/**
	 * @return \PhpPlatform\Config\SettingsCache
	 */
	public static function getInstance(){
		if(self::$cacheObj == null){
			self::$cacheObj = new SettingsCache();
		}
		return self::$cacheObj;
	}
	
    /**
     * @param array $path , key path for finding the settings in cache
     * @return NULL|mixed
     */
    function getData(array $path) {
    	if(is_array($path) && count($path) > 0){
    	    $value = $this->settings;
    	    foreach($path as $pathElem){
				if (isset($value[$pathElem])) {
					$value = $value[$pathElem];
				} else {
					return NULL;
				}
			}
    		return $value;
    	}
    	return NULL;
    }

    /**
     * @param array $setting , setting to merge with current settings
     * @return boolean, TRUE on success , FALSE on failure
     */
    function setData(array $setting) {
    	$originalSettings = $this->settings;
    	$this->settings = array_merge_recursive($this->settings,$setting);
    	$jsonSettings = json_encode($this->settings);
    	if($jsonSettings === FALSE){
    		return FALSE;
    	}
    	if(file_put_contents($this->cacheFileName, $jsonSettings) === FALSE){
    		$this->settings = $originalSettings;
    		return FALSE;
    	}
    	return TRUE;
    }
    
    /**
     * resets complete cache to empty
     * @return boolean, TRUE on success , FALSE on failure
     */
    private function resetCache(){
    	$originalSettings = $this->settings;
    	$this->settings = array();
    	if(file_put_contents($this->cacheFileName, "{}") === FALSE){
    		$this->settings = $originalSettings;
    		return FALSE;
    	}
    	return TRUE;
    }
    
    public static function reset(){
    	self::getInstance()->resetCache();
    }

}

?>
