<?php
namespace Schema;
use Schema\SchInsertAction;

class SchAppendAction extends SchInsertAction{
	function __construct(){$this->namespace = "AppendAction";}
}