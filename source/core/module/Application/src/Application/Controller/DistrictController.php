<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class DistrictController extends FrontEndController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cities_id = $request->getPost('cities_id', '');
            if(!empty($cities_id)){
                $data = $this->getModelTable('UserTable')->loadDistrict($cities_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data,
                    'type' => 'loadDistrict',
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE,
            'type' => 'loadDistrict',
        ));
        die();
    }
}
