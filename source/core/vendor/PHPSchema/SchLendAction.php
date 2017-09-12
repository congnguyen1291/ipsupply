<?php
namespace Schema;
use Schema\SchTransferAction;

class SchLendAction extends SchTransferAction{
	protected $borrower	=	'Person';
	function __construct(){$this->namespace = "LendAction";}
}