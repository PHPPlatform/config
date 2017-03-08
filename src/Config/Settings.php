<?php
/**
 * User: Raaghu
 * Date: 22-09-2015
 * Time: PM 10:36
 */

namespace PhpPlatform\Config;


class Settings{
	
	private static function getVendorDir(){
		$configPackageHome = dirname(dirname(__DIR__));
		if(is_dir($configPackageHome.'/vendor')){
			$vendorDir = $configPackageHome.'/vendor';
		}else if(is_dir($configPackageHome.'/../../../vendor')){
			$vendorDir = $configPackageHome.'/../../../vendor';
		}else{
			throw new \Exception('Unable to find vendor directory');
		}
		return $vendorDir;
	}
	
	

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
    	$settings = SettingsCache::getInstance()->getData($package_);
        if($settings === NULL){

        	$vendorDir = self::getVendorDir();
        	
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
            foreach (array_reverse(preg_split('/\/|\\\/', $package)) as $packagePath){
            	$absoluteSettings = array($packagePath=>$absoluteSettings);
            }
            SettingsCache::getInstance()->setData($absoluteSettings);
        }

        if(is_string($setting)){
            $settings = SettingsCache::getInstance()->getData($package_.".".$setting);
        }

        return $settings;

    }
    
}

