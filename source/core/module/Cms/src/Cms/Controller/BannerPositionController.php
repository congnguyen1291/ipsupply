<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\BannerPositionForm;
use Cms\Lib\Paging;
use Cms\Model\BannerPosition;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class BannerPositionController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'developer';
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

        $total = $this->getModelTable('BannerPositionTable')->countAll( $params );
        $positions = $this->getModelTable('BannerPositionTable')->fetchAll( $params );

        $link = '/cms/banner-position?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['positions'] = $positions;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new BannerPositionForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new BannerPosition();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $g->exchangeArray($request->getPost());
                try {
                    if( empty($g->position_alias) ){
                        $g->position_alias = $this->getStringUpcaseFriendly($g->position_name);
                    }
                    $positions = $this->getModelTable('BannerPositionTable')->countBannerPositionWithAlias($g->position_alias);
                    if(!empty($positions)){
                        $g->position_alias = $g->position_alias.'_'.(count($positions)+1);
                    }
                    $image_preview = $request->getPost('image_preview', '');
                    $g->image_preview = $image_preview;

                    $this->getModelTable('BannerPositionTable')->saveBannerPosition($g);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/BannerPosition');
                } catch (\Exception $ex) {
                    //die($ex->getMessage());
                }

            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/BannerPosition', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('BannerPositionTable')->getBannerPosition($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/BannerPosition', array(
                'action' => 'index'
            ));
        }
        $form = new BannerPositionForm();
        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    if( empty($g->position_alias) ){
                        $g->position_alias = $this->getStringUpcaseFriendly($g->position_name);
                    }
                    $positions = $this->getModelTable('BannerPositionTable')->countBannerPositionWithAlias($g->position_alias);
                    $list_id = array();
                    foreach ($positions as $key => $position) {
                        $list_id[] = $position['position_id'];
                    }
                    if(!empty($list_id) && !in_array($g->position_id,$list_id)){
                        $g->position_alias = $g->position_alias.'_'.(count($list_id)+1);
                    }
                    $image_preview = $request->getPost('image_preview', '');
                    $g->image_preview = $image_preview;
                    $this->getModelTable('BannerPositionTable')->saveBannerPosition($g);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/BannerPosition');
                } catch (\Exception $ex) {
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['position'] = $g;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('BannerPositionTable')->deletePosition($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('BannerPositionTable')->deletePosition($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/BannerPosition');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('BannerPositionTable')->updatePosition($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('BannerPositionTable')->updatePosition($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/BannerPosition');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('BannerPositionTable')->updatePosition($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('BannerPositionTable')->updatePosition($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/BannerPosition');
    }

}