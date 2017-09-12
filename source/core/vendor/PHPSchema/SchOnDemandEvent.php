<?php
namespace Schema;
use Schema\SchPublicationEvent;

class SchOnDemandEvent extends SchPublicationEvent{
	function __construct(){$this->namespace = "OnDemandEvent";}
}