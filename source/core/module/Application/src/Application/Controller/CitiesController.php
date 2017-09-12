<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class CitiesController extends FrontEndController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $country_id = $request->getPost('country_id', '');
            if(!empty($country_id)){
                $data = $this->getModelTable('UserTable')->loadCities($country_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data,
                    'type' => 'loadCities',
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE,
            'type' => 'loadCities',
        ));
        die();
    }
}
