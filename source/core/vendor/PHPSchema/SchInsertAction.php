<?php
namespace Schema;
use Schema\SchAddAction;

class SchInsertAction extends SchAddAction{
	protected $toLocation	=	'Place,Number';
	function __construct(){$this->namespace = "InsertAction";}
}