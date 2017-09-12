<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\WebsiteForm;
use Cms\Model\Websites;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class MenusController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'menus';
    }

    
    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $menus = $this->getModelTable('MenusTable')->getAllAndSort();
        $this->data_view['menus'] = $menus;
        return $this->data_view;
    }

    public function addAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        if($id){
            $menu = $this->getModelTable('MenusTable')->getMenusById($id);
            $this->data_view['menu'] = $menu;
        }

        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $languages_ = $this->getModelTable('LanguagesTable')->fetchAll('','', 0, 100);
            $languages =  array();
            $list_keyword =  array();
            try{
                foreach($languages_ as $lang){
                    $languages[] = $lang;
                    if(is_file(LANG_PATH.'/'.$lang['languages_file'].'.php')){
                        require_once LANG_PATH.'/'.$lang['languages_file'].'.php';
                    }
                    $list_keyword[$lang['languages_id']] = $$lang['languages_file'];
                }
            }catch(\Exception $e){}

            if( isset($data['Title']) && !empty($data['Title']) ){
                $menu = array();
                if( !empty($data['menus_id']) ){
                    $menu = $this->getModelTable('MenusTable')->getMenusById($data['menus_id']);
                }

                $str_alias = '';
                if( empty($menu) && isset($data['Alias']) && !empty($data['Alias'])){
                    $str_alias = $this->getStringUpcaseFriendly($data['Alias']);

                    $together_alias = $this->getModelTable('MenusTable')->getMenuByAlias($str_alias);
                    if(!(empty($together_alias) || (isset($menu) && $menu->menus_alias == $str_alias))){
                        $str_alias .= '_'.count($together_alias);
                    }
                }else if( !empty($menu) ){
                    $str_alias = $menu->menus_alias;
                }

                if(!empty($str_alias)){
                    $row = array('website_id'=>ID_MASTERPAGE,
                                'menus_name'=>$data['Title'],
                                'menus_alias'=>$str_alias,
                                'is_published'=>$data['is_published']);

                    $txt_menu_name = '';
                    if( isset($menu) && !empty($menu) ){
                        $txt_menu_name = 'txt_menu_'.$data['menus_id'];
                        $row['menus_name'] = $txt_menu_name;
                        $this->getModelTable('MenusTable')->editMenus($row, array('menus_id' =>$data['menus_id'], 'website_id'=>ID_MASTERPAGE ));
                    }else{
                        $data['menus_id'] = $this->getModelTable('MenusTable')->insertMenus($row);
                        $txt_menu_name = 'txt_menu_'.$data['menus_id'];
                        $row = array('menus_name'=>$txt_menu_name);
                        $this->getModelTable('MenusTable')->editMenus($row, array('menus_id' =>$data['menus_id'], 'website_id'=>ID_MASTERPAGE ));
                    }

                    if(!empty($txt_menu_name)){
                        foreach($languages as $lang){
                            $list_keyword[$lang['languages_id']][$txt_menu_name] = $data['Title'];
                        }
                    }
                    
                    $list_link_update = array();
                    if( isset($data['Links']) && !empty($data['Links']) && isset($data['menus_id']) && !empty($data['menus_id'])  ){
                        foreach ($data['Links'] as $key => $Link) {
                            $menu_link = null;
                            if(isset($Link['Title']) && !empty($Link['Title'])){
                                if( isset($Link['Id']) && !empty($Link['Id']) ){
                                    $menu_link = $this->getModelTable('MenusTable')->getMenusById($Link['Id']);
                                }
                                $sub_alias = $this->getStringUpcaseFriendly($Link['Title']);
                                $menus_alias = $this->getModelTable('MenusTable')->getMenuByAlias($sub_alias);
                                if(!(empty($menus_alias) || (isset($menu_link) && $menu_link->menus_alias == $sub_alias))){
                                    $sub_alias .= '_'.count($menus_alias);
                                }
                                $row = array('website_id'=>ID_MASTERPAGE,
                                    'parent_id'=>$data['menus_id'],
                                    'menus_alias'=>$sub_alias,
                                    'menus_type'=>$Link['Type'],
                                    'ordering'=>$Link['Position'],
                                    'menus_reference_id'=>$Link['ItemId'],
                                    'menus_reference_name'=>$Link['ItemName'],
                                    'is_published'=>$data['is_published']);
                                if(in_array($Link['Type'], array('collection', 'product', 'page', 'article', 'blog'))){
                                    $row['menus_reference_id']=$Link['ItemId'];
                                }else if($Link['Type'] == 'http' && !empty($Link['Url'])){
                                    $row['menus_reference_url']=$Link['Url'];
                                }

                                $txt_menu_name = '';
                                $txt_menus_reference_name = '';
                                $txt_menus_description = '';
                                if( isset($menu_link) && !empty($menu_link) ){
                                    $txt_menu_name = 'txt_menu_name_'.$Link['Id'];
                                    $row['menus_name'] = $txt_menu_name;
                                    
                                    if($Link['Type'] == 'description' && !empty($Link['Description'])){
                                        $txt_menus_description = 'txt_menus_description_'.$Link['Id'];
                                        $row['menus_description'] = $txt_menus_description;
                                    }

                                    $this->getModelTable('MenusTable')->editMenus($row, array('menus_id' =>$Link['Id'], 'website_id'=>ID_MASTERPAGE ));
                                }else{
                                    $Link['Id'] = $this->getModelTable('MenusTable')->insertMenus($row);

                                    $txt_menu_name = 'txt_menu_name_'.$Link['Id'];
                                    $row = array('menus_name'=>$txt_menu_name);

                                    if($Link['Type'] == 'description' && !empty($Link['Description'])){
                                        $txt_menus_description = 'txt_menus_description_'.$Link['Id'];
                                        $row['menus_description'] = $txt_menus_description;
                                    }

                                    $this->getModelTable('MenusTable')->editMenus($row, array('menus_id' =>$Link['Id'], 'website_id'=>ID_MASTERPAGE ));
                                }
                                

                                if(!empty($txt_menu_name)){
                                    foreach($languages as $lang){
                                        $list_keyword[$lang['languages_id']][$txt_menu_name] = $Link['Title'];
                                        if($Link['Type'] == 'description' && !empty($Link['Description'])){
                                            $list_keyword[$lang['languages_id']][$txt_menus_description] = $Link['Description'];
                                        }
                                    }
                                }

                                $list_link_update[] = $Link['Id'];
                            }
                        }
                    }
                }else{
                    return $this->redirect()->toRoute('cms/menus', array(
                        'action' => 'add'
                    ));
                }
            }
            
            $menus = $this->getModelTable('MenusTable')->getAllAndSort();
            if(isset($menus[$data['menus_id']])){
                foreach ($menus[$data['menus_id']] as $key => $mdel) {
                    $menus_id = $mdel['menus_id'];
                    if(!in_array($menus_id, $list_link_update )){
                        if(isset($menus[$menus_id])){
                            foreach ($menus[$menus_id] as $key => $mdelc) {
                                $menus_idc = $mdelc['menus_id'];
                                if(!in_array($menus_idc, $list_link_update )){
                                    $this->getModelTable('MenusTable')->delete($menus_idc);
                                    $txt_menu_name_idc = 'txt_menu_name_'.$menus_idc;
                                    $txt_menus_description_idc = 'txt_menus_description_'.$menus_idc;
                                    foreach($languages as $lang){
                                        unset($list_keyword[$lang['languages_id']][$txt_menu_name_idc]);
                                        unset($list_keyword[$lang['languages_id']][$txt_menus_description_idc]);
                                    }
                                }
                            }
                        }
                        $this->getModelTable('MenusTable')->delete($menus_id);
                        $txt_menu_name = 'txt_menu_name_'.$menus_id;
                        $txt_menus_description = 'txt_menus_description_'.$menus_id;
                        foreach($languages as $lang){
                            $array_lang = $$lang['languages_file'];
                            unset($list_keyword[$lang['languages_id']][$txt_menu_name]);
                            unset($list_keyword[$lang['languages_id']][$txt_menus_description]);
                        }
                    }
                }
            }
            
            foreach($languages as $lang){
                $langs = $list_keyword[$lang['languages_id']];
                $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($langs,true). "; \r\n return megeTranslate(\${$lang['languages_file']}, '{$lang['languages_file']}');";
                $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                @file_put_contents($path, $data);
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            return $this->redirect()->toRoute('cms/menus', array(
                'action' => 'index'
            ));
        }
        $menus = $this->getModelTable('MenusTable')->getAllAndSort();
        $this->data_view['menus'] = $menus;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        if($id){
            $menu = $this->getModelTable('MenusTable')->getMenusById($id);
            if( !empty($menu) ){
                $menus = $this->getModelTable('MenusTable')->getAllAndSort();
                if(isset($menus[$menu->menus_id])){
                    foreach ($menus[$menu->menus_id] as $key => $mdel) {
                        $menus_id = $mdel['menus_id'];
                        if(isset($menus[$menus_id])){
                            foreach ($menus[$menus_id] as $keyc => $mdelc) {
                                $menus_idc = $mdelc['menus_id'];
                                if(isset($menus[$menus_idc])){
                                    foreach ($menus[$menus_idc] as $keycv => $mdelcv) {
                                        $menus_idcv = $mdelcv['menus_id'];
                                        $this->getModelTable('MenusTable')->delete($menus_idcv);
                                    }
                                }
                                $this->getModelTable('MenusTable')->delete($menus_idc);
                            }
                        }
                        $this->getModelTable('MenusTable')->delete($menus_id);
                    }
                }
                $this->getModelTable('MenusTable')->delete($menu->menus_id);
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/menus', array(
            'action' => 'index'
        ));
    }

}