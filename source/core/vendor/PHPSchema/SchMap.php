<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchMap extends SchCreativeWork{
	protected $mapType	=	'MapCategoryType';
	function __construct(){$this->namespace = "Map";}
}