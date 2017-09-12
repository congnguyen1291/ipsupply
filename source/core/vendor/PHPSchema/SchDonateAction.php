<?php
namespace Schema;
use Schema\SchTradeAction;

class SchDonateAction extends SchTradeAction{
	protected $recipient	=	'Person,Organization,Audience';
	function __construct(){$this->namespace = "DonateAction";}
}