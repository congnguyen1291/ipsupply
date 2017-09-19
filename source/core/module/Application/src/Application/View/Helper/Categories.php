<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Categories extends App
{
    public function getAllGoldTimerCurrent($params = array(), $intPage=0, $intPageSize=100)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllGoldTimerCurrent('.(is_array($params) ? implode('-',$params) : $params).$intPage.';'.$intPageSize.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent($params, $intPage, $intPageSize);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getDatasForCategory($categories, $limit=100)
    {
        $list_id  = array();
        foreach ($categories as $key => $cat) {
            $list_id[] = $cat['categories_id'];
        }
        $key = md5($this->getNamspaceCached().':CategoriesTable:getDataHomepage('.implode('-',$list_id).'-'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getDataHomepage($categories, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getBreadCrumb($id, $breadCrumbMore = '')
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBreadCrumb('.(is_array($id) ? implode('-',$id) : $id).';'.$breadCrumbMore.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getBreadCrumb($id, $breadCrumbMore);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
    public function getLeftMenuPageCategory($id)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getLeftMenuPageCategory('.(is_array($id) ? implode('-',$id) : $id).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getLeftMenuPageCategory($id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
    public function countTotalProduct($cate)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:countTotalProduct('.(is_array($cate) ? implode('-',$cate) : $cate).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->countTotalProduct($cate);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getHtmlLeftFilterFeature($list, $feature)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getHtmlLeftFilterFeature('.(is_array($list) ? implode('-',$list) : $list).','.(is_array($feature) ? implode('-',$feature) : $feature).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getHtmlLeftFilterFeature($list, $feature);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getCategoriesUrl($cat = array()){
        $url = FOLDERWEB .$this->getUrlPrefixLang(). '/'.$cat['categories_alias'].'-'.$cat['categories_id'].'s.html';
        return $url;
    }

    public function getAllParentsOfCategory($categories_id, $has_me = false)
    {
        $_has_me = 0;
        if($has_me){
            $_has_me = 1;
        }
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllParentsOfCategory('.$categories_id.','.$_has_me.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getAllParentsOfCategory($categories_id, $has_me);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllCategoriesSort()
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategoriesSort');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getAllCategoriesSort();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllCategoriesSortWithKeyValue()
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategoriesSortKeyValue');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getAllCategoriesSortWithKeyValue();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getCategoriesIdChildOfParentID($categories_id, $has_me = false)
    {
        $_has_me = 0;
        if($has_me){
            $_has_me = 1;
        }
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).','.$_has_me.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            if( $has_me ){
                if( !empty($results) )
                    $results[] = $categories_id;
                else
                    $results = $categories_id;
            }
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getBannersOfCategories($categories_id)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBannerForCat('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesTable')->getBannerForCat($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getRoot($categories){
        if( !empty($categories) && !empty($categories[0]) ){
            return $categories[0];
        }
        return array();
    }

    public function getId($categories){
        if( !empty($categories) && !empty($categories['categories_id']) ){
            return $categories['categories_id'];
        }
        return '';
    }

    public function getTitle($categories){
        if( !empty($categories) && !empty($categories['categories_title']) ){
            return $categories['categories_title'];
        }
        return '';
    }

    public function getNumberProduct($categories){
        if( !empty($categories) 
            && !empty($categories['number_product']) ){
            return $categories['number_product'];
        }
        return 0;
    }

    public function getIcon($categories){
        if( !empty($categories) && !empty($categories['icon']) ){
            return $categories['icon'];
        }
        return '';
    }

    public function getDescription($categories){
        if( !empty($categories) && !empty($categories['categories_description']) ){
            return html_entity_decode($categories['categories_description'], ENT_QUOTES, 'UTF-8');
        }
        return '';
    }

    public function getChildrens($categories, $categories_id){
        if( !empty($categories)
            && !empty($categories_id) 
            && !empty($categories[$categories_id]) ){
            return $categories[$categories_id];
        }
        return array();
    }
	
}
