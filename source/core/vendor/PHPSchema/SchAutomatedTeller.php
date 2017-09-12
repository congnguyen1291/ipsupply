<?php
namespace Schema;
use Schema\SchFinancialService;

class SchAutomatedTeller extends SchFinancialService{
	function __construct(){$this->namespace = "AutomatedTeller";}
}