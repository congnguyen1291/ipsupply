<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:45 AM
 */

namespace Cms\Controller;

use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\CategoryForm;
use Cms\Model\Category;
use Cms\Lib\Paging;
use Zend\Dom\Query;

class TagsController extends BackEndController
{

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'tags';
    }

    public function getTagsAction()
    {
        //findProducts
        $str = $_GET['query'];
        $tags = $this->getModelTable('TagsTable')->findTags($str);
        echo json_encode(array(
            'suggestions' => $tags
        ));
        die;
    }

}