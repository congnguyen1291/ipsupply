<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Products extends App
{
    public function getProductsUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
    	    $url = FOLDERWEB . $this->getUrlPrefixLang().'/'.$product['products_alias'];
        }
        return $url;
    }

    public function getAddToCartProductsUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/cart/popAddToCart/'.$product['products_id'];
        }
        return $url;
    }

    public function getQuickviewProductsUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/quickview/'.$product['products_alias'].'-'.$product['products_id'];
        }
        return $url;
    }

    public function getBuyProductsByEmailUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/buyByEmail/'.$product['products_alias'].'-'.$product['products_id'];
        }
        return $url;
    }

    public function getHeartProductsUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/heart/'.$product['products_alias'].'-'.$product['products_id'];
        }
        return $url;
    }

    public function getBuyFastProductsUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/cart/popBuyFastProduct/'.$product['products_id'];
        }
        return $url;
    }

    public function getCartExtentionUrl( $product = array() ){
        $url = 'javascript:void(0);';
        if( !empty($product) ){
            $url = FOLDERWEB . $this->getUrlPrefixLang().'/cart/getExtention?products_id='.$product['products_id'].'&product_type='.$product['products_type_id'];
        }
        return $url;
    }

    public function getHotProduct($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProduct('.$offset .';'. $limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getHotProduct($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'HotProduct');
        return $results;
    }

    public function getProductBanchay($offset=0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBanchay('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductBanchay($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductBanchay');
        return $results;
    }

    public function getNewProduct($offset=0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getNewProduct('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getNewProduct($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'NewProduct');
        return $results;
    }

    public function getNewProductInCategory($categories_id, $offset=0, $limit = 5)
    {
        $stri_key = '';
        if( is_array($categories_id) ){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getNewProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getNewProductInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'NewProductInCategory');
        return $results;
    }

    public function getImages($products_id)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getImages('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getImages($products_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getProductInCategory($categories_id, $params = array())
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $child = $this->view->Datas()->popIDataHashMap($key);
        if( !$child ) {
            $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $child);
        }
        $child[] = $categories_id;

        $stri_key = '';
        if(is_array($child)){
            $stri_key = $this->createKeyCacheFromArray($child);
        }else{
            $stri_key = $child;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCate('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductCate($child, $params);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductInCategory');
        return $results;
    }

    public function getListProducts($params = array())
    {
        $stri_key = '';
    
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductAll('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductAll($params);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ListProducts');
        return $results;
    }

    public function getFqaProduct($products_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getAllFqa('.$stri_key.';'.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getAllFqa($products_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getTotalFqaProduct($products_id)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTotalFqaProduct('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getTotalFqa($products_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getFqaChild($id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getAllFqa('.$stri_key.';'.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getAllFqaChild($id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getExtensionsRequireForProduct($products_id)
    {
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsRequire('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getExtensionsRequire($products_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getProductNearPrice($categories_id, $products_id, $price_sale, $price_min, $price_max, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        if(is_array($products_id)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key .= '-'.$products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductNearPrice11('.$stri_key.';'.$price_sale.';'.$price_min.';'.$price_max.';'.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductNearPrice($categories_id, $products_id, $price_sale, $price_min, $price_max, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductNearPrice');
        return $results;
    }

    public function getRecommendProductById($products_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getRecommendProductById('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getRecommendProductById($products_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'RecommendProductById');
        return $results;
    }


    public function getTagsProduct($products_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTagsProduct('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getTagsProduct($products_id, $offset, $limit );
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
    public function getExtensionsProduct($products_id)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsProduct('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getExtensionsProduct($products_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
    public function getTypeProduct($products_id)
    {
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTypeProduct('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getTypeProduct($products_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getPrice($product)
    {
        if(empty($product['products_type_id'])){
            return $product['price']+$product['total_price_extention'];
        }else{
            return $product['t_price']+$product['total_price_extention'];
        }
    }

    public function getPriceSimple($product)
    {
        if(empty($product['products_type_id'])){
            return $product['price'];
        }else{
            return $product['t_price'];
        }
    }

    public function getPriceSale($product)
    {
        $price = $this->getPriceSaleSimple($product);
        return $price+$product['total_price_extention'];
    }

    public function getPriceSaleSimple($product)
    {
        if(empty($product['products_type_id'])){
            if(!empty($product['price']) && empty($product['price_sale'])){
                return $product['price'];
            }
            return $product['price_sale'];
        }else{
            if(!empty($product['t_price']) && empty($product['t_price_sale'])){
                return $product['t_price'];
            }
            return $product['t_price_sale'];
        }
    }

    public function getIsAvailable($product)
    {
        if(empty($product['products_type_id'])){
            return $product['is_available'];
        }else{
            return $product['t_is_available'];
        }
    }

    public function getQuantity($product)
    {
        if(empty($product['products_type_id'])){
            return $product['quantity'];
        }else{
            return $product['t_quantity'];
        }
    }

    public function getExtension($product)
    {
        if( !empty($product['extensions']) ){
            return $product['extensions'];
        }
        return array();
    }

    public function getExtensionRequire($product)
    {
        if( !empty($product['extensions_require']) ){
            return $product['extensions_require'];
        }
        return array();
    }

    public function getIsGoingOn($product)
    {
        return $product['is_goingon'];
    }

    public function getIsDelete($product)
    {
        return $product['is_delete'];
    }

    public function getIsPublished($product)
    {
        return $product['is_published'];
    }

    public function getNameType($product)
    {
        if(empty($product['products_type_id'])){
            return $product['products_title'];
        }else{
            return $product['type_name'];
        }
    }

    public function getTypeName($product)
    {
        if( empty($product['products_type_id']) ){
            return '';
        }else{
            return $product['type_name'];
        }
    }

    public function getToBuy( $product = array() ){
        if( !empty($product) ){
            $price_sale = $this->getPriceSale($product);
            $price = $this->getPrice($product);
            $is_available = $this->getIsAvailable($product);//quản lý kho
            $quantity = $this->getQuantity($product);
            $is_goingon = $this->getIsGoingOn($product);
            $is_delete = $this->getIsDelete($product);
            $is_published = $this->getIsPublished($product);
            /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
            hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
            if( !(($is_available == 1 && $quantity <= 0) 
                || $is_delete == 1 || $is_published == 0 || $price_sale <= 0 || $is_goingon == 1) ){
                return true;
            }
        }
        return false;
    }

    //sp deal
    public function getDealProduct($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getDealProduct('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getDealProduct($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    //sp giam gia
    public function getSaleProduct($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getSaleProduct('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getSaleProduct($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getHotProductInCategoryV2($categories_id, $offset = 0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $child = $this->view->Datas()->popIDataHashMap($key);
        if( !$child ) {
            $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $child);
        }

        $child[] = $categories_id;

        $stri_key = '';
        if(is_array($child)){
            $stri_key = $this->createKeyCacheFromArray($child);
        }else{
            $stri_key = $child;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getHotProductInCategory($child, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'HotProductInCategory');
        return $results;
    }

    public function getHotProductInCategory($categories_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getHotProductInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'HotProductInCategory');
        return $results;
    }

    public function getDealProductInCategoryV2($categories_id, $offset = 0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $child = $this->view->Datas()->popIDataHashMap($key);
        if( !$child ) {
            $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $child);
        }

        $child[] = $categories_id;

        $stri_key = '';
        if(is_array($child)){
            $stri_key = $this->createKeyCacheFromArray($child);
        }else{
            $stri_key = $child;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getDealProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getDealProductInCategory($child, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'DealProductInCategory');
        return $results;
    }

    public function getDealProductInCategory($categories_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getDealProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getDealProductInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'DealProductInCategory');
        return $results;
    }

    public function getSaleProductInCategoryV2($categories_id, $offset = 0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $child = $this->view->Datas()->popIDataHashMap($key);
        if( !$child ) {
            $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $child);
        }

        $child[] = $categories_id;

        $stri_key = '';
        if(is_array($child)){
            $stri_key = $this->createKeyCacheFromArray($child);
        }else{
            $stri_key = $child;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getSaleProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getSaleProductInCategory($child, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'SaleProductInCategory');
        return $results;
    }

    public function getSaleProductInCategory($categories_id, $offset = 0, $limit = 5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getSaleProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getSaleProductInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'SaleProductInCategory');
        return $results;
    }

    public function getGoingOnProduct($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getGoingOnProduct('.$offset .';'. $limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getGoingOnProduct($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'GoingOnProduct');
        return $results;
    }

    public function getGoingOnProductInCategory($categories_id, $offset = 0, $limit=5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getGoingOnProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getGoingOnProductInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'GoingOnProductInCategory');
        return $results;
    }

    public function getProductBestseller($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBestseller('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductBestseller($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductBestseller');
        return $results;
    }

    public function getProductBestsellerInCategory($categories_id, $offset = 0, $limit=5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBestsellerInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductBestsellerInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductBestsellerInCategory');
        return $results;
    }

    public function getProductBuyMost($offset = 0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBuyMost('.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductBuyMost($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductBuyMost');
        return $results;
    }

    public function getProductBuyMostInCategory($categories_id, $offset = 0, $limit=5)
    {
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBuyMostInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getProductBuyMostInCategory($categories_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        $this->setJavascriptProduct($results, 'ProductBuyMostInCategory');
        return $results;
    }

    public function getPromotion($p)
    {
        $promotion = '';
        if(!empty($p)){
            $price = $this->getPrice($p);
            $price_sale = $this->getPriceSale($p);
            $promotion = isset($p['promotion_description']) ? $p['promotion_description'] :  null;
            if (!$promotion) {
                $promotion = isset($p['promotion1_description']) ? $p['promotion1_description'] :  null;
            }
            if (!$promotion) {
                $promotion = isset($p['promotion2_description']) ? $p['promotion2_description'] :  null;
            }
            if (!$promotion) {
                $promotion = isset($p['promotion3_description']) ? $p['promotion3_description'] :  null;
            }
            if (!empty($promotion)){
                $promotion = $promotion;
            }else if( !empty($price_sale) 
                    && !empty($price)
                    && $price_sale < $price 
                    && floor((1-($price_sale/$price))*100) > 0){
                $promotion = '-'.floor((1-($price_sale/$price))*100).'%';
            }
        }
        return $promotion;
    }

    public function getName($p)
    {
        $name = '';
        if( !empty($p) 
            && !empty($p['products_title']) ){
            $name = $p['products_title'];
            if(!empty($p['type_name'])){
               $name .= '('.$p['type_name'].')';
            }
            //if(!empty($p['title_extention_always'])){
                //$name .= "<span class='coz-plug' >{$p['title_extention_always']}</span>";
            //}
        }
        return $name;
    }

    public function getProductsId($p)
    {
        $products_id = '';
        if( !empty($p) && !empty($p['products_id']) ){
            $products_id = $p['products_id'];
        }
        return $products_id;
    }

    public function getCategoriesId($p)
    {
        $categories_id = '';
        if( !empty($p) && !empty($p['categories_id']) ){
            $categories_id = $p['categories_id'];
        }
        return $categories_id;
    }

    public function getProductTypeId($p)
    {
        $products_id = '';
        if( !empty($p) && !empty($p['products_type_id']) ){
            $products_id = $p['products_type_id'];
        }
        return $products_id;
    }

    public function getProductTypeName($p)
    {
        $type_name = '';
        if( !empty($p) && !empty($p['type_name']) ){
            $type_name = $p['type_name'];
        }
        return $type_name;
    }

    public function getExtensionId($extension)
    {
        $id = '';
        if( !empty($extension) && !empty($extension['id']) ){
            $id = $extension['id'];
        }
        return $id;
    }

    public function getExtensionName($extension)
    {
        $ext_name = '';
        if( !empty($extension) && !empty($extension['ext_name']) ){
            $ext_name = $extension['ext_name'];
        }
        return $ext_name;
    }

    public function getExtensionPrice($extension)
    {
        $price = '';
        if( !empty($extension) && !empty($extension['price']) ){
            $price = $extension['price'];
        }
        return $price;
    }

    public function isExtensionAlways($extension)
    {
        if( !empty($extension) && $extension['is_always'] == 1 ){
            return true;
        }
        return false;
    }

    public function getTitle($p)
    {
        $name = '';
        if( !empty($p) && !empty($p['products_title']) ){
            $name = $p['products_title'];
        }
        return $name;
    }

    public function getCode($p)
    {
        $code = '';
        if( !empty($p) && !empty($p['products_code']) ){
            $code = $p['products_code'];
        }
        return $code;
    }

    public function getProductsDescription($p)
    {
        $products_description = '';
        if( !empty($p) && !empty($p['products_description']) ){
            $products_description = html_entity_decode($p['products_description'], ENT_QUOTES, 'UTF-8');
        }
        return $products_description;
    }

    public function getProductsMore($p)
    {
        $products_more = '';
        if( !empty($p) && !empty($p['products_more']) ){
            $products_more = html_entity_decode(preg_replace('/(<(?!img)\w+[^>]+)(font-family)([^>]*)(>)/', '${1}${3}${4}', $p['products_more']), ENT_QUOTES, 'UTF-8');
        }
        return $products_more;
    }

    public function getProductsLongdescription($p)
    {
        $products_longdescription = '';
        if( !empty($p) && !empty($p['products_longdescription']) ){
            $products_longdescription = html_entity_decode(preg_replace('/(<(?!img)\w+[^>]+)(font-family)([^>]*)(>)/', '${1}${3}${4}', $p['products_longdescription']), ENT_QUOTES, 'UTF-8');
        }
        return $products_longdescription;
    }

    public function getYoutubeVideo($p)
    {
        $youtube_video = '';
        if( !empty($p) && !empty($p['youtube_video']) ){
            $youtube_video = $p['youtube_video'];
        }
        return $youtube_video;
    }

    public function getImage($p)
    {
        $thumb_image = '';
        if( !empty($p) ){
            $thumb_image = $p['thumb_image'];
            if( !empty($p['t_thumb_image']) ){
                $thumb_image = $p['t_thumb_image'];
            }
        }
        return $thumb_image;
    }

    public function getThumbs($p)
    {
        $thumb_image = array();
        if( !empty($p) ){
            if( !empty($p['list_thumb_image']) ){
                try{
                    $list_thumbs = json_decode($p['list_thumb_image'], TRUE);
                    if( !empty($list_thumbs) 
                        && is_array($list_thumbs) ){
                        foreach ($list_thumbs as $key => $item_thumb ) {
                            if( !empty($item_thumb['src']) ){
                                $thumb_image[] = $item_thumb['src'];
                            }
                        }
                    }
                } catch ( \Exception $e ) {
                    $thumb_image = array();
                }
            }

            if( empty($thumb_image) 
                && !empty($p['thumb_image']) ){
                $thumb_image = array($p['thumb_image']);
            }
        }
        return $thumb_image;
    }

    public function getRating($p)
    {
        $rating = 0;
        if( !empty($p) && !empty($p['rating']) ){
            $rating = $p['rating'];
        }
        return $rating;
    }

    public function getManufacturersName($p)
    {
        $manufacturers_name = '';
        if( !empty($p) && !empty($p['manufacturers_name']) ){
            $manufacturers_name = $p['manufacturers_name'];
        }
        return $manufacturers_name;
    }

    public function getLink($p)
    {
        $link = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $link = $this->getQuickviewProductsUrl($p);
            }else{
                $link = $this->getProductsUrl($p);
            }
        }
        return $link;
    }

    public function getTypeView($p)
    {
        $type = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $type = 'data-neo="popup"';
            }
        }
        return $type;
    }

    public function getTagsInCategory($categories_id = 0, $offset = 0, $limit = 5)
    {
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($categories_id) ? implode('-',$categories_id) : $categories_id).')');
        $child = $this->view->Datas()->popIDataHashMap($key);
        if( !$child ) {
            $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
            $this->view->Datas()->pushIDataHashMap($key, $child);
        }

        $child[] = $categories_id;

        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTagsProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ProductsTable')->getTagsProductInCategory($child, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getTagsProductInCategory($categories_id, $offset = 0, $limit = 5)
    {   
        $results = array();
        if( !empty($categories_id) ){
            $stri_key = '';
            if(is_array($categories_id)){
                $stri_key = $this->createKeyCacheFromArray($categories_id);
            }else{
                $stri_key = $categories_id;
            }
            $key = md5($this->getNamspaceCached().':ProductsTable:getTagsProductInCategory('.$stri_key.','.$offset.','.$limit.')');
            $results = $this->view->Datas()->popIDataHashMap($key);
            if( !$results ) {
                $results = $this->getModelTable('ProductsTable')->getTagsProductInCategory($categories_id, $offset, $limit);
                $this->view->Datas()->pushIDataHashMap($key, $results);
            }
        }
        return $results;
    }

    public function getStringAvailability($product){
        $translator = $this->getTranslator();
        $price_sale = $this->getPriceSale($product);
        $price = $this->getPrice($product);
        $is_available = $this->getIsAvailable($product);//quản lý kho
        $quantity = $this->getQuantity($product);
        $is_goingon = $this->getIsGoingOn($product);
        $is_delete = $this->getIsDelete($product);
        $is_published = $this->getIsPublished($product);
        /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
        hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
        if( !(($is_available == 1 && $quantity <= 0) 
            || $is_delete == 1 || $is_published == 0 || $price_sale <= 0 || $is_goingon == 1) ){
            return $translator->translate('txt_availability_product');
        }
        return $translator->translate('txt_not_availability_product');
    }

    public function IsAvailablePrice($product){
        $translator = $this->getTranslator();
        $price_sale = $this->getPriceSale($product);
        $price = $this->getPrice($product);
        /*có quản lý kho và số lượng sản phẩm nhỏ hơn 0
        hoặc đã xóa hoặc chưa hiển thị hoặc giá bán nhỏ hơn 0 hoặc hàng sắp về*/
        if( $price > 0){
            return true;
        }
        return false;
    }

    public function stringWhenNotAvailablePrice(){
        $translator = $this->getTranslator();
        return '<span data-neo="priceZero" >'.$translator->translate('txt_price_zero').'</span>';
    }

    public function getAttrHtml( $p ){
        $attr = '';
        if( !empty($p) ){
            foreach ($p as $key => $att) {
                if( is_array($att)
                    || is_object($att) ){
                    $attr .= ' data-' .$key. '=\'' .json_encode($att). '\'';
                }else{
                    $attr .= ' data-' .$key. '=\'' .str_replace('\'', '"', trim($att)). '\'';
                }
            }
        }
        return $attr;
    }

    public function getLinkShareFacebook($p)
    {
        $link = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $link = $this->getQuickviewProductsUrl($p);
            }else{
                $link = $this->getProductsUrl($p);
            }
            $link = 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($link).'&t='.$this->getName($p);
        }
        return $link;
    }

    public function getLinkShareGoogle($p)
    {
        $link = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $link = $this->getQuickviewProductsUrl($p);
            }else{
                $link = $this->getProductsUrl($p);
            }
            $link = 'https://plus.google.com/share?url='.urlencode($link);
        }
        return $link;
    }

    public function getLinkShareTwitter($p)
    {
        $link = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $link = $this->getQuickviewProductsUrl($p);
            }else{
                $link = $this->getProductsUrl($p);
            }
            $link = 'https://twitter.com/share?url='.urlencode($link).'&text='.$this->getName($p);
        }
        return $link;
    }

    public function getLinkShareLinkedin($p)
    {
        $link = '';
        if(!empty($p)){
            if($p['type_view'] == 2){
                $link = $this->getQuickviewProductsUrl($p);
            }else{
                $link = $this->getProductsUrl($p);
            }
            $link = 'https://www.linkedin.com/shareArticle?mini=true&url='.urlencode($link).'&title='.$this->getName($p).'&source=';
        }
        return $link;
    }
	
}
