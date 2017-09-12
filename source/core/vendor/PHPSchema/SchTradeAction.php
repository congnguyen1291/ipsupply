<?php
namespace Schema;
use Schema\SchAction;

class SchTradeAction extends SchAction{
	protected $price	=	'Number,Text';
	function __construct(){$this->namespace = "TradeAction";}
}