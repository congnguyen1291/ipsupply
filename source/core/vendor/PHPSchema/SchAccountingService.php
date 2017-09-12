<?php

namespace Schema;
use Schema\SchFinancialService;

class SchAccountingService extends SchFinancialService{
	function __construct(){$this->namespace = "AccountingService";}
}