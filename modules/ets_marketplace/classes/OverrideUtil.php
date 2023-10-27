<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
class Ets_mp_overrideUtil
{
    /**
     * @var array[]
     */
    static public $conflict = [
        [
            'class' => 'Carrier',
            'file' => 'classes/Carrier.php',
            'method' => 'getAvailableCarrierList',
            'conflicts' => ['ets_marketplace', 'ets_onepagecheckout'],
            'priority' => [
                0 => 'ets_marketplace',
                1 => 'ets_onepagecheckout',
            ]
        ],
        [
            'class' => 'Cart',
            'file' => 'classes/Cart.php',
            'method' => 'getPackageList',
            'conflicts' => ['ets_marketplace', 'ets_onepagecheckout'],
            'priority' => [
                0 => 'ets_marketplace',
                1 => 'ets_onepagecheckout',
            ]
        ],
        [
            'class' => 'Cart',
            'file' => 'classes/Cart.php',
            'method' => 'getPackageShippingCost',
            'conflicts' => ['ets_marketplace','ets_shippingcost', 'ets_onepagecheckout'],
            'priority' => [
                0 => 'ets_marketplace',
                1=> 'ets_shippingcost',
                2 => 'ets_onepagecheckout',
            ]
        ],
    ];
    static public $replaced = [];

    /**
     * @param \Module $module
     * @param  string $filePath
     * @return string
     */
    public static function getOverrideFilePath(Module $module, $filePath)
    {
        return $module->getLocalPath() . 'override/' . $filePath;
    }

    public static function installOverride($modules)
    {
        foreach ($modules as $module) {
            if($module instanceof \Module){
                $module->installOverrides();
            }else{
                Module::getInstanceByName($module)->installOverrides();
            }
        }
    }

    /**
     * Run before enable module
     * @param \Module $currentModule
     * @throws \ReflectionException
     */
    public static function resolveConflict(Module $currentModule)
    {
        $curModuleName = $currentModule->name;
//            self::$replaced = [];
        $uninstalled = $toInstall = [];
        foreach (self::$conflict as $item) {
            if(in_array($curModuleName, $item['conflicts'])){
                $matchedByPriority = null;
                foreach ($item['priority'] as $i => $name) {
                    if($name != $curModuleName){
                        if(Module::isEnabled($name)){
                            $matchedByPriority = $name;
                        }
                    }else{
                        if($matchedByPriority === null){
                            $matchedByPriority = $curModuleName;
                        }
                    }
                    if($matchedByPriority !== null){
                        break;
                    }
                }
                if($matchedByPriority !== null){
                    $reverse = array_reverse($item['priority'], true);
                    foreach ($reverse as $i => $name) {
                        if($name != $curModuleName && Module::isEnabled($name)){
                            $instance = Module::getInstanceByName($name);
                            if(!in_array($name, $uninstalled)){
                                $instance->uninstallOverrides();
                                $uninstalled[] = $name;
                                $toInstall[] = $name;
                            }
                            if($name != $matchedByPriority){
                                self::replace($item['method'], $name, self::getOverrideFilePath($instance, $item['file']));
                            }
                        }else{
                            if($curModuleName != $matchedByPriority){
                                self::replace($item['method'], $curModuleName, self::getOverrideFilePath($currentModule, $item['file']));
//                                    $toInstall[] = $currentModule;
                            }
                        }
                    }
                }
            }
        }
        if(count($toInstall)){
            self::installOverride($toInstall);
        }
    }

