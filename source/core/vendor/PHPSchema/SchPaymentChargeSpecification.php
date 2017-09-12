<?php
namespace Schema;
use Schema\SchPriceSpecification;

class SchPaymentChargeSpecification extends SchPriceSpecification{
	protected $appliesToDeliveryMethod	=	'DeliveryMethod';
	protected $appliesToPaymentMethod	=	'PaymentMethod';
	function __construct(){$this->namespace = "PaymentChargeSpecification";}
}