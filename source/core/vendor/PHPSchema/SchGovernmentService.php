<?php
namespace Schema;
use Schema\SchService;

class SchGovernmentService extends SchService{
	protected $serviceOperator	=	'Organization';
	function __construct(){$this->namespace = "GovernmentService";}
}