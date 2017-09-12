<?php
namespace Schema;
use Schema\SchFinancialService;

class SchBankOrCreditUnion extends SchFinancialService{
	function __construct(){$this->namespace = "BankOrCreditUnion";}
}