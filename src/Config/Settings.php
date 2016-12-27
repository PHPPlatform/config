<?php
/**
 * User: Raaghu
 * Date: 22-09-2015
 * Time: PM 10:36
 */

namespace PhpPlatform\Config;

use Composer\Autoload\ClassLoader;

class Settings{

    /**
     * this method retrieves settings of a package
     * settings are saved as config.json file in package root directory
     *
     * @param string $package Name of the package
     * @param string|null $setting setting to retrieve , Optional
     * @return mixed returns settings array
     * @throws \Exception if settings not found
     */
    public static function getSettings($package,$setting = null){

    	$package_ = preg_replace('/\/|\\\\/', ".", $package);
    	$packagePaths = self::getPaths($package_);
        $settings = SettingsCache::getInstance()->getData($packagePaths);
        if($settings === NULL){
            $classLoaderReflection = new \ReflectionClass(new ClassLoader());
            $vendorDir = dirname(dirname($classLoaderReflection->getFileName()));

            $packageConfigFile = $vendorDir.'/'.$package.'/config.json';

            if(!file_exists($packageConfigFile)){
            	// if no config.json in vendor directory , 
            	// try to find the current package's config.json
            	$thisPackageRoot = dirname($vendorDir);
            	while(!file_exists($thisPackageRoot.'/composer.json')){
            		$thisPackageRoot_ = dirname($thisPackageRoot);
            		if($thisPackageRoot == $thisPackageRoot_){
            			throw new \Exception("No configuration found for the package $package");
            		}
            		$thisPackageRoot = $thisPackageRoot_;
            	}
            	$composerJson = json_decode(file_get_contents($thisPackageRoot.'/composer.json'),true);
            	if($composerJson["name"] == $package){
            		$packageConfigFile = $thisPackageRoot.'/config.json';
            	}
            	if(!file_exists($packageConfigFile)){
            		throw new \Exception("No configuration found for the package $package");
            	}
            }

            $settings = json_decode(file_get_contents($packageConfigFile),true);
            
            $absoluteSettings = $settings;
            foreach (array_reverse($packagePaths) as $packagePath){
            	$absoluteSettings = array($packagePath=>$absoluteSettings);
            }
            SettingsCache::getInstance()->setData($absoluteSettings);
        }

        if(is_string($setting)){
            $settingPaths = self::getPaths($package_.".".$setting);
            $settings = SettingsCache::getInstance()->getData($settingPaths);
        }

        return $settings;

    }
    
    /**
     * this method get the path array from string key
     * @param string $key
     */
    private static function getPaths($key){
    	$paths = array();
    	$sourcePaths = explode(".", $key);
    	foreach ($sourcePaths as $sourcePath){
    		$subPaths = preg_split('/\[(.*?)\]/',$sourcePath,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    		if($subPaths !== FALSE){
    			$paths = array_merge($paths,$subPaths);
    		}
    	}
    	return $paths;
    }

}

