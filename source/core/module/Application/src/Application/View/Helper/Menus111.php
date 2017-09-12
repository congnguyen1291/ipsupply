<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Menus extends App
{
    public function getMenuWithAlias($alias)
    {
        $menu = $this->getModelTable('MenusTable')->getMenuWithAlias($alias);
        return $menu;
    }

    public function getAllMenuAndSort()
    {
        $menus = $this->getModelTable('MenusTable')->getAllAndSort();
        return $menus;
    }

    public function getLinkForMenu($menu)
    {
        $link = '';
        if(!empty($menu)){
            switch ($menu['menus_type']) {
                case 'frontpage':
                    $link = FOLDERWEB;
                    break;
                case 'allcollection':
                    $link = FOLDERWEB . '/category';
                    break;
                case 'collection':
                    $link = FOLDERWEB . '/category/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'];
                    break;
                case 'product':
                    $link = FOLDERWEB . '/'.$this->toAlias($product['menus_reference_name']).'-'.$product['menus_reference_id'];
                    break;
                case 'catalog':
                    $link = FOLDERWEB . '/category';
                    break;
                case 'article':
                case 'page':
                    $link = FOLDERWEB . '/articles/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'].'.html';
                    break;
                case 'allblog':
                    $link = FOLDERWEB . '/articles';
                    break;
                case 'blog':
                    $link = FOLDERWEB . '/articles/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'];
                    break;
                case 'search':
                    $link = FOLDERWEB . '/search';
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
        if( !empty($menu) && $this->isSubCollection() ){
            return 0;
        }
        return 0;
    }

    public function getCategoriesArticlesId($menu){
        if( !empty($menu) && $this->isSubBlog() ){
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

    public function isSubCollection($menu){
        if( !empty($menu) 
            && !empty($menu['menus_type'])
            && $menu['menus_type'] == 'subcollection' ){
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
	
}
