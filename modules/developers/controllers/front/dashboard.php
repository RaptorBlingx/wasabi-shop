<?php

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\BreadcrumbItem;

/** 
 * @property Developers $module
 */
class DevelopersDashboardModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    private Customer $customer;

    public function init()
    {
        if (! $this->module->isDeveloperApproved($this->context->customer)) {
            Tools::redirectLink(
                $this->context->link->getModuleLink($this->module->name, 'registration')
            );
        }
        parent::init();
        $this->customer = $this->context->customer;
    }

    public function initContent()
    {
        parent::initContent();
        $orders = $this->getOrders();
        $total = $this->getTotalFromOrders($orders);
        $this->context->smarty->assign([
            'orders' => $orders,
            'total' => $total,
            'products' => $this->getProducts(),
        ]);
        $this->setTemplate('module:developers/views/templates/front/dashboard.tpl');
    }


    protected function getBreadcrumbLinks()
    {
        return $this->module->getBreadcrumbLinks();
    }

    protected function getProducts()
    {
        return Product::findByDeveloperEmail($this->customer->email, $this->context->language->id);
    }

    /** @return array{order:Order, status:OrderState, details:OrderDetail} */
    protected function getOrders()
    {
        /** @var OrderDetail[] */
        $details =  (new PrestaShopCollection(OrderDetail::class))
            ->where('developer_id', '=', $this->customer->id)
            ->getResults();
        
        $ordersIds = array_map(function(OrderDetail $orderDetail) {
            return $orderDetail->id_order;
        }, $details ?? []);
        $ordersIds = array_unique($ordersIds);
        /** @var Order[] */
        $orders = (new PrestaShopCollection(Order::class))
            ->where('id_order', '=', $ordersIds)
            ->getResults();

        $orders = array_reduce($orders, function(array $carry, Order $order) {
            $carry[$order->id] = $order;
            return $carry;
        }, []);

        $orderStates = (new PrestaShopCollection(OrderState::class, $this->context->language->id))->getResults();
        $orderStates = array_reduce($orderStates, function(array $carry, OrderState $orderState) {
            $carry[$orderState->id] = $orderState;
            return $carry;
        }, []);

        return array_map(function(OrderDetail $orderDetail) use($orders, $orderStates) {
            return [
                'order' => $orders[$orderDetail->id_order],
                'status' => $orderStates[$orders[$orderDetail->id_order]->current_state],
                'details' => $orderDetail
            ];
        }, $details);
    }

    /** @param array{order:Order, status:OrderState, details:OrderDetail} $orders */
    protected function getTotalFromOrders($orders)
    {
        return array_reduce($orders, function($carry, $item){
            /** @var array{order:Order, status:OrderState, details:OrderDetail} $item */ 
            return $item['order']->valid ? $carry + $item['details']->total_price_tax_incl : $carry;
        }, 0);
    }

    
}