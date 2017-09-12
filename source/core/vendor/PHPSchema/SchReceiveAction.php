<?php
namespace Schema;
use Schema\SchTransferAction;

class SchReceiveAction extends SchTransferAction{
	protected $deliveryMethod	=	'DeliveryMethod';
	protected $sender	=	'Audience,Person,Organization';
	function __construct(){$this->namespace = "ReceiveAction";}
}