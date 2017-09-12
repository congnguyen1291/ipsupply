<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:05 AM
 */

namespace Cms\Controller;

use Cms\Form\CategoryThemeForm;
use Cms\Model\CategoryArticles;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;

class CategoryThemeController extends BackEndController{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'theme';
    }

    public function indexAction(){
        $cats = $this->getModelTable('CategoryTemplateTable')->getAll();
        $this->data_view['cats'] = $cats;
        return $this->data_view;
    }

    public function addAction(){

        $form = new CategoryThemeForm();
        $form->get('submit')->setValue('Lưu lại');
        $category_templates = $this->getModelTable('CategoryTemplateTable')->getAll();
        $options = array(0=>'ROOT');
        foreach ($category_templates as $key => $category_template) {
            $options[$category_template['categories_template_id']] = $category_template['categories_title'];
        }
        $form->get('parent_id')->setOptions(array(
            'options' => $options
        ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $categories_template_id = $request->getPost('categories_template_id', 0);
            $parent_id = $request->getPost('parent_id', 0);
            $categories_title = $request->getPost('categories_title', '');
            $categories_alias = $request->getPost('categories_alias', '');
            $is_published = $request->getPost('is_published');
            if(!empty($categories_title)){
                $row = array('categories_template_id'=>$categories_template_id,
                             'parent_id'=>$parent_id,
                             'categories_title'=>$categories_title,
                             'categories_alias'=>$categories_alias,
                             'is_published'=>$is_published);
                $this->getModelTable('CategoryTemplateTable')->saveCategory($row);
                $this->updateNamespaceCached();
                return $this->redirect()->toRoute('cms/category_theme');
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/category_theme', array(
                'action' => 'add'
            ));
        }
        try {
            $cat = $this->getModelTable('CategoryTemplateTable')->getCategory($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/category_theme', array(
                'action' => 'index'
            ));
        }

        $form = new CategoryThemeForm();
        $category_templates = $this->getModelTable('CategoryTemplateTable')->getAll();
        $options = array(0=>'ROOT');
        foreach ($category_templates as $key => $category_template) {
            $options[$category_template['categories_template_id']] = $category_template['categories_title'];
        }
        $form->get('parent_id')->setOptions(array(
            'options' => $options
        ));
        $form->bind($cat);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $categories_template_id = $request->getPost('categories_template_id', 0);
            $parent_id = $request->getPost('parent_id', 0);
            $categories_title = $request->getPost('categories_title', '');
            $categories_alias = $request->getPost('categories_alias', '');
            $is_published = $request->getPost('is_published');
            if(!empty($categories_title) && !empty($categories_template_id)){
                $row = array('categories_template_id'=>$categories_template_id,
                             'parent_id'=>$parent_id,
                             'categories_title'=>$categories_title,
                             'categories_alias'=>$categories_alias,
                             'is_published'=>$is_published);
                $this->getModelTable('CategoryTemplateTable')->saveCategory($row);
                $this->updateNamespaceCached();
                return $this->redirect()->toRoute('cms/category_theme');
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function singlepublishAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if($id){
            $ids = array($id);
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CategoryTemplateTable')->update($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/category_theme');
    }

    public function singleunpublishAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if($id){
            $ids = array($id);
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('CategoryTemplateTable')->update($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/category_theme');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CategoryTemplateTable')->update($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/category_theme');
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
                $this->getModelTable('CategoryTemplateTable')->update($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/category_theme');
    }

} 