    /**
     * Run after disable module
     * @param \Module $module
     * @throws \ReflectionException
     */
    public static function restoreReplacedMethod(\Module $module)
    {
        self::$replaced = [];
        $curModuleName = $module->name;
        $uninstalled = $toInstall = [];
        foreach (self::$conflict as $item) {
            if(in_array($curModuleName, $item['conflicts'])){
                $matchedByPriority = null;
                foreach ($item['priority'] as $i => $name) {
                    if($name != $curModuleName){
                        if(Module::isEnabled($name)){
                            $matchedByPriority = $name;
                            $instance = Module::getInstanceByName($name);
                            if(!in_array($name, $uninstalled)){
                                $instance->uninstallOverrides();
                                $uninstalled[] = $name;
                                $toInstall[] = $name;
                            }
                            self::restore($item['method'], $name, self::getOverrideFilePath($instance, $item['file']));
                        }
                    }
                    if($matchedByPriority !== null){
                        break;
                    }
                }
                $class = self::getReflectClassFromFile(self::getOverrideFilePath($module, $item['file']));
                $allMethods = $class->getMethods();
                foreach ($allMethods as $method) {
                    if(strpos($method->getName(), $item['method'] . '___' . $curModuleName) === 0){
                        self::$replaced[] = [
                            'method' => $item['method'],
                            'module' => $curModuleName,
                            'file' => self::getOverrideFilePath($module, $item['file']),
                        ];
                    }
                }
            }
        }
        foreach (self::$replaced as $item) {
            self::restore($item['method'], $item['module'], $item['file']);
        }
        if(count($toInstall)){
            self::installOverride($toInstall);
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    public static function getMethodSource(ReflectionMethod $method)
    {
        $path = $method->getFileName();
        $lines = @file($path);
        $from = $method->getStartLine();
        $to = $method->getEndLine();
        $len = $to - $from + 1;
        return implode(array_slice($lines, $from - 1, $len));
    }

    /**
     * @param string $filePath
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public static function getReflectClassFromFile($filePath)
    {
        $content = file($filePath);
        $content = array_diff($content, ["\n"]);
        preg_match('/class\s+([\\\\a-z0-9_]+)(?:(\s+extends\s+([\\\\a-z0-9_]+))?(\s+implements\s+([\\\\a-z0-9_]+))?)/i', implode('', $content), $m);
        $classname = $m[1];
        do {
            $uniq = uniqid();
            $classname = $classname . '___' . $uniq;
        } while (class_exists($classname, false));
        self::execCode(preg_replace(
            ['#^\s*<\?(?:php)?#', '/class\s+' . $m[1] . '(?:(\s+extends\s+([\\\\a-z0-9_]+))?(\s+implements\s+([\\\\a-z0-9_]+))?)/i'],
            [' ', 'class ' . $classname . ' extends \stdClass'],
            implode('', $content)
        ));
        return new ReflectionClass($classname);
    }

    /**
     * @param string $methodName
     * @param string $moduleName
     * @param string $filePath
     * @return false|int
     * @throws \ReflectionException
     */
    public static function replace($methodName, $moduleName, $filePath)
    {
        $module_file = file($filePath);
        $class = self::getReflectClassFromFile($filePath);
        try {
            $method = $class->getMethod($methodName);
        } catch (ReflectionException $e) {
            return false;
        }
        $str = preg_replace('/((:?public|private|protected)?\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', '$1___'. $moduleName, $module_file);
        return @file_put_contents($filePath, implode('', $str));
    }

    /**
     * @param string $orgMethodName
     * @param string $moduleName
     * @param string $filePath
     * @return false|int
     * @throws \ReflectionException
     */
    public static function restore($orgMethodName, $moduleName, $filePath)
    {
        $class = self::getReflectClassFromFile($filePath);
        $allMethods = $class->getMethods();
        $module_file = file($filePath);
        foreach ($allMethods as $method) {
            if(strpos($method->getName(), $orgMethodName . '___' . $moduleName) === 0){
                $str = preg_replace('/((:?public|private|protected)?\s+(static\s+)?function\s+)(?:\b' . $method->getName() . '\b)/ism', '$1'. $orgMethodName, $module_file);
                return @file_put_contents($filePath, implode('', $str));
            }
        }
        return false;
    }
    public static function execCode($php_code)
    {
        if (!function_exists('ets_execute_php'))
            include_once(dirname(__FILE__) . '/temp');
        if (function_exists('ets_execute_php'))
            call_user_func('ets_execute_php', $php_code);
        else {
            $temp = @tempnam(Module::getInstanceByName('ets_marketplace')->getLocalPath() . 'cache', 'execCode');
            $handle = fopen($temp, "w+");
            fwrite($handle, "<?php\n" . $php_code);
            fclose($handle);
            if(file_exists($temp))
            {
                include $temp;
                @unlink($temp);
            }
        }
    }
}