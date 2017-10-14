<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/10/14
 * Time: 9:07 AM
 */
namespace Cms\Controller;
use Cms\Form\LanguagesForm;
use Cms\Model\Languages;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

use JasonGrimes\Paginator;

class LanguageController extends BackEndController{
    protected $fcKeyword = "";

    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'language';
    }

    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('LanguagesTable')->countAll( $params );
        $languages = $this->getModelTable('LanguagesTable')->fetchAll( $params );
        
        $link = '/cms/language?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        return $this->data_view;
    }

    public function addAction(){
        $form = new LanguagesForm();

        $request = $this->getRequest();
        if($request->isPost()){
            $lang = new Languages();
            $form->setInputFilter($lang->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $lang->exchangeArray($form->getData());
                if($this->getModelTable('LanguagesTable')->saveLanguage($lang)){

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/language');
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/language', array('action' => 'add'));
        }
        try{
            $lang = $this->getModelTable('LanguagesTable')->getById($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/language');
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

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/language');
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
            $this->getModelTable('LanguagesTable')->softUpdateData($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('LanguagesTable')->softUpdateData($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/language');
    }

    public function unpublishedAction(){
        $request = $this->getRequest();
        if($request->getPost()){
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('LanguagesTable')->softUpdateData($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('LanguagesTable')->softUpdateData($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/language');
    }

    public function manageKeywordsAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 0);
        $page = max($page, 0);
        $q = $this->params()->fromQuery('q', '');

        $languages_ = $this->getModelTable('LanguagesTable')->fetchAll('','', 0, 100);
        
        $all_keywords =  array();
        $languages =  array();
        foreach($languages_ as $lang){
            $languages[] = $lang;
            if(!$this->isMasterPage()){
                $name_folder = $this->website['websites_folder'];
                if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                    require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                    $array_lang = $$lang['languages_file'];
                    $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                    $all_keywords = array_merge($all_keywords, $array_lang);
                }
            }else{
                if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                    require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                    $array_lang = $$lang['languages_file'];
                    $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                    $all_keywords = array_merge($all_keywords, $array_lang);
                }
            } 
        }

        if( !empty($q) ){
            $list_word = array();
            foreach ($all_keywords as $key => $value) {
                if( strpos(strtolower($value), strtolower($q))  !== false 
                    || strpos(strtolower($key), strtolower($q))  !== false ){
                    $list_word[$key] = $value;
                }
            }
            $all_keywords = $list_word;
        }

        $all_keywords =array_keys($all_keywords);
        $total = count($all_keywords);
        $keys = array_splice($all_keywords,$page*$limit, $limit);

        $keywords = array();
        foreach ($keys as $k => $key) {
            $kw = $key;
            $keyword = array('keyword'=>$kw);
            foreach($languages as $lang){
                $array_lang = $$lang['languages_file'];
                $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                $keyword[$lang['languages_file']] = isset($array_lang[$kw]) ? $array_lang[$kw] : '';
            }
            $keywords[] = $keyword;
        }

        $link = '/cms/language/manage-keywords?'.( !empty($q) ? 'q='.$q : 'q=' ).'&page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['q'] = $q;
        $this->data_view['keywords'] = $keywords;
        $this->data_view['languages'] = $languages;
        $this->data_view['paging'] = $paginator->toHtml();
        return $this->data_view;
    }

    public function addKeywordAction(){

        $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            if(trim($keyword) == ''){
                $error[] = 'Từ khóa không được bỏ trống';
            }
            if(!isset($error)){
                $keyword = strtolower(str_replace(' ', '',trim($keyword)));
                $translates = $request->getPost('translate');
                $languages =  array();
                $has_keyword = false;
                foreach($languages_ as $lang){
                    $languages[] = $lang;
                    if(!$this->isMasterPage()){
                        $name_folder = $this->website['websites_folder'];
                        if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                            require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            if(isset($array_lang[$keyword])){
                                $has_keyword = true;
                                break;
                            }
                        }
                    }else{
                        if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                            require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            if(isset($array_lang[$keyword])){
                                $has_keyword = true;
                                break;
                            }
                        }
                    } 
                }

                if(!$has_keyword){
                    foreach($languages as $lang){
                        $array_lang = $$lang['languages_file'];
                        $array_lang[$keyword] = isset($translates[$lang['languages_id']]) ? $translates[$lang['languages_id']] : '';
                        
                        
                        if(!$this->isMasterPage()){
                            $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                            $name_folder = $_SESSION['website']['websites_folder'];
                            $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                        }else{
                            $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']}, '{$lang['languages_file']}');";
                            $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                        }
                        @file_put_contents($path, $data);
                    }
                    return $this->redirect()->toRoute('cms/language', array('action' => 'manage-keywords'));
                }else{
                    return $this->redirect()->toUrl(FOLDERWEB.'/cms/language/edit-keyword?word='.$keyword);
                }
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        $this->data_view['languages'] = $languages_;
        return $this->data_view;
    }

    public function editKeywordAction(){
        $word = $this->params()->fromQuery('word', NULL);
        if(!$word){
            return $this->redirect()->toRoute('cms/language', array('action' => 'add-keyword'));
        }
        $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
        $languages =  array();
        $keyword = array();
        try{
            foreach($languages_ as $lang){
                $languages[] = $lang;
                if(!$this->isMasterPage()){
                    $name_folder = $this->website['websites_folder'];
                    if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                        require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                        $array_lang = $$lang['languages_file'];
                        $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        if(isset($array_lang[$word])){
                            $keyword[$lang['languages_id']] = $array_lang[$word];
                        }
                    }
                }else{
                    if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                        require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                        $array_lang = $$lang['languages_file'];
                        $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        if(isset($array_lang[$word])){
                            $keyword[$lang['languages_id']] = $array_lang[$word];
                        }
                    }
                } 
            }
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/language', array('action' => 'manage-keyword'));
        }
        
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            if(trim($keyword) == ''){
                $error[] = 'Từ khóa không được bỏ trống';
            }
            if(!isset($error)){
                $keyword = strtolower(str_replace(' ', '',trim($keyword)));
                $translates = $request->getPost('translate');
                foreach($languages as $lang){
                    $array_lang = $$lang['languages_file'];
                    $array_lang[$keyword] = isset($translates[$lang['languages_id']]) ? $translates[$lang['languages_id']] : '';
                    
                    
                    if(!$this->isMasterPage()){
                        $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                        $name_folder = $_SESSION['website']['websites_folder'];
                        $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                    }else{
                        $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']}, '{$lang['languages_file']}');";
                        $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                    }
                    @file_put_contents($path, $data);
                }

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/language', array('action' => 'manage-keywords'));
            }else{
                $_SESSION['error'] = $error;
            }
        }
        if(!empty($keyword)){
            $keyword['keyword'] = $word;
        }else{
            return $this->redirect()->toRoute('cms/language', array('action' => 'manage-keyword'));
        }
        $this->data_view['languages'] = $languages;
        $this->data_view['keyword'] = $keyword;
        return $this->data_view;
    }

    public function deleteKeywordAction(){
        $request = $this->getRequest();
        $keywords = array();
        if($request->getPost()){
            $keywords = $request->getPost('cid');
        }else{
            $keyword = $this->params()->fromQuery('word', '');
            if( !empty($keyword) ){
                $keywords[] = $keyword;
            }
        }
        if( !empty($keywords) ){
            $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
            $languages =  array();
            foreach($languages_ as $lang){
                $languages[] = $lang;
            }
            $keywords = $request->getPost('cid');
            foreach($keywords as $word){
                foreach($languages as $lang){
                    if(!$this->isMasterPage()){
                        $name_folder = $this->website['websites_folder'];
                        if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                            require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            if(isset($array_lang[$word])){
                                unset($array_lang[$word]);
                                
                                $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                                $name_folder = $_SESSION['website']['websites_folder'];
                                $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                                @file_put_contents($path, $data);
                            }
                        }
                    }else{
                        if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                            require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            if(isset($array_lang[$word])){
                                unset($array_lang[$word]);
                                
                                $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']}, '{$lang['languages_file']}');";
                                $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                                @file_put_contents($path, $data);
                            }
                        }
                    } 
                }
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/language', array('action' => 'manage-keywords'));
    }

    public function filterKeywordAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            //$keywords = $this->getModelTable('LanguagesTable')->getKeywordsBySearch($keyword);
            $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
            $languages =  array();
            $keywords = array();

            foreach($languages_ as $lang){
                $languages[] = $lang;
                if(!$this->isMasterPage()){
                    $name_folder = $this->website['websites_folder'];
                    if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                        require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                        $array_lang = $$lang['languages_file'];
                        $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        foreach ($array_lang as $key => $value) {
                            if(strpos(strtolower($value), strtolower($keyword))  !== false || strpos(strtolower($key), strtolower($keyword))  !== false ){
                                if(!isset($keywords[$key])){
                                    $keywords[$key] = array('keyword'=>$key, $lang['languages_id'] =>$value);
                                }else{
                                    $keywords[$key][$lang['languages_id']] = $value;
                                }
                            }
                        }
                    }
                }else{
                    if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                        require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                        $array_lang = $$lang['languages_file'];
                        $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        foreach ($array_lang as $key => $value) {
                            if(strpos(strtolower($value), strtolower($keyword))  !== false  || strpos(strtolower($key), strtolower($keyword))  !== false){
                                if(!isset($keywords[$key])){
                                    $keywords[$key] = array('keyword'=>$key, $lang['languages_id'] =>$value);
                                }else{
                                    $keywords[$key][$lang['languages_id']] = $value;
                                }
                            }
                        }
                    }
                } 
            }

            $result = new ViewModel();
            $result->setTerminal(true);
            $result->setVariables(array(
                'keywords' => $keywords,
                'languages'=> $languages,
            ));
            return $result;
        }
    }

    public function popupEditKeywordAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $word = $request->getPost('word');
            $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
            $languages =  array();
            $keyword = array();
            if(!empty($word)){
                $keyword['keyword'] = $word;
                foreach($languages_ as $lang){
                    if( empty($keyword[$lang['languages_id']]) ){
                        $keyword[$lang['languages_id']] = '';
                    }
                    $languages[] = $lang;
                    if(!$this->isMasterPage()){
                        $name_folder = $this->website['websites_folder'];
                        if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                            require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                            if(isset($array_lang[$word])){
                                $keyword['keyword'] = $word;
                                $keyword[$lang['languages_id']] = $array_lang[$word];
                            }
                        }
                    }else{
                        if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                            require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                            if(isset($array_lang[$word])){
                                $keyword['keyword'] = $word;
                                $keyword[$lang['languages_id']] = $array_lang[$word];
                            }
                        }
                    } 
                }
            }
            $result = new ViewModel();
            $result->setTerminal(true);
            $result->setVariables(array(
                'keyword' => $keyword,
                'languages'=> $languages,
            ));

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            return $result;
        }
    }

    public function editKeywordAjackAction(){
        
        $request = $this->getRequest();
        if($request->isPost()){
            $keyword = $request->getPost('keyword');
            $keyword = str_replace(' ', '',trim($keyword));
            $translates = $request->getPost('translate');
            $lang_tr = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
            $value_lang = '';
            if(!empty($keyword)){
                $languages_ = $this->getModelTable('LanguagesTable')->fetchAll();
                $languages =  array();
                foreach($languages_ as $lang){
                    $languages[] = $lang;
                    if(!$this->isMasterPage()){
                        $name_folder = $this->website['websites_folder'];
                        if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                            require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        }
                    }else{
                        if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                            require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                            $array_lang = $$lang['languages_file'];
                            $array_lang = mergeTranslateForRootAdmin($array_lang, $lang['languages_file']);
                        }
                    }
                }

                foreach($languages as $lang){
                    $array_lang = $$lang['languages_file'];
                    $array_lang[$keyword] = isset($translates[$lang['languages_id']]) ? $translates[$lang['languages_id']] : '';
                    
                    
                    if(!$this->isMasterPage()){
                        $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                        $name_folder = $_SESSION['website']['websites_folder'];
                        $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                    }else{
                        $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']}, '{$lang['languages_file']}');";
                        $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                    }
                    @file_put_contents($path, $data);
                    if($lang_tr == $lang['languages_file']){
                        $value_lang = $array_lang[$keyword];
                    }
                }

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                $result = array('flag'=>true, 'keyword'=>$keyword, 'translates' =>$translates, 'value' => $value_lang, 'lang'=>$lang_tr);
                echo json_encode($result);die();
            }
        }

        $result = array('flag'=>false);
        echo json_encode($result);die();
    }

    public function switchTranslateAction(){

        if(empty($_SESSION['CMSMEMBER']['translate']) 
            || (!empty($_SESSION['CMSMEMBER']['translate']) &&  $_SESSION['CMSMEMBER']['translate'] == 0) ){
            $_SESSION['CMSMEMBER']['translate'] = 1;
        }else{
            $_SESSION['CMSMEMBER']['translate'] = 0;
        }
        
        $result = array('flag'=>true);
        echo json_encode($result);
        die();
    }

    public function setLanguageAction(){
        $languages_id = $this->params()->fromRoute('id', 0);
        $result = array('flag'=>FALSE, 'languages_id' => $languages_id);
        if( !empty($languages_id) ){
            if( empty($_SESSION['CMSMEMBER'])){
                $_SESSION['CMSMEMBER'] = array();
            }

            $_SESSION['CMSMEMBER']['languages_id'] = $languages_id;
            
            $result = array('flag'=>TRUE, 'languages_id' => $languages_id);
        }
        echo json_encode($result);
        die();
    }

}