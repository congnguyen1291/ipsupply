<?php
namespace Schema;
use Schema\SchTradeAction;

class SchTipAction extends SchTradeAction{
	protected $recipient	=	'Person,Organization,Audience';
	function __construct(){$this->namespace = "TipAction";}
}