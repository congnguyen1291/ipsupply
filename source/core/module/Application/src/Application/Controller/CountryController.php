<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class CountryController extends FrontEndController
{
    public function indexAction()
    {
        $data = $this->getModelTable('CountryTable')->getContries();
        echo json_encode(array(
            'success' => TRUE,
            'results' => $data,
            'type' => 'loadCountry',
        ));
        die();
    }

    public function forWebsiteAction()
    {
        $item = array(
            'success' => FALSE,
            'type' => 'loadCountry',
        );
        if( empty($this->website->is_local) || empty($this->website->website_contries) ){
            $data = $this->getModelTable('CountryTable')->getContries();
            $item = array(
                'success' => TRUE,
                'results' => $data,
                'type' => 'loadCountry',
            );
        }else{
            $ids = explode(',', $this->website->website_contries);
            $data = $this->getModelTable('CountryTable')->getContries( array('id'=> $ids) );
            $item = array(
                'success' => TRUE,
                'results' => $data,
                'type' => 'loadCountry',
            );
        }
        $data = $this->getModelTable('CountryTable')->getContries();
        echo json_encode($item);
        die();
    }

    public function setAction(){
        $id = $this->params()->fromQuery('id', '');
        if( !empty($id) ){
            $country = $this->getModelTable('CountryTable')->getOne( $id );
            if( !empty($country) ){
                $_SESSION['LOCATION']['country_id'] = $country->id;
                $_SESSION['LOCATION']['country_code'] = $country->code;
            }
        }else if( $id == 0 ){
            unset($_SESSION['LOCATION']);
            $_SESSION['LOCATION'] = array();
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home', array( 
                           'controller' => 'Index', 
                           'action' =>  'index'
                        )); 
    }

}
