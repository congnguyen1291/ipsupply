<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
//use Zend\I18n\Translator\Translator;
class DataFactory extends AbstractHelper
{
	protected $sm;

    public function __invoke()
    {
        return $this;
		
    }

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    protected function getModelTable($name) {
        if (! isset ( $this->{$name} )) {
            $this->{$name} = NULL;
        }
        if (! $this->{$name}) {
            $this->{$name} = $this->sm->get ( 'Application\Model\\' . $name );
        }
        return $this->{$name};
    }
	
	public function getHotProduct($model){
		$results = array();
		try{
			$results = $model->getHotProduct();
		}catch(\Exception $ex){
			return $ex->getMessage();
		}
		return $results;
	}

}

