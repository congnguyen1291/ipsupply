<?php
namespace Schema;
use Schema\SchThing;

class SchProduct extends SchThing{
	protected $aggregateRating	=	'AggregateRating';
	protected $audience	=	'Audience';
	protected $brand	=	'Organization,Brand';
	protected $color	=	'Text';
	protected $depth	=	'QuantitativeValue,Distance';
	protected $gtin13	=	'Text';
	protected $gtin14	=	'Text';
	protected $gtin8	=	'Text';
	protected $height	=	'QuantitativeValue,Distance';
	protected $isAccessoryOrSparePartFor	=	'Product';
	protected $isConsumableFor	=	'Product';
	protected $isRelatedTo	=	'Product';
	protected $isSimilarTo	=	'Product';
	protected $itemCondition	=	'OfferItemCondition';
	protected $logo	=	'URL,ImageObject';
	protected $manufacturer	=	'Organization';
	protected $model	=	'ProductModel,Text';
	protected $mpn	=	'Text';
	protected $offers	=	'Offer';
	protected $productID	=	'Text';
	protected $releaseDate	=	'Date';
	protected $review	=	'Review';
	protected $sku	=	'Text';
	protected $weight	=	'QuantitativeValue';
	protected $width	=	'QuantitativeValue,Distance';
	function __construct(){$this->namespace = "Product";}
}