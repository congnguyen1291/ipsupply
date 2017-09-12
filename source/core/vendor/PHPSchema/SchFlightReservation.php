<?php
namespace Schema;
use Schema\SchReservation;

class SchFlightReservation extends SchReservation{
	protected $boardingGroup	=	'Text';
	function __construct(){$this->namespace = "FlightReservation";}
}