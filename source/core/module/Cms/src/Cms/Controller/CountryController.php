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

use JasonGrimes\Paginator;

class CountryController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'country';
    }

    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $total = $this->getModelTable('CountryTable')->countAll( $params );
        $countries = $this->getModelTable('CountryTable')->fetchAll( $params );

        $link = '/cms/country?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['countries'] = $countries;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/country', array(
                'action' => 'index'
            ));
        }
        try {
            $country = $this->getModelTable('CountryTable')->getCountry( array('id'=>$id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/country', array(
                'action' => 'index'
            ));
        }
        $form = new ApiForm();
        $form->bind($country);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($country->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $this->getModelTable('ApiTable')->saveCountry($country);
                    return $this->redirect()->toRoute('cms/country');
                } catch (\Exception $ex) {
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['country'] = $country;
        return $this->data_view;
    }

}