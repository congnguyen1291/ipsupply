<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ApiForm;
use Cms\Lib\Paging;
use Cms\Model\Api;
use Zend\View\Model\ViewModel;

class DistrictsController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'districts';
    }

    public function ajackFilterAction()
    {
        $request = $this->getRequest();
        $products = array();

        if ($request->isPost()) {
            $data_filter = $request->getPost('query');
            $products = $this->getModelTable('DistrictTable')->find($data_filter);

        }
        echo json_encode($products);
        die();
    }

    public function ajaxDistrictsAction()
    {
        $request = $this->getRequest();
        $products = array();
        if($request->isPost()){
            $ids = $request->getPost('id');
            $products = $this->getModelTable('DistrictTable')->getDistrictByIds($ids);
        }
        echo json_encode($products);
        die();
    }
}