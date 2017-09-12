<?php
namespace Schema;
use Schema\SchEnumeration;

class SchPaymentMethod extends SchEnumeration{
	function __construct(){$this->namespace = "PaymentMethod";}
}