<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Params  extends App
{
    public function fromQuery($param = null, $default = null)
    {
        $app = $this->getApp();
        if ($param === null)
        {
            return $app->getRequest()->getQuery($param, $default)->toArray();
        }

        return $app->getRequest()->getQuery($param, $default);
    }

    public function fromPost($param = null, $default = null)
    {
        $app = $this->getApp();
        if ($param === null)
        {
            return $app->getRequest()->getPost($param, $default)->toArray();
        }

        return $app->getRequest()->getPost($param, $default);
    }

    public function fromRoute($param = null, $default = null)
    {
        $app = $this->getApp();
        if ($param === null)
        {
            return $app->getMvcEvent()->getRouteMatch()->getParams();
        }

        return $app->getMvcEvent()->getRouteMatch()->getParam($param, $default);
    }

    public function getDataView_() {
        $children = $this->view->viewModel()->getCurrent()->getChildren();
        return $children;
    }

}
