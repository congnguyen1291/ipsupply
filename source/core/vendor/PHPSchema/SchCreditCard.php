<?php
namespace Schema;
use Schema\SchPaymentMethod;

class SchCreditCard extends SchPaymentMethod{
	function __construct(){$this->namespace = "CreditCard";}
}