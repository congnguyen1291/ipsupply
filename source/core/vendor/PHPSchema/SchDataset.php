<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchDataset extends SchCreativeWork{
	protected $catalog	=	'DataCatalog';
	protected $distribution	=	'DataDownload';
	protected $spatial	=	'Place';
	protected $temporal	=	'DateTime';
	function __construct(){$this->namespace = "Dataset";}
}