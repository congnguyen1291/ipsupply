<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Menus extends App
{
    public function getMenuWithAlias($alias)
    {
        $key = md5($this->getNamspaceCached().':MenusTable:getMenuWithAlias('.$alias.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('MenusTable')->getMenuWithAlias($alias);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllMenuAndSort()
    {
        $key = md5($this->getNamspaceCached().':MenusTable:getAllAndSort');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('MenusTable')->getAllAndSort();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getLinkForMenu($menu)
    {
        $link = '';
        if(!empty($menu)){
            switch ($menu['menus_type']) {
                case 'frontpage':
                    $link = FOLDERWEB.$this->getUrlPrefixLang();
                    break;
                case 'allcollection':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category';
                    break;
                case 'collection':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id']."s.html";
                    break;
                case 'product':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/'.$this->toAlias($product['menus_reference_name']);
                    break;
                case 'catalog':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category';
                    break;
                case 'article':
                case 'page':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'].'.html';
                    break;
                case 'allblog':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles';
                    break;
                case 'blog':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'];
                    break;
                case 'articleNew':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles/news';
                    break;
                case 'articleNewInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles/news';
                    break;
                case 'articleHot':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles/hots';
                    break;
                case 'articleHotInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/articles/hots';
                    break;
                case 'GoingOn':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/going-on';
                    break;
                case 'GoingOnInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/going-on';
                    break;
                case 'SignIn':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/sign-in';
                    break;
                case 'SignUp':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/sign-up';
                    break;
                case 'user':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/profile';
                    break;
                case 'contact':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/contact';
                    break;
                case 'error':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/error';
                    break;
                case 'search':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/search';
                    break;
                case 'bestseller':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/bestseller';
                    break;
                case 'BestsellerInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/bestseller';
                    break;
                case 'BuyMost':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/buymost';
                    break;
                case 'BuyMostInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/buymost';
                    break;
                case 'Hot':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/hots';
                    break;
                case 'HotInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/hots';
                    break;
                case 'New':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/news';
                    break;
                case 'NewInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/news';
                    break;
                case 'Deal':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/deals';
                    break;
                case 'DealInCategory':
                    $link = FOLDERWEB . $this->getUrlPrefixLang().'/category/deals';
                    break;
                case 'http':
                    $link = $menu['menus_reference_url'];
                    break;
                default:
                    $link = 'javascript:void(0);';
                    break;
            }
        }
        return $link;
    }

    public function getMenuRoot($catid){
        if(!is_dir('./module/menu_cached')){
            @mkdir('./module/menu_cached',0777);
        }
        $menu_cached = './module/menu_cached/menu-sub-'.$catid.'.cache';
        if(is_file($menu_cached)){
            $htmlSub = @file_get_contents($menu_cached);
            return $htmlSub;
        }
        return FALSE;
    }

    public function getId($menu){
        if( !empty($menu) && !empty($menu['menus_id']) ){
            return $menu['menus_id'];
        }
        return '';
    }

    public function getCategoriesId($menu){
        if( !empty($menu) && $this->isOneCollection($menu) ){
            return $menu['menus_reference_id'];
        }
        return 0;
    }

    public function getCategoriesArticlesId($menu){
        if( !empty($menu) && $this->isSubBlog($menu) ){
            return 0;
        }
        return 0;
    }

    public function getClassStatusActive($menu){
        return '';
    }

    public function getName($menu){
        if( !empty($menu) && !empty($menu['menus_name']) ){
            $translator = $this->getTranslator ();
            return $translator->translate($menu['menus_name']);
        }
        return '';
    }

    public function getDescription($menu){
        if( !empty($menu) && !empty($menu['menus_description']) ){
            $translator = $this->getTranslator ();
            return $translator->translate($menu['menus_description']);
        }
        return '';
    }

    public function getChildrens($menus, $menus_id){
        if( !empty($menus)
            && !empty($menus_id) 
            && !empty($menus[$menus_id]) ){
            return $menus[$menus_id];
        }
        return array();
    }

    public function isCollection($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'allcollection' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isOneCollection($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'collection' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isSubCollection($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'subcollection' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isBestseller($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'bestseller' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isBestsellerInCategory($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'BestsellerInCategory' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isBuyMost($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'BuyMost' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isBuyMostInCategory($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'BuyMostInCategory' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isHot($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'Hot' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isNew($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'New' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isHotInCategory($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'HotInCategory' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isNewInCategory($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'NewInCategory' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isDeal($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'Deal' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isDealInCategory($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'DealInCategory' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isBlog($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'allblog' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isSubBlog($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'subblog' ){
            return TRUE;
        }
        return FALSE;
    }

    public function isDescription($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'description' ){
            return TRUE;
        }
        return FALSE;
    }

    public function hasChildrens($menus, $menu, $categories, $categoryArticles){
        $childrens = $this->getChildrens($menus, $this->getId($menu));
        $categoriesRoot = $this->view->Categories()->getRoot($categories);
        $categoriesArticlesRoot = $this->view->CategoriesArticles()->getRoot($categoryArticles);
        if( (!empty($childrens) && !$this->isCollection($menu) && !$this->isBlog($menu))
            || ($this->isCollection($menu) && !empty($categoriesRoot))
            || ($this->isBlog($menu) && !empty($categoriesArticlesRoot)) ){
            
            return TRUE;
        }
        return FALSE;
    }

    public function getProducts($menu){
        $products = array();
        if( !empty($menu['menus_reference_id']) 
            && ($this->isOneCollection($menu) || $this->isSubCollection($menu)) ){
            $products = $this->view->Products()->getProductInCategory($menu['menus_reference_id']);
        }else if( $this->isBestseller($menu) ){
            $products = $this->view->Products()->getProductBestseller(0, 8);
        }else if( !empty($menu['menus_reference_id']) && $this->isBestsellerInCategory($menu) ){
            $products = $this->view->Products()->getProductBestsellerInCategory($menu['menus_reference_id'], 0, 8);
        }else if( $this->isBuyMost($menu) ){
            $products = $this->view->Products()->getProductBuyMost(0, 8);
        }else if( !empty($menu['menus_reference_id']) && $this->isBuyMostInCategory($menu) ){
            $products = $this->view->Products()->getProductBuyMostInCategory($menu['menus_reference_id'], 0, 8);
        }else if( $this->isHot($menu) ){
            $products = $this->view->Products()->getHotProduct(0, 8);
        }else if( !empty($menu['menus_reference_id']) && $this->isHotInCategory($menu) ){
            $products = $this->view->Products()->getHotProductInCategory($menu['menus_reference_id'], 0, 8);
        }else if( $this->isNew($menu) ){
            $products = $this->view->Products()->getNewProduct(0, 8);
        }else if( !empty($menu['menus_reference_id']) && $this->isNewInCategory($menu) ){
            $products = $this->view->Products()->getNewProductInCategory($menu['menus_reference_id'], 0, 8);
        }else if( $this->isDeal($menu) ){
            $products = $this->view->Products()->getSaleProduct(0, 8);
        }else if( !empty($menu['menus_reference_id']) && $this->isDealInCategory($menu) ){
            $products = $this->view->Products()->getSaleProductInCategory($menu['menus_reference_id'], 0, 8);
        }
        return $products;
    }
	
}
