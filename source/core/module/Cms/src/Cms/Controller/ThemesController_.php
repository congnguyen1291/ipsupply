<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\ThemeForm;
use Cms\Model\Templates;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

class ThemesController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'website';
    }

    public function indexAction()
    {   
        $themes = $this->getModelTable('TemplatesTable')->getAll();
        $this->data_view['themes'] = $themes;
        return $this->data_view;
    }

    public function listAction()
    {   
        $params = array();
        $link = '';
        $page = $this->params()->fromQuery('page', 0);
        $order_type = $this->params()->fromQuery('order_type','desc');
        $order_by = $this->params()->fromQuery('order','template_id');

        $template_name = $this->params()->fromQuery('template_name', NULL);
        if($template_name){
            $params['template_name'] = $website_name;
            $link .= '&template_name='.$website_name;
        }
        $template_status = $this->params()->fromQuery('template_status', NULL);
        if($template_status){
            $params['template_status'] = $template_status;
            $link .= '&template_status='.$template_status;
        }


        $order = array(
            $order_by => $order_type,
        );
        $order_link = $link;

        $total = $this->getModelTable('TemplatesTable')->countAll($params);
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $objPage = new Paging( $total, $page, $page_size, $link );
        $paging = $objPage->getListFooter ( $link );
        $themes = $this->getModelTable('TemplatesTable')->getList($params, $order, $this->intPage, $this->intPageSize);
        if(!$order_link){
            $order_link = FOLDERWEB.'/cms/themes/list';
            if(isset($_GET['page'])){
                $order_link .= '?page='.$_GET['page'].'&';
            }else{
                $order_link .= '?';
            }
        }else{
            $order_link = FOLDERWEB.'/cms/themes/list?'.trim($order_link,'&');
            if(isset($_GET['page'])){
                $order_link .= '&page='.$_GET['page'].'&';
            }else{
                $order_link .= '&';
            }
        }
        $this->data_view['themes'] = $themes;
        $this->data_view['paging'] = $paging;
        $this->data_view['order_link'] = $order_link;
        return $this->data_view;
    }

    public function singlepublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'template_status' => 1
            );
            $this->getModelTable('TemplatesTable')->updateTemplateWithId($data, $ids);
        }
        return $this->redirect()->toRoute('cms/themes', array('action' => 'list'));
    }

    public function singleunpublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'template_status' => 0
            );
            $this->getModelTable('TemplatesTable')->updateTemplateWithId($data, $ids);
        }
        return $this->redirect()->toRoute('cms/themes', array('action' => 'list'));
    }

    public function addAction()
    {   
        $form = new ThemeForm();
        $form->get('submit')->setValue('Cập nhật');
        $category_templates = $this->getModelTable('CategoryTemplateTable')->getAll();
        $options = array();
        foreach ($category_templates as $key => $category_template) {
            $options[$category_template['categories_template_id']] = $category_template['categories_title'];
        }
        $form->get('categories_template_id')->setOptions(array(
            'options' => $options
        ));
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $templates = new Templates();
            $form->setInputFilter($templates->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data['source'] = $request->getFiles('source');
                $templates->exchangeArray($data);

                $this->getModelTable('TemplatesTable')->saveTemplates($templates);
                return $this->redirect()->toRoute('cms/themes');
            }

        }

        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/themes', array(
                'action' => 'add'
            ));
        }
        try {
            $template = $this->getModelTable('TemplatesTable')->getTemplateById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/themes', array(
                'action' => 'add'
            ));
        }   
        $form = new ThemeForm();
        $form->get('submit')->setValue('Cập nhật');
        $category_templates = $this->getModelTable('CategoryTemplateTable')->getAll();
        $options = array();
        foreach ($category_templates as $key => $category_template) {
            $options[$category_template['categories_template_id']] = $category_template['categories_title'];
        }
        $form->get('categories_template_id')->setOptions(array(
            'options' => $options
        ));
        $form->bind($template);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $templates = new Templates();
            $form->setInputFilter($templates->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data['source'] = $request->getFiles('source');
                $templates->exchangeArray($data);

                $this->getModelTable('TemplatesTable')->saveTemplates($templates);
                return $this->redirect()->toRoute('cms/themes');
            }

        }

        $this->data_view['form'] = $form;
        $this->data_view['id'] = $template['template_id'];
        return $this->data_view;
    }

    public function buyAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $template_id = (int)$request->getPost('template_id');
            $template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
            if(!empty($template)){
                
                $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                $name_folder = $this->website['websites_folder'];
                
                //copy view
                $view_source = $Default.'/view';
                $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/view';
                //xoa source cu di
                exec('rm -rf '.escapeshellarg($view_new));
                //chep source sang
                exec("cp -r $view_source $view_new");

                //copy style
                $styles_source = $Default.'/styles';
                $styles_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/styles';
                //xoa source cu di
                exec('rm -rf '.escapeshellarg($styles_new));
                //chep source sang
                exec("cp -r $styles_source $styles_new");

                $templete_buy = $this->website['templete_buy'];
                $list_templete_by = explode(',',$templete_buy);
                if(!in_array($template_id, $list_templete_by)){
                    $list_templete_by[] = $template_id;
                }
                $data = array('template_id'=>$template_id, 'templete_buy' => implode(',',$list_templete_by));
                $this->getModelTable('WebsitesTable')->updateWebsite($data, $this->website);
                echo json_encode(array(
                    'success' => TRUE,
                    'template_id' => $template_id,
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => false
        ));
        die();
    }

}