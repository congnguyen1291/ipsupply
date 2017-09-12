<?php
namespace Schema;
use Schema\SchSoftwareApplication;

class SchWebApplication extends SchSoftwareApplication{
	protected $browserRequirements	=	'Text';
	function __construct(){$this->namespace = "WebApplication";}
}