<?php
namespace Schema;
use Schema\SchInsertAction;

class SchPrependAction extends SchInsertAction{
	function __construct(){$this->namespace = "PrependAction";}
}