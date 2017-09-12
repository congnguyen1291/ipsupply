<?php
namespace Schema;
use Schema\SchPlaceOfWorship;

class SchChurch extends SchPlaceOfWorship{
	function __construct(){$this->namespace = "Church";}
}