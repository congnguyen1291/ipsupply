<?php
namespace Schema;
use Schema\SchPublicationEvent;

class SchBroadcastEvent extends SchPublicationEvent{
	function __construct(){$this->namespace = "BroadcastEvent";}
}