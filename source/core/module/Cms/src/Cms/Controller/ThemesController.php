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
    protected $fcKeyword = "if (!function_exists('hasPermission')) {
                            function hasPermission(\$module, \$controller,\$action){
                                if(isset(\$_SESSION['CMSMEMBER'])){
                                    if(\$_SESSION['CMSMEMBER']['type'] == 'admin' && \$_SESSION['CMSMEMBER']['website_id'] == ID_MASTERPAGE){
                                        return TRUE;
                                    }
                                    if(isset(\$_SESSION['CMSMEMBER']['permissions'])){
                                        if(isset(\$_SESSION['CMSMEMBER']['permissions'][\$module][\$controller][\$action])){
                                            return TRUE;
                                        }
                                    }
                                }
                                return FALSE;
                            };
                        }
                        if (!function_exists('swapTranslateForAdmin')) {
                            function swapTranslateForAdmin(\$langs){
                              if(hasPermission('cms','language', 'manage-keywords')){
                                \$results = array();
                                foreach (\$langs as \$key => \$lang) {
                                  \$results[\$key] = '<lang class=\"editer-lang\" data-key=\"'.\$key.'\" >'.\$lang.'</lang>';
                                }
                                return \$results;
                              }
                              return \$langs;
                            };
                        }";

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

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

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

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

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
                $picture_id = $request->getPost('picture_id', '');
                $data['source'] = $request->getFiles('source');
                $templates->exchangeArray($data);

                $this->getModelTable('TemplatesTable')->saveTemplates($templates, $picture_id);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/themes', array('action' => 'list'));
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
                $picture_id = $request->getPost('picture_id', '');
                $data['source'] = $request->getFiles('source');
                $templates->exchangeArray($data);

                $this->getModelTable('TemplatesTable')->saveTemplates($templates, $picture_id);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/themes', array('action' => 'list'));
            }

        }

        $this->data_view['form'] = $form;
        $this->data_view['id'] = $template['template_id'];
        $this->data_view['template'] = $template;
        return $this->data_view;
    }

    public function switchPreviewAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $template_id = (int)$request->getPost('template_id');
            $template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
            if(!empty($template)){
                $_SESSION['CMSMEMBER']['preview'] = $template;
                echo json_encode(array(
                    'flag' => TRUE,
                    'preview' => $_SESSION['CMSMEMBER']['preview'],
                    'url' => 'http://'.$this->website['website_domain']
                ));
                die();
            }else{
                $_SESSION['CMSMEMBER']['preview'] = array();
                echo json_encode(array(
                    'flag' => TRUE,
                    'preview' => $_SESSION['CMSMEMBER']['preview'],
                    'url' => 'http://'.$this->website['website_domain']
                ));
                die();
            }
        }
        echo json_encode(array(
            'flag' => FALSE
        ));
        die();
    }

    public function buyAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            try {
                $template_id = (int)$request->getPost('template_id');
                $template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
                if(!empty($template)){
                    
                    $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                    $name_folder = $this->website['websites_folder'];
                    
                    //copy view
                    $view_source = $Default.'/view';
                    $folder_backup=PATH_BASE_ROOT . '/templates/Backup/'.$name_folder;
                    if(!is_dir($folder_backup)){
                        @mkdir ( $folder_backup, 0777 );
                    }
                    $folder_backup .= '/'.date('Y_m_d_H_i_s');
                    if(!is_dir($folder_backup)){
                        @mkdir ( $folder_backup, 0777 );
                    }
                    $view_backup = $folder_backup.'/view';
                    $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/view';
                    //backup source cu
                    exec("cp -r $view_new $view_backup");
                    //xoa source cu di
                    exec('rm -rf '.escapeshellarg($view_new));
                    //chep source sang
                    exec("cp -r $view_source $view_new");

                    //copy style
                    $styles_source = $Default.'/styles';
                    $styles_backup = $folder_backup.'/styles';
                    $styles_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/styles';
                    //backup style cu
                    exec("cp -r $styles_new $styles_backup");
                    //xoa style cu di
                    exec('rm -rf '.escapeshellarg($styles_new));
                    //chep style sang
                    exec("cp -r $styles_source $styles_new");


                    if(!$this->isMasterPage()){
                        $languages_ = $this->getModelTable('LanguagesTable')->fetchAll('','', 0, 100);
                        foreach($languages_ as $lang){
                            if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                                require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                                $array_lang = $$lang['languages_file'];
                                if(is_file($Default.'/lang/'.$lang['languages_file'].'.php')){
                                    require_once $Default.'/lang/'.$lang['languages_file'].'.php';
                                    $langs_compare = $$lang['languages_file'];
                                    foreach ($langs_compare as $key => $value) {
                                        if(!isset($array_lang[$key])){
                                            $array_lang[$key] = $value;
                                        }
                                    }
                                }
                                $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                                $data = "<?php \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                                @file_put_contents($path, $data);
                            }
                        }

                        $config_source = $Default.'/config.json';
                        $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                        //modify css, js
                        if(is_file($config_source)){
                            if(is_file($url_config)){
                                try{
                                    $config_s = file_get_contents($config_source);
                                    $config_s = json_decode($config_s, true);
                                    $config = file_get_contents($url_config);
                                    $config = json_decode($config, true);
                                    $css = array();
                                    $js = array();
                                    if(!empty($config_s['css'])){
                                        $css = $config_s['css'];
                                    }
                                    if(!empty($config_s['js'])){
                                        $js = $config_s['js'];
                                    }
                                    $config['css'] = $css;
                                    $config['js'] = $js;

                                    $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";

                                    $fp = fopen($url_config, 'w+');
                                    fwrite($fp, $str);
                                    fclose($fp);

                                }catch(\Exception $ex){
                                    echo json_encode(array('flag'=>FALSE, 'msg'=> 'có lỗi xảy ra, vui lòng thử lại'));
                                    die();
                                }
                            }else{
                                $config_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                                //chep config sang
                                exec("cp -r $config_source $config_new");
                            }
                        }
                                    
                    }

                    $templete_buy = trim($this->website['templete_buy'], ',');
                    $list_templete_by = explode(',',$templete_buy);
                    if(!in_array($template_id, $list_templete_by)){
                        $list_templete_by[] = $template_id;
                    }
                    $data = array('template_id'=>$template_id, 'templete_buy' => implode(',',$list_templete_by));
                    $this->getModelTable('WebsitesTable')->updateWebsite($data, $this->website);
                    unset($_SESSION['CMSMEMBER']['preview']);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    echo json_encode(array(
                        'success' => TRUE,
                        'template_id' => $template_id
                    ));
                    die();
                }
            } catch (\Exception $e) {
                echo json_encode(array(
                    'success' => false,
                    'msg' => $e->getMessage()
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => false
        ));
        die();
    }

    public function appAction(){
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        if($request->isPost()){
            try {
                if( !empty($_SESSION['CMSMEMBER']['preview']) ){
                    $preview = $_SESSION['CMSMEMBER']['preview'];
                    $template_id = $preview['template_id'];
                    $template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
                    if(!empty($template)){
                        
                        $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                        $name_folder = $this->website['websites_folder'];
                        
                        //copy view
                        $view_source = $Default.'/view';
                        $folder_backup=PATH_BASE_ROOT . '/templates/Backup/'.$name_folder;
                        if(!is_dir($folder_backup)){
                            @mkdir ( $folder_backup, 0777 );
                        }
                        $folder_backup .= '/'.date('Y_m_d_H_i_s');
                        if(!is_dir($folder_backup)){
                            @mkdir ( $folder_backup, 0777 );
                        }
                        $view_backup = $folder_backup.'/view';
                        $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/view';
                        $view_new_=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder;
                        //backup source cu
                        exec("cp -r $view_new $view_backup");
                        //xoa source cu di
                        exec('rm -rf '.escapeshellarg($view_new));
                        //chep source sang
                        exec("cp -r $view_source $view_new_");

                        //copy style
                        $styles_source = $Default.'/styles';
                        $styles_backup = $folder_backup.'/styles';
                        $styles_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/styles';
                        $styles_new_=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder;
                        //backup style cu
                        exec("cp -r $styles_new $styles_backup");
                        //xoa style cu di
                        exec('rm -rf '.escapeshellarg($styles_new));
                        //chep style sang
                        exec("cp -r $styles_source $styles_new_");


                        if(!$this->isMasterPage()){
                            $languages_ = $this->getModelTable('LanguagesTable')->fetchAll('','', 0, 100);
                            foreach($languages_ as $lang){
                                if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                                    require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                                    $array_lang = $$lang['languages_file'];
                                    if(is_file($Default.'/lang/'.$lang['languages_file'].'.php')){
                                        require_once $Default.'/lang/'.$lang['languages_file'].'.php';
                                        $langs_compare = $$lang['languages_file'];
                                        foreach ($langs_compare as $key => $value) {
                                            if(!isset($array_lang[$key])){
                                                $array_lang[$key] = $value;
                                            }
                                        }
                                    }
                                    $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                                    $data = "<?php \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                                    @file_put_contents($path, $data);
                                }
                            }

                            $config_source = $Default.'/config.json';
                            $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                            //modify css, js
                            if(is_file($config_source)){
                                if(is_file($url_config)){
                                    try{
                                        $config_s = file_get_contents($config_source);
                                        $config_s = json_decode($config_s, true);
                                        $config = file_get_contents($url_config);
                                        $config = json_decode($config, true);
                                        $css = array();
                                        $js = array();
                                        if(!empty($config_s['css'])){
                                            $css = $config_s['css'];
                                        }
                                        if(!empty($config_s['js'])){
                                            $js = $config_s['js'];
                                        }
                                        $config['css'] = $css;
                                        $config['js'] = $js;

                                        $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";

                                        $fp = fopen($url_config, 'w+');
                                        fwrite($fp, $str);
                                        fclose($fp);

                                    }catch(\Exception $ex){
                                        echo json_encode(array('flag'=>FALSE, 'msg'=> 'có lỗi xảy ra, vui lòng thử lại'));
                                        die();
                                    }
                                }else{
                                    $config_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                                    //chep config sang
                                    exec("cp -r $config_source $config_new");
                                }
                            }
                                        
                        }

                        $templete_buy = trim($this->website['templete_buy'], ',');
                        $list_templete_by = explode(',',$templete_buy);
                        if(!in_array($template_id, $list_templete_by)){
                            $list_templete_by[] = $template_id;
                        }
                        $data = array('template_id'=>$template_id, 'templete_buy' => implode(',',$list_templete_by));
                        $this->getModelTable('WebsitesTable')->updateWebsite($data, $this->website);
                        
                        unset($_SESSION['CMSMEMBER']['preview']);
                        /*strigger change namespace cached*/
                        $this->updateNamespaceCached();

                        echo json_encode(array(
                            'flag' => TRUE,
                            'template_id' => $template_id
                        ));
                        die();
                    }
                }
            } catch (\Exception $e) {
                echo json_encode(array(
                    'flag' => FALSE,
                    'msg' => $e->getMessage()
                ));
                die();
            }
        }
        echo json_encode(array(
            'flag' => FALSE,
            'msg' => $translator->translate('txt_not_found'),
        ));
        die();
    }

}