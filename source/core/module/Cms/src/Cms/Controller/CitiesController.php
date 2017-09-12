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

class CitiesController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'cities';
    }

    public function indexAction()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        

        $country_id = $this->params()->fromQuery('country_id', 241 );
        $params = array();
        $params['country_id'] = $country_id;
        $params['page'] = $page;
        $params['itemsPerPage'] = $page_size;
        $cities = $this->getModelTable('CityTable')->getCities($params);
        $total = $this->getModelTable('CityTable')->getTotalCities($params);
        $link = "&country_id=".$country_id;
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);

        $contries = $this->getModelTable('CountryTable')->getContries();
        $this->data_view['cities'] = $cities;
        $this->data_view['paging'] = $paging;
        $this->data_view['contries'] = $contries;
        $this->data_view['country_id'] = $country_id;
        $this->data_view['page'] = $page;
        return $this->data_view;
    }

    public function shippingAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/cities', array(
                'action' => 'index'
            ));
        }

        $city = $this->getModelTable('CityTable')->getCity($id);
        if( !empty($city) ){
            $page = isset($_GET['page']) ? $_GET['page'] : 0;
            $this->intPage = $page;
            $page_size = $this->intPageSize;
            
            $params = array();
            $params['cities_id'] = $id;
            $params['page'] = $page;
            $params['itemsPerPage'] = $page_size;
            $shippings = $this->getModelTable('ShippingTable')->getShippingOfCity($params);
            $total = $this->getModelTable('ShippingTable')->getTotalShippingOfCity($params);
            $link = "";
            $objPage = new Paging($total, $page, $page_size, $link);
            $paging = $objPage->getListFooter($link);
            $this->data_view['shippings'] = $shippings;
            $this->data_view['paging'] = $paging;
            $this->data_view['page'] = $page;
            $this->data_view['city'] = $city;
            return $this->data_view;
        }else{
            return $this->redirect()->toRoute('cms/cities', array(
                'action' => 'index'
            ));
        }
    }

    public function ajackFilterAction()
    {
        $request = $this->getRequest();
        $cities = array();

        if ($request->isPost()) {
            $query = $request->getPost('query', '');
            $country_id = $request->getPost('country_id', '');
            $params = array('cities_title' => $query, 'country_id' => $country_id);
            $cities = $this->getModelTable('CityTable')->find($params);

        }
        echo json_encode($cities);
        die();
    }

    public function ajaxCitiesAction()
    {
        $request = $this->getRequest();
        $cities = array();
        if($request->isPost()){
            $ids = $request->getPost('id');
            $cities = $this->getModelTable('CityTable')->getCitiesByIds($ids);
        }
        echo json_encode($cities);
        die();
    }
}