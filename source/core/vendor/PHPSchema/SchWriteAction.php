<?php
namespace Schema;
use Schema\SchCreateAction;

class SchWriteAction extends SchCreateAction{
	protected $language	=	'Language';
	function __construct(){$this->namespace = "WriteAction";}
}