<?php
namespace Schema;
use Schema\SchIntangible;

class SchBrand extends SchIntangible{
	protected $logo	=	'ImageObject,URL';
	function __construct(){$this->namespace = "Brand";}
}