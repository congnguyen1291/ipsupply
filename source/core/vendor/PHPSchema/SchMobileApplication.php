<?php
namespace Schema;
use Schema\SchSoftwareApplication;

class SchMobileApplication extends SchSoftwareApplication{
	protected $carrierRequirements	=	'Text';
	function __construct(){$this->namespace = "MobileApplication";}
}