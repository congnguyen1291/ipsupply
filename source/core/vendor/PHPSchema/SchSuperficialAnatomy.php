<?php
namespace Schema;
use Schema\SchMedicalEntity;

class SchSuperficialAnatomy extends SchMedicalEntity{
	protected $associatedPathophysiology	=	'Text';
	protected $relatedAnatomy	=	'AnatomicalSystem,AnatomicalStructure';
	protected $relatedCondition	=	'MedicalCondition';
	protected $relatedTherapy	=	'MedicalTherapy';
	protected $significance	=	'Text';
	function __construct(){$this->namespace = "SuperficialAnatomy";}
}