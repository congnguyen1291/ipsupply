<?php
namespace Schema;
use Schema\SchArticle;

class SchTechArticle extends SchArticle{
	protected $dependencies	=	'Text';
	protected $proficiencyLevel	=	'Text';
	function __construct(){$this->namespace = "TechArticle";}
}