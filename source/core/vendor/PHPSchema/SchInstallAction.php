<?php
namespace Schema;
use Schema\SchConsumeAction;

class SchInstallAction extends SchConsumeAction{
	function __construct(){$this->namespace = "InstallAction";}
}