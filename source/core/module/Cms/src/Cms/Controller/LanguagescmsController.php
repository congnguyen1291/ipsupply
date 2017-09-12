<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/10/14
 * Time: 9:07 AM
 */
namespace Cms\Controller;
use Cms\Form\LanguagesForm;
use Cms\Model\Languagescms;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;
class LanguagescmsController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'languagescms';
    }

    public function indexAction(){
//        $path = './module/lang/';
//        $files = glob($path.'*.php');
//        $lang_files = array();
//        foreach($files as $file){
//            $lang_files[] = basename($file);
//        }

        $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', $this->intPage, $this->intPageSize);
        $this->data_view['languages'] = $languages;
        return $this->data_view;
    }

    public function addAction(){
        $form = new LanguagesForm();
//        $template = include('./module/lang/lang_template.php');
        $request = $this->getRequest();
        if($request->isPost()){
            $lang = new Languages();
            $form->setInputFilter($lang->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $lang->exchangeArray($form->getData());
                if($this->getModelTable('LanguagesTable')->saveLanguage($lang)){
                    return $this->redirect()->toRoute('cms/languagecms');
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/languagecms', array('action' => 'add'));
        }
        try{
            $lang = $this->getModelTable('LanguagesTable')->getById($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/languagecms');
        }
        $form = new LanguagesForm();
        $form->get('languages_file')->setAttribute('readonly', 'readonly');
        $form->bind($lang);
        $request = $this->getRequest();
        if($request->isPost()){
            $lang = new Languages();
            $form->setInputFilter($lang->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                if($this->getModelTable('LanguagesTable')->saveLanguage($lang)){
                    return $this->redirect()->toRoute('cms/languagecms');
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function publishedAction(){
        $request = $this->getRequest();
        if($request->getPost()){
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('LanguagescmsTable')->softUpdateData($ids, $data);
        }
        return $this->redirect()->toRoute('cms/Languagescms');
    }

    public function unpublishedAction(){
        $request = $this->getRequest();
        if($request->getPost()){
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('LanguagescmsTable')->softUpdateData($ids, $data);
        }
        return $this->redirect()->toRoute('cms/Languagescms');
    }

    public function manageKeywordsAction(){
        $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', 0, 100);
        $total = $this->getModelTable('LanguagescmsTable')->countAllKeyword();
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter ( $link );
        $keywords = $this->getModelTable('LanguagescmsTable')->getKeywords($this->intPage, $this->intPageSize);
        $this->data_view['keywords'] = $keywords;
        $this->data_view['languages'] = $languages;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function addKeywordAction(){

        $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', 0, 100);
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            if(trim($keyword) == ''){
                $error[] = 'Từ khóa không được bỏ trống';
            }
            if(!isset($error)){
                $keyword = str_replace(' ', '',trim($keyword));
                $translates = $request->getPost('translate');
                $keywords = $this->getModelTable('LanguagescmsTable')->getKeyword($keyword);
                if($keywords->count() == 0){
                    $this->getModelTable('LanguagescmsTable')->saveKeyword($keyword, $translates,$languages);
                    return $this->redirect()->toRoute('cms/languagescms', array('action' => 'manage-keywords'));
                }else{
                    return $this->redirect()->toUrl(FOLDERWEB.'/cms/languagescms/edit-keyword?word='.$keyword);
                }
            }
        }
        $this->data_view['languages'] = $languages;
        return $this->data_view;
    }

    public function editKeywordAction(){
        $word = $this->params()->fromQuery('word', NULL);
        if(!$word){
            return $this->redirect()->toRoute('cms/languagescms', array('action' => 'add-keyword'));
        }
        try{
            $keyword = $this->getModelTable('LanguagescmsTable')->getKeyword($word);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/languagescms', array('action' => 'manage-keyword'));
        }
        $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', 0, 100);
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            if(trim($keyword) == ''){
                $error[] = 'Từ khóa không được bỏ trống';
            }
            if(!isset($error)){
                $keyword = str_replace(' ', '',trim($keyword));
                $translates = $request->getPost('translate');
                $this->getModelTable('LanguagescmsTable')->saveKeyword($keyword, $translates, $languages);
                return $this->redirect()->toRoute('cms/Languagescms', array('action' => 'manage-keywords'));
            }else{
                $_SESSION['error'] = $error;
            }
        }
        $this->data_view['languages'] = $languages;
        $this->data_view['keyword'] = $keyword;
        return $this->data_view;
    }

    public function deleteKeywordAction(){
        $request = $this->getRequest();
        if($request->getPost()){
            $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', 0, 100);
            $keywords = $request->getPost('cid');
            foreach($keywords as $keyword){
                $this->getModelTable('LanguagescmsTable')->deleteKeyword($keyword, $languages);
            }
        }
        return $this->redirect()->toRoute('cms/languagescms', array('action' => 'manage-keywords'));
    }

    public function filterKeywordAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            $keywords = $this->getModelTable('LanguagescmsTable')->getKeywordsBySearch($keyword);
            $languages = $this->getModelTable('LanguagescmsTable')->fetchAll('','', 0, 100);
            $result = new ViewModel();
            $result->setTerminal(true);
            $result->setVariables(array(
                'keywords' => $keywords,
                'languages'=> $languages,
            ));
            return $result;
        }
    }

}