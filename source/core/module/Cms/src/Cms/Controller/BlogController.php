<?php
namespace Cms\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Cms\Form\BlogForm;
use Zend\View\Model\ViewModel;
use Cms\Model\Blog;

class BlogController extends BackEndController{

    protected $blogTable;
    protected $categoryTable;

    public function __construct(){
        parent::__construct();
    }

    public function indexAction(){
        return new ViewModel(array(
            'blogs' => $this->getBlogTable()->fetchAll()
        ));
    }

    public function addAction(){
        $form = new BlogForm();
        $form->get('submit')->setValue('Add');
        $form->get('catid')->setOptions(array(
            'options' => $this->getCategorySelect()
            ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $blog = new Blog();
            $form->setInputFilter($blog->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $blog->exchangeArray($form->getData());
                $this->getBlogTable()->saveBlog($blog);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/blog');
            }
        }
        return array('form' => $form);
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/blog', array(
                'action' => 'add'
            ));
        }
        try {
            $blog = $this->getBlogTable()->getBlog($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/blog', array(
                'action' => 'index'
            ));
        }
        $form = new BlogForm();
        $form->get('catid')->setOptions(array(
            'options' => $this->getCategorySelect()
        ));
        $form->bind($blog);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($blog->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getBlogTable()->saveBlog($blog);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/blog');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction(){

    }

    public function getBlogTable()
    {
        if (!$this->blogTable) {
            $sm = $this->getServiceLocator();
            $this->blogTable = $sm->get('Cms\Model\BlogTable');
        }
        return $this->blogTable;
    }

    public function getCategoryTable(){
        if (!$this->categoryTable) {
            $sm = $this->getServiceLocator();
            $this->categoryTable = $sm->get('Cms\Model\CategoryTable');
        }
        return $this->categoryTable;
    }

    public function getCategorySelect(){
        $result = array();
        $cats = $this->getCategoryTable()->fetchAll();
        foreach($cats as $cat) {
            $result[$cat->id] = $cat->name;
        }
        return $result;
    }

}