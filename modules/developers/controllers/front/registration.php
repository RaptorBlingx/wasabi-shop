<?php

/** 
 * @property Developers $module
 */
class DevelopersRegistrationModuleFrontController extends ModuleFrontController
{

    public $auth = true;
    public $guestAllowed = true;

    private Customer $customer;

    public function init()
    {
        parent::init();
        $this->customer = $this->context->customer;
        if ($this->module->isDeveloperApproved($this->customer)) {
            Tools::redirectLink(
                $this->context->link->getModuleLink($this->module->name, 'dashboard')
            );
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign([
            'status' => $this->customer->developer_status
        ]);
        $this->setTemplate('module:developers/views/templates/front/registration.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('become-developer') && is_null($this->customer->developer_status)) {
            $this->customer->developer_status = Developers::STATUS_PENDING;
            $this->customer->update();
        }
    }

    protected function getBreadcrumbLinks()
    {
        return $this->module->getBreadcrumbLinks();
    }
}