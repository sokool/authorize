<?php
namespace FloTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{
    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;

    public static function init()
    {
        static::initOldFlo();

        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/TestConfig.php')) {
            $testConfig = include __DIR__ . '/TestConfig.php';
        } else {
            $testConfig = include __DIR__ . '/TestConfig.php.dist';
        }

        $zf2ModulePaths = array();

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader($testConfig['modules']);

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        static::$serviceManager = $serviceManager;
        static::$config         = $config;
    }

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    protected static function initAutoloader($modules = [])
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        } else {
            $zf2Path = getenv('ZF2_PATH') ? : (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        }

        $namespaces = [];
        foreach ($modules as $moduleName) {
            $path = APPLICATION_ROOT . '/flo-new-age/module/' . $moduleName . '/test';
            if (!is_dir($path)) {
                continue;
            }
            $namespaces[$moduleName . 'Test'] = $path;
        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces'      => $namespaces
            ),
        ));
    }

    protected static function findParentPath($path)
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }

    protected static function initOldFlo()
    {

        $dirName = '../../../..';

        set_include_path(
            get_include_path() . PATH_SEPARATOR .
            $dirName . '/lib/' . PATH_SEPARATOR .
            $dirName . '/lib/semlib/' . PATH_SEPARATOR .
            $dirName . '/lib/mpdf/mpdf' . PATH_SEPARATOR .
            $dirName . '/lib/zendframework/zendframework1/library' . PATH_SEPARATOR .
            $dirName . '/AboveWeb/' . PATH_SEPARATOR .
            $dirName . '/AboveWeb/App/');

        include_once $dirName . '/AboveWeb/App/EtipsApplication.php';
        include_once $dirName . '/lib/semlib/utilities.php';
        include_once $dirName . '/AboveWeb/application_config.inc.php';
        include_once $dirName . '/AboveWeb/App/system_manager.class.php';
        include_once $dirName . '/AboveWeb/App/user.class.php';

        $application = \EtipsApplication::init(getenv('environment'));

        if (extension_loaded('test_helpers')) {
            set_exit_overload(function () {
                return false;
            });
        }
    }
}

Bootstrap::init();