<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:45 AM
 */

namespace Cms\Controller;

use Cms\Form\ProductForm;
use Cms\Model\Product;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\CategoryForm;
use Cms\Model\Category;
use Cms\Lib\Paging;
use Zend\Dom\Query;
use Zend\Validator;

use JasonGrimes\Paginator;

class StockController extends BackEndController
{

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'stock';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['is_available'] = 1;

        if( !empty($q) ){
            if( $type == 0 ){
                $params['products_title'] = $q;
            }else if( $type == 1 ){
                $params['products_code'] = $q;
            }
            else if( $type == 2 ){
                $params['price'] = $q;
            }
            else if( $type == 3 ){
                $params['quantity'] = $q;
            }
        }

        $category = array();
        if( !empty($id) ){
            $params['categories_id'] = $id;
            $category = $this->getModelTable('CategoryTable')->getCategoryLanguage($id, $language);
        }
        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $total = $this->getModelTable('ProductTable')->countAll($params);
        $products = $this->getModelTable('ProductTable')->fetchAll($params);

        $link = '/cms/stock' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['category'] = $category;
        $this->data_view['products'] = $products;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        $this->data_view['q'] = $q;
		$this->data_view['type'] = $type;
        return $this->data_view;
    }
}