<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Keywords extends App
{
	public function getTopKeywords()
    {
        $keywords = $this->getModelTable('KeywordsTable')->getTopKeywords();
        return $keywords;
    }
}
