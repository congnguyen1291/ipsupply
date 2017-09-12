<?php
namespace Schema;
use Schema\SchCreativeWork;

class SchWebPageElement extends SchCreativeWork{
	protected $mainContentOfPage	=	'MainContentOfPage';
	function __construct(){$this->namespace = "WebPageElement";}
}