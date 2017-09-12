<?php
namespace Schema;
use Schema\SchUserInteraction;

class SchUserTweets extends SchUserInteraction{
	function __construct(){$this->namespace = "UserTweets";}
}