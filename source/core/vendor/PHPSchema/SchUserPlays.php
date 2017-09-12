<?php
namespace Schema;
use Schema\SchUserInteraction;

class SchUserPlays extends SchUserInteraction{
	function __construct(){$this->namespace = "UserPlays";}
}