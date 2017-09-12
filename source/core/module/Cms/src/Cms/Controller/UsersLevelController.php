<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\UserForm;
use Cms\Form\UsersLevelForm;
use Cms\Model\User;
use Cms\Model\UsersLevel;

use JasonGrimes\Paginator;

class UsersLevelController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'user';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('UserTable')->countUsersLevels( $params );
        $levels = $this->getModelTable('UserTable')->getUsersLevels( $params );

        $link = '/cms/user-level?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['total'] = $total;
        $this->data_view['levels'] = $levels;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new UsersLevelForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $user = new UsersLevel();
            $form->setInputFilter($user->getInputFilter());
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $post['min_buy'] = str_replace(',','', $post['min_buy']);
            $form->setData($post);
            if($form->isValid()){
                $user->exchangeArray($form->getData());
                $this->getModelTable('UserTable')->saveUsersLevel($user);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/user_level');
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/user_level', array(
                'action' => 'add'
            ));
        }
        try {
            $user = $this->getModelTable('UserTable')->getUsersLevel($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/user_level', array(
                'action' => 'index'
            ));
        }
        $form = new UsersLevelForm();
        $form->bind($user);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($data);
            if($form->isValid()){
                $this->getModelTable('UserTable')->saveUsersLevel($user);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/user_level');
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['user'] = $user;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('UserTable')->deleteUsersLevel($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('UserTable')->deleteUsersLevel($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user_level');
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
                $this->getModelTable('UserTable')->updateUsersLevel($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('UserTable')->updateUsersLevel($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user_level');
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
                $this->getModelTable('UserTable')->updateUsersLevel($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('UserTable')->updateUsersLevel($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user_level');
    }

}