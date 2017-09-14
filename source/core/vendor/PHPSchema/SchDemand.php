<?php
namespace Schema;
use Schema\SchIntangible;

class SchDemand extends SchIntangible{
	protected $acceptedPaymentMethod	=	'PaymentMethod';
	protected $advanceBookingRequirement	=	'QuantitativeValue';
	protected $availability	=	'ItemAvailability';
	protected $availabilityEnds	=	'DateTime';
	protected $availabilityStarts	=	'DateTime';
	protected $availableAtOrFrom	=	'Place';
	protected $availableDeliveryMethod	=	'DeliveryMethod';
	protected $businessFunction	=	'BusinessFunction';
	protected $deliveryLeadTime	=	'QuantitativeValue';
	protected $eligibleCustomerType	=	'BusinessEntityType';
	protected $eligibleDuration	=	'QuantitativeValue';
	protected $eligibleQuantity	=	'QuantitativeValue';
	protected $eligibleRegion	=	'GeoShape,Text';
	protected $eligibleTransactionVolume	=	'PriceSpecification';
	protected $gtin13	=	'Text';
	protected $gtin14	=	'Text';
	protected $gtin8	=	'Text';
	protected $includesObject	=	'TypeAndQuantityNode';
	protected $inventoryLevel	=	'QuantitativeValue';
	protected $itemCondition	=	'OfferItemCondition';
	protected $itemOffered	=	'Product';
	protected $mpn	=	'Text';
	protected $priceSpecification	=	'PriceSpecification';
	protected $seller	=	'Person,Organization';
	protected $serialNumber	=	'Text';
	protected $sku	=	'Text';
	protected $validFrom	=	'DateTime';
	protected $validThrough	=	'DateTime';
	protected $warranty	=	'WarrantyPromise';
	function __construct(){$this->namespace = "Demand";}
}