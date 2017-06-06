<?php
/**
 * User: Raaghu
 * Date: 25-09-2015
 * Time: AM 11:57
 */

namespace PhpPlatform\Tests\Config;

use PhpPlatform\Config\Settings;
use PhpPlatform\Config\SettingsCache;

class TestSettings extends \PHPUnit_Framework_TestCase{
	
	static $thisPackageName = 'php-platform/config';
	
    public function testGetSettings0(){
        $isException = false;
        try{
            Settings::getSettings('PHPPlatform/dummyPackage');
        }catch (\Exception $e){
            $isException = true;
            $this->assertEquals("No configuration found for the package PHPPlatform/dummyPackage",$e->getMessage());
        }
        $this->assertTrue($isException);
    }
    
    
    public function testGetSettings1(){
    	// prepare data
    	
    	$vendorDir = $this->getVendorDir();
    	$thisPackageConfigFile = dirname($vendorDir).'/config.json';
    	
    	SettingsCache::getInstance()->reset();
    	
    	$setting = array("test"=>array("my"=>array("settings"=>array(1,array("here"=>"as a array"),3))));
    	file_put_contents($thisPackageConfigFile, json_encode($setting));
    	
    	
    	$actualSetting = Settings::getSettings(self::$thisPackageName);
    	$this->assertEquals($setting,$actualSetting);
    	
    	
    	$actualSetting = Settings::getSettings(self::$thisPackageName,"test.my");
    	$this->assertEquals($setting["test"]["my"],$actualSetting);
    	
    	$actualSetting = Settings::getSettings(self::$thisPackageName,"test.my.settings[0]");
    	$this->assertEquals($setting["test"]["my"]["settings"][0],$actualSetting);
    	 
    	$actualSetting = Settings::getSettings(self::$thisPackageName,"test.my.settings[0].key");
    	$this->assertEquals(null,$actualSetting);
    	 
    	$actualSetting = Settings::getSettings(self::$thisPackageName,"test.my.settings[1].here");
    	$this->assertEquals($setting["test"]["my"]["settings"][1]["here"],$actualSetting);
    	
    	
    	// clear data
    	unlink($thisPackageConfigFile);
    }
    
    public function testGetSettings2(){
    	// prepare data
    	$vendorDir = $vendorDir = $this->getVendorDir();
    	
    	$packageName = "php-platform/testconfig123456";
    	
    	$packageConfigFile = $vendorDir.'/'.$packageName.'/config.json';
    	
    	if(!is_dir(dirname($packageConfigFile))){
    		mkdir(dirname($packageConfigFile),"0777",true);
    	}
    	
    	SettingsCache::getInstance()->reset();
    	
    	$setting = array("test"=>array("my"=>array("settings"=>array(1,array("here"=>"as a array"),3))));
    	file_put_contents($packageConfigFile, json_encode($setting));
    	 
    	$actualSetting = Settings::getSettings($packageName);
    	$this->assertEquals($setting,$actualSetting);
    	 
    	 
    	$actualSetting = Settings::getSettings($packageName,"test.my");
    	$this->assertEquals($setting["test"]["my"],$actualSetting);
    	 
    	$actualSetting = Settings::getSettings($packageName,"test.my.settings[0]");
    	$this->assertEquals($setting["test"]["my"]["settings"][0],$actualSetting);
    
    	$actualSetting = Settings::getSettings($packageName,"test.my.settings[0].key");
    	$this->assertEquals(null,$actualSetting);
    
    	$actualSetting = Settings::getSettings($packageName,"test.my.settings[1].here");
    	$this->assertEquals($setting["test"]["my"]["settings"][1]["here"],$actualSetting);
    	 
    	 
    	// clear data
    	unlink($packageConfigFile);
    	rmdir(dirname($packageConfigFile));
    }
    
    private function getVendorDir(){
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
    
    
}