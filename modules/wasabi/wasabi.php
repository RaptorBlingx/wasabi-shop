<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Wasabi extends Module
{
    private $override_path;
    private static $product_add = false;
    private static $product_update = false;

    public function __construct()
    {
        $this->name = 'wasabi';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Wasabi';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Wasabi customizations');
        $this->description = $this->l('Module used for adding overrides');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->override_path = $this->local_path . 'override' . DIRECTORY_SEPARATOR;
    }

    public function install()
    {
        $this->_installOverride();
        return parent::install() &&
            $this->registerHook([
                'actionObjectProductAddAfter',
                'actionObjectProductUpdateAfter'
            ]);
    }

    private function _installOverride()
    {
        $modules = array_map('basename', glob($this->override_path . 'modules' . DIRECTORY_SEPARATOR . '*'));
        foreach ($modules as $module) {
            $relativePath = 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'controllers';
            $this->copy_recursive(
                source: $this->override_path . $relativePath,
                dest: _PS_OVERRIDE_DIR_ . $relativePath
            );
        }
    }

    /** 
     * @param array{object: Product} $params
     */
    public function hookActionObjectProductAddAfter($params)
    {
        if (class_exists('Ets_MarketPlaceProductsModuleFrontControllerOverride')) {
            Ets_MarketPlaceProductsModuleFrontControllerOverride::$productID = $params['object']->id;
        }
        if (self::$product_add) {
            return;
        }
        $this->hookActionObjectProductUpdateAfter($params);

        self::$product_add = true;
    }

    /** 
     * @param array{object: Product} $params
     */
    public function hookActionObjectProductUpdateAfter($params)
    {
        if (self::$product_update) {
            return;
        }

        $product = $params['object'];
        if (
            $this->context->controller
            && class_exists('Ets_MarketPlaceProductsModuleFrontControllerOverride')
            && $this->context->controller::class === Ets_MarketPlaceProductsModuleFrontControllerOverride::class
        ) {
            if (Tools::getValue('product_type') == 3) {
                $product->is_virtual = true;
                $product->product_type = 'virtual';
                $product->skill = Tools::getValue('product_skill');
            }
            else {  
                $product->skill = null;
            }
            self::$product_update = true;
            $product->save(null_values: true);
        }

        self::$product_update = true;
    }

    private function copy_recursive($source, $dest)
    {
        if (! is_dir($source)) {
            copy($source, $dest);
            return;
        }

        $dir_handle=opendir($source);
        while($file=readdir($dir_handle)){
            if($file!="." && $file!=".."){
                if(! is_dir($source.DIRECTORY_SEPARATOR.$file)){
                    copy($source.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file);
                    continue;
                }
                if(!is_dir($dest.DIRECTORY_SEPARATOR.$file)){
                    mkdir($dest.DIRECTORY_SEPARATOR.$file, 0755, true);
                }
                $this->copy_recursive($source.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file);
            }
        }
        closedir($dir_handle);
    }
}
