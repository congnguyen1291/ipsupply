<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchDataCatalog extends SchCreativeWork{
	protected $dataset	=	'Dataset';
	function __construct(){$this->namespace = "DataCatalog";}
}