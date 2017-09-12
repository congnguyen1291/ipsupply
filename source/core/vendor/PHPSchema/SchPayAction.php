<?php
namespace Schema;
use Schema\SchTradeAction;

class SchPayAction extends SchTradeAction{
	protected $purpose	=	'MedicalDevicePurpose,Thing';
	protected $recipient	=	'Person,Organization,Audience';
	function __construct(){$this->namespace = "PayAction";}
}