<?php
namespace Schema;
use Schema\SchTransferAction;

class SchBorrowAction extends SchTransferAction{
	protected $lender	=	'Person';
	function __construct(){$this->namespace = "BorrowAction";}
}