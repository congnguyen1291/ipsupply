<?php
namespace Schema;
use Schema\SchAction;

class SchUpdateAction extends SchAction{
	protected $collection	=	'Thing';
	function __construct(){$this->namespace = "UpdateAction";}
}