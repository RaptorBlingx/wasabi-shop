<?php

namespace Wasabi\Developers\Controller;

use Customer;
use Developers;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

include_once __DIR__ . '/../../developers.php';
class DeveloperController extends FrameworkBundleAdminController
{
    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @return Response
     */
    public function indexAction()
    {
        $customRoutes = file_get_contents(_PS_MODULE_DIR_ . 'developers/config/routes.json');
        $developers = Customer::getDevelopers(active: false);
        return $this->render(
            view: '@Modules/developers/views/templates/admin/developers_index.html.twig',
            parameters: [
                'developers' => $developers,
                'customRoutes' => $customRoutes
            ]
        );
    }

    public function blockAction(Request $request)
    {
        $id = $request->get('id');
        $customer = new Customer($id);
        $customer->developer_status = Developers::STATUS_REFUSED;
        $customer->update();
        return $this->json(['message' => 'success']);
    }
    
    public function approveAction(Request $request)
    {
        $id = $request->get('id');
        $customer = new Customer($id);
        $customer->developer_status = Developers::STATUS_APPROVED;
        $customer->update();
        return $this->json(['message' => 'success']);
    }
}