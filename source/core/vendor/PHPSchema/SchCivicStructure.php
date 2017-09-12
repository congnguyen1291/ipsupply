<?php
namespace Schema;
use Schema\SchPlace;

class SchCivicStructure extends SchPlace{
	protected $openingHours	=	'Duration';
	function __construct(){$this->namespace = "CivicStructure";}
}