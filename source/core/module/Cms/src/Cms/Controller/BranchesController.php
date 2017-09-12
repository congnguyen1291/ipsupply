<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/9/14
 * Time: 9:57 AM
 */
namespace Cms\Controller;

use Cms\Form\BranchesForm;
use Cms\Model\Branches;
use Cms\Lib\Paging;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Zend\Dom\Query;

use JasonGrimes\Paginator;

class BranchesController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'branches';
    }
	
    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            if( $type == 0 ){
                $params['branches_title'] = $q;
            }else if( $type == 1 ){
                $params['address'] = $q;
            }else if( $type == 2 ){
                $params['phone'] = $q;
            }else if( $type == 3 ){
                $params['email'] = $q;
            }
        }

        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $total = $this->getModelTable('BranchesTable')->countAll( $params );
        $branches = $this->getModelTable('BranchesTable')->fetchAll( $params );

        $link = '/cms/branches?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['branches'] = $branches;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['total'] = $total;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

	public function updatedefaultAction()
    {
	
		 $id = (int)$this->params()->fromRoute('id', 0);
		 if ($id ) {
			$websiteid=(isset($_SESSION['CMSMEMBER']['website_id']) && $_SESSION['CMSMEMBER']['website_id']!="")?$_SESSION['CMSMEMBER']['website_id']:"";
			if($websiteid!=''){
				$data1 = array('is_default' => 0);
				$this->getModelTable('BranchesTable')->update_isdefault($websiteid, $data1);
				$ids = array($id);
				$data = array(
					'is_default' => 1
				);
				$detail=$this->getModelTable('BranchesTable')->getById($id);
				if($detail){
					
					$ward=(isset($detail->wards_title) && $detail->wards_title!="")?$detail->wards_title.", ":"";
					$districts=(isset($detail->districts_title) && $detail->districts_title!="")?$detail->districts_title.", ":"";
					$address=$detail->address.", ".$ward.$districts.$detail->cities_title;
					 $dataqq = array(
						'website_address' => $address,
						'website_city' => $detail->cities_id,
						'website_city_name' => $detail->cities_title,
						'website_latitude' => $detail->latitude,
						'website_longitude' => $detail->latitude,
						'districts_id' => $detail->districts_id,
						'districts_title' => $detail->districts_title,
						'wards_id' => $detail->wards_id,
						'wards_title' => $detail->wards_title,
					);
					$this->getModelTable('WebsitesTable')->updateWebsiteWithId($dataqq,$websiteid);
				}
				$this->getModelTable('BranchesTable')->softUpdateData($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

			}
		 }
         return $this->redirect()->toRoute('cms/branches');
    }

    public function addAction()
    {
        $form = new BranchesForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $branch = new Branches();
            $form->setInputFilter($branch->getInputFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $branch->exchangeArray($data);
                try{
                    $this->getModelTable('BranchesTable')->saveBranch($branch);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/branches');
                }catch(\Exception $e){}
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/branches', array('action' => 'add'));
        }
        try{
            $branch = $this->getModelTable('BranchesTable')->getById($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/branches');
        }
        $form = new BranchesForm();
        $form->bind($branch);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $branch = new Branches();
            $form->setInputFilter($branch->getInputFilter());
            $form->setData($data);
            if ($form->isValid()) {
                $branch->exchangeArray($data);
                try{
                    $this->getModelTable('BranchesTable')->saveBranch($branch);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/branches');
                }catch(\Exception $ex){
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['branch'] = $branch;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('BranchesTable')->deleteBranchs($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('BranchesTable')->deleteBranchs($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/branches');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            if ( !empty($ids) ) {
                $this->getModelTable('BranchesTable')->updateBranchs($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('BranchesTable')->updateBranchs($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/branches');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if ( !empty($ids) ) {
                $this->getModelTable('BranchesTable')->updateBranchs($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('BranchesTable')->updateBranchs($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/branches');
    }

    public function defaultAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_default' => 1
            );
            if ( !empty($ids) ) {
                $this->getModelTable('BranchesTable')->updateBranchs($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_default' => 1
                );
                $this->getModelTable('BranchesTable')->updateBranchs($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/branches');
    }

    public function undefaultAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_default' => 0
            );
            if ( !empty($ids) ) {
                $this->getModelTable('BranchesTable')->updateBranchs($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_default' => 0
                );
                $this->getModelTable('BranchesTable')->updateBranchs($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/branches');
    }

    public function autoOrderAction()
    {
        $branches = $this->getModelTable('BranchesTable')->fetchAll();
        foreach ($branches as $key => $branche) {
            $row = array();
            $row['ordering'] = $key;
            $this->getModelTable('BranchesTable')->updateBranchs($branche['branches_id'], $row);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/branches', array(
                'action' => 'index'
            ));
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('BranchesTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/branches');
    }

}