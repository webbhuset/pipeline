<?php

class Webbhuset_Bifrost_Autoload
{
    /**
     * Holds basedirs for namespaces.
     *
     * @var array
     * @access protected
     */
    static protected $_baseDirs = array();

    /**
     * Register a namespace base dir for autoloading.
     *
     * @param string $namespace
     * @param string $dir
     * @static
     * @access public
     * @return void
     */
    static public function registerBase($namespace, $dir)
    {
        $nameArr = explode('\\', $namespace);
        $currentDir = &self::$_baseDirs;
        foreach ($nameArr as $name) {
            if (!isset($currentDir[$name])) {
                $currentDir[$name] = array();
            }
            $currentDir = &$currentDir[$name];
        }

        $currentDir = $dir;
    }

    /**
     * Register spl autoload function.
     *
     * @static
     * @access public
     * @return void
     */
    static public function register()
    {
        spl_autoload_register(array('Webbhuset_Bifrost_Autoload', 'autoload'), true, true);
    }

    /**
     * Autoload function.
     *
     * @param string $class
     * @static
     * @access public
     * @return boolean
     */
    static public function autoload($class)
    {
        $classArr   = explode('\\', $class);
        $currentDir = self::$_baseDirs;
        $filename   = '';
        $isFound    = false;
        foreach ($classArr as $name) {
            if ($isFound) {
                $filename .= DIRECTORY_SEPARATOR . $name;
                continue;
            }
            if (isset($currentDir[$name])) {
                $currentDir = $currentDir[$name];
            } elseif (isset($currentDir['*'])) {
                $currentDir = $currentDir['*'];
                $filename .= DIRECTORY_SEPARATOR . $name;
            }
            if (is_string($currentDir)) {
                $isFound = true;
            }
        }
        if (is_string($currentDir)) {
            $file = $currentDir . $filename . '.php';

            if (!is_file($file)) {
                return false;
            }
            include $file;

            return true;
        }

        return false;
    }
}

Webbhuset_Bifrost_Autoload::registerBase('Webbhuset\Bifrost\Core', Mage::getBaseDir('lib') . '/Webbhuset/Bifrost');
Webbhuset_Bifrost_Autoload::register();
