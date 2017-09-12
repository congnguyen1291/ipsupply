<?php
namespace Schema;
use Schema\SchReservation;

class SchReservationPackage extends SchReservation{
	protected $subReservation	=	'Reservation';
	function __construct(){$this->namespace = "ReservationPackage";}
}