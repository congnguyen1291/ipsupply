<?php
namespace Schema;
use Schema\SchFindAction;

class SchTrackAction extends SchFindAction{
	protected $deliveryMethod	=	'DeliveryMethod';
	function __construct(){$this->namespace = "TrackAction";}
}