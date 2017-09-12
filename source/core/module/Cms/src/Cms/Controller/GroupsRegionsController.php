<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\GroupsRegionsForm;
use Cms\Lib\Paging;
use Cms\Model\GroupsRegions;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use JasonGrimes\Paginator;

class GroupsRegionsController extends BackEndController
{
    public $transportation_type = array(0 => 'Flat rate',1 => 'Free shipping', 2 => 'Local pickup' );
    public $price_type = array(0 => 'Fixed',1 => 'Number of products', 2 => 'Total cart' , 3 => 'Minimum Cart' );
    public $free_conditions = array(0 => 'A minimum order amount');
    public $shipping_class = array(0 => 'Normal',1 => 'Special' );

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'group-regions';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            $params['group_regions_name'] = $q;
        }

        $total = $this->getModelTable('GroupsRegionsTable')->countAll($params);
        $groups = $this->getModelTable('GroupsRegionsTable')->fetchAll($params);

        $link = '/cms/group-regions?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['groups'] = $groups;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new GroupsRegionsForm();
        $countryDisable = $this->getModelTable('GroupsRegionsTable')->getCountries();
        $idCountriesDisable = array();
        foreach ($countryDisable as $key => $cd) {
            $idCountriesDisable[] = $cd['id'];
        }
        $citiesDisable = $this->getModelTable('GroupsRegionsTable')->getRegions();
        $idCitiesDisable = array();
        foreach ($citiesDisable as $key => $cd) {
            $idCitiesDisable[] = $cd['cities_id'];
        }

        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new GroupsRegions();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $country_id = $request->getPost('country_id', array());
                    $regions = $request->getPost('regions', array());
                    $g->country_id = implode(',', $country_id);
                    $g->regions = implode(',', $regions);

                    $this->getModelTable('GroupsRegionsTable')->save($g);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/group_regions');
                } catch (\Exception $ex) {}

            }
        }

        $this->data_view['idCountriesDisable'] = $idCountriesDisable;
        $this->data_view['idCitiesDisable'] = $idCitiesDisable;
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/group_regions', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('GroupsRegionsTable')->one($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/group_regions', array(
                'action' => 'index'
            ));
        }

        $targetCountry = explode(',', $g->country_id);
        $targetCities = explode(',', $g->regions);

        $form = new GroupsRegionsForm();
        $countryDisable = $this->getModelTable('GroupsRegionsTable')->getCountries();
        $idCountriesDisable = array();
        foreach ($countryDisable as $key => $cd) {
            if( !in_array($cd['id'], $targetCountry)){
                $idCountriesDisable[] = $cd['id'];
            }
        }
        $citiesDisable = $this->getModelTable('GroupsRegionsTable')->getRegions();
        $idCitiesDisable = array();
        foreach ($citiesDisable as $key => $cd) {
            if( !in_array($cd['cities_id'], $targetCities)){
                $idCitiesDisable[] = $cd['cities_id'];
            }
        }

        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $country_id = $request->getPost('country_id', array());
                    $regions = $request->getPost('regions', array());
                    $g->country_id = implode(',', $country_id);
                    $g->regions = implode(',', $regions);
                    $this->getModelTable('GroupsRegionsTable')->save($g);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/group_regions');
                } catch (\Exception $ex) {
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['group'] = $g;
        $this->data_view['targetCountry'] = $targetCountry;
        $this->data_view['targetCities'] = $targetCities;
        $this->data_view['idCountriesDisable'] = $idCountriesDisable;
        $this->data_view['idCitiesDisable'] = $idCitiesDisable;
        return $this->data_view;
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('GroupsRegionsTable')->updateGroupRegions($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('GroupsRegionsTable')->updateGroupRegions($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/group_regions');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('GroupsRegionsTable')->updateGroupRegions($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('GroupsRegionsTable')->updateGroupRegions($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/group_regions');
    }

    public function deleteAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $cid = $request->getPost('cid');
            try{
                $this->getModelTable('GroupsRegionsTable')->delete($cid);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }catch(\Exception $ex){
            }
        }
        return $this->redirect()->toRoute('cms/group_regions');
    }

    public function districtsAction(){
        
        $group_regions_id = $this->params()->fromQuery('group_regions_id', '' );
        $list = array();
        if( !empty($group_regions_id) ){
            $cities = $this->getModelTable('GroupsRegionsTable')->getCities($group_regions_id);
            $results = $this->getModelTable('GroupsRegionsTable')->getDistrictsInGroup($group_regions_id);
            foreach ($results as $key => $result) {
                if( !empty($list[$result['cities_id']]) ){
                    $list[$result['cities_id']]['data'][] =  $result;
                }else{
                    $list[$result['cities_id']] =  array(
                                                    'city' => array(
                                                                    'cities_id'=>$result['cities_id'],
                                                                    'cities_title'=>$result['cities_title'],
                                                                ) ,
                                                    'data'=> array($result));
                }
            }
        }
        $item = array('list' => $list, 'cities' => $cities);
        return new JsonModel($item);
    }

}