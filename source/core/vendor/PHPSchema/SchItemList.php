<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchItemList extends SchCreativeWork{
	protected $itemListElement	=	'Text';
	protected $itemListOrder	=	'Text';
	function __construct(){$this->namespace = "ItemList";}
}