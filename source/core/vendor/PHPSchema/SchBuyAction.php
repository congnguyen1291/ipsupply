<?php
namespace Schema;
use Schema\SchTradeAction;

class SchBuyAction extends SchTradeAction{
	protected $seller	=	'Person,Organization';
	protected $warrantyPromise	=	'WarrantyPromise';
	function __construct(){$this->namespace = "BuyAction";}
}