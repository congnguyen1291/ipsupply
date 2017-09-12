<?php

/**
 * Project : phpschema
 * User: thuytien
 * Date: 10/23/2014
 * Time: 3:07 PM
 */

namespace Schema;

class Schema
{
    public function __construct() {
        return $this;
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function APIReference($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAPIReference::getInstance()->prop($prop, $val);
        } else {
            return SchAPIReference::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AboutPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAboutPage::getInstance()->prop($prop, $val);
        } else {
            return SchAboutPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AcceptAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAcceptAction::getInstance()->prop($prop, $val);
        } else {
            return SchAcceptAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AccountingService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAccountingService::getInstance()->prop($prop, $val);
        } else {
            return SchAccountingService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AchieveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAchieveAction::getInstance()->prop($prop, $val);
        } else {
            return SchAchieveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Action($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAction::getInstance()->prop($prop, $val);
        } else {
            return SchAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AddAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAddAction::getInstance()->prop($prop, $val);
        } else {
            return SchAddAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AdministrativeArea($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAdministrativeArea::getInstance()->prop($prop, $val);
        } else {
            return SchAdministrativeArea::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AdultEntertainment($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAdultEntertainment::getInstance()->prop($prop, $val);
        } else {
            return SchAdultEntertainment::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AggregateOffer($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAggregateOffer::getInstance()->prop($prop, $val);
        } else {
            return SchAggregateOffer::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AggregateRating($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAggregateRating::getInstance()->prop($prop, $val);
        } else {
            return SchAggregateRating::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AgreeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAgreeAction::getInstance()->prop($prop, $val);
        } else {
            return SchAgreeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Airline($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAirline::getInstance()->prop($prop, $val);
        } else {
            return SchAirline::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Airport($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAirport::getInstance()->prop($prop, $val);
        } else {
            return SchAirport::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AlignmentObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAlignmentObject::getInstance()->prop($prop, $val);
        } else {
            return SchAlignmentObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AllocateAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAllocateAction::getInstance()->prop($prop, $val);
        } else {
            return SchAllocateAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AmusementPark($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAmusementPark::getInstance()->prop($prop, $val);
        } else {
            return SchAmusementPark::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AnatomicalStructure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAnatomicalStructure::getInstance()->prop($prop, $val);
        } else {
            return SchAnatomicalStructure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AnatomicalSystem($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAnatomicalSystem::getInstance()->prop($prop, $val);
        } else {
            return SchAnatomicalSystem::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AnimalShelter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAnimalShelter::getInstance()->prop($prop, $val);
        } else {
            return SchAnimalShelter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Answer($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAnswer::getInstance()->prop($prop, $val);
        } else {
            return SchAnswer::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ApartmentComplex($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchApartmentComplex::getInstance()->prop($prop, $val);
        } else {
            return SchApartmentComplex::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AppendAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAppendAction::getInstance()->prop($prop, $val);
        } else {
            return SchAppendAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ApplyAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchApplyAction::getInstance()->prop($prop, $val);
        } else {
            return SchApplyAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ApprovedIndication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchApprovedIndication::getInstance()->prop($prop, $val);
        } else {
            return SchApprovedIndication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Aquarium($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAquarium::getInstance()->prop($prop, $val);
        } else {
            return SchAquarium::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ArriveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchArriveAction::getInstance()->prop($prop, $val);
        } else {
            return SchArriveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ArtGallery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchArtGallery::getInstance()->prop($prop, $val);
        } else {
            return SchArtGallery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Artery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchArtery::getInstance()->prop($prop, $val);
        } else {
            return SchArtery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Article($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchArticle::getInstance()->prop($prop, $val);
        } else {
            return SchArticle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BlogPosting($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBlogPosting::getInstance()->prop($prop, $val);
        } else {
            return SchBlogPosting::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AskAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAskAction::getInstance()->prop($prop, $val);
        } else {
            return SchAskAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AssessAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAssessAction::getInstance()->prop($prop, $val);
        } else {
            return SchAssessAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AssignAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAssignAction::getInstance()->prop($prop, $val);
        } else {
            return SchAssignAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Attorney($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAttorney::getInstance()->prop($prop, $val);
        } else {
            return SchAttorney::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Audience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAudience::getInstance()->prop($prop, $val);
        } else {
            return SchAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AudioObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAudioObject::getInstance()->prop($prop, $val);
        } else {
            return SchAudioObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AuthorizeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAuthorizeAction::getInstance()->prop($prop, $val);
        } else {
            return SchAuthorizeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoBodyShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoBodyShop::getInstance()->prop($prop, $val);
        } else {
            return SchAutoBodyShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoDealer($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoDealer::getInstance()->prop($prop, $val);
        } else {
            return SchAutoDealer::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoPartsStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoPartsStore::getInstance()->prop($prop, $val);
        } else {
            return SchAutoPartsStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoRental($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoRental::getInstance()->prop($prop, $val);
        } else {
            return SchAutoRental::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoRepair($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoRepair::getInstance()->prop($prop, $val);
        } else {
            return SchAutoRepair::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutoWash($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutoWash::getInstance()->prop($prop, $val);
        } else {
            return SchAutoWash::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutomatedTeller($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutomatedTeller::getInstance()->prop($prop, $val);
        } else {
            return SchAutomatedTeller::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function AutomotiveBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchAutomotiveBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchAutomotiveBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Bakery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBakery::getInstance()->prop($prop, $val);
        } else {
            return SchBakery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BankOrCreditUnion($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBankOrCreditUnion::getInstance()->prop($prop, $val);
        } else {
            return SchBankOrCreditUnion::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BarOrPub($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBarOrPub::getInstance()->prop($prop, $val);
        } else {
            return SchBarOrPub::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Base($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBase::getInstance()->prop($prop, $val);
        } else {
            return SchBase::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BaseInterface($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBaseInterface::getInstance()->prop($prop, $val);
        } else {
            return SchBaseInterface::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Beach($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBeach::getInstance()->prop($prop, $val);
        } else {
            return SchBeach::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BeautySalon($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBeautySalon::getInstance()->prop($prop, $val);
        } else {
            return SchBeautySalon::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BedAndBreakfast($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBedAndBreakfast::getInstance()->prop($prop, $val);
        } else {
            return SchBedAndBreakfast::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BefriendAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBefriendAction::getInstance()->prop($prop, $val);
        } else {
            return SchBefriendAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BikeStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBikeStore::getInstance()->prop($prop, $val);
        } else {
            return SchBikeStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Blog($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBlog::getInstance()->prop($prop, $val);
        } else {
            return SchBlog::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BloodTest($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBloodTest::getInstance()->prop($prop, $val);
        } else {
            return SchBloodTest::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BodyOfWater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBodyOfWater::getInstance()->prop($prop, $val);
        } else {
            return SchBodyOfWater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Bone($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBone::getInstance()->prop($prop, $val);
        } else {
            return SchBone::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Book($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBook::getInstance()->prop($prop, $val);
        } else {
            return SchBook::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BookFormatType($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBookFormatType::getInstance()->prop($prop, $val);
        } else {
            return SchBookFormatType::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BookStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBookStore::getInstance()->prop($prop, $val);
        } else {
            return SchBookStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BookmarkAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBookmarkAction::getInstance()->prop($prop, $val);
        } else {
            return SchBookmarkAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BorrowAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBorrowAction::getInstance()->prop($prop, $val);
        } else {
            return SchBorrowAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BowlingAlley($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBowlingAlley::getInstance()->prop($prop, $val);
        } else {
            return SchBowlingAlley::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BrainStructure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBrainStructure::getInstance()->prop($prop, $val);
        } else {
            return SchBrainStructure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Brand($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBrand::getInstance()->prop($prop, $val);
        } else {
            return SchBrand::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Brewery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBrewery::getInstance()->prop($prop, $val);
        } else {
            return SchBrewery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BroadcastEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBroadcastEvent::getInstance()->prop($prop, $val);
        } else {
            return SchBroadcastEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BroadcastService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBroadcastService::getInstance()->prop($prop, $val);
        } else {
            return SchBroadcastService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BuddhistTemple($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBuddhistTemple::getInstance()->prop($prop, $val);
        } else {
            return SchBuddhistTemple::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusReservation::getInstance()->prop($prop, $val);
        } else {
            return SchBusReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusStation::getInstance()->prop($prop, $val);
        } else {
            return SchBusStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusStop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusStop::getInstance()->prop($prop, $val);
        } else {
            return SchBusStop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusTrip($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusTrip::getInstance()->prop($prop, $val);
        } else {
            return SchBusTrip::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusinessAudience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusinessAudience::getInstance()->prop($prop, $val);
        } else {
            return SchBusinessAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusinessEntityType($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusinessEntityType::getInstance()->prop($prop, $val);
        } else {
            return SchBusinessEntityType::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusinessEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusinessEvent::getInstance()->prop($prop, $val);
        } else {
            return SchBusinessEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BusinessFunction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBusinessFunction::getInstance()->prop($prop, $val);
        } else {
            return SchBusinessFunction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function BuyAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchBuyAction::getInstance()->prop($prop, $val);
        } else {
            return SchBuyAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CafeOrCoffeeShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCafeOrCoffeeShop::getInstance()->prop($prop, $val);
        } else {
            return SchCafeOrCoffeeShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Campground($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCampground::getInstance()->prop($prop, $val);
        } else {
            return SchCampground::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Canal($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCanal::getInstance()->prop($prop, $val);
        } else {
            return SchCanal::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CancelAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCancelAction::getInstance()->prop($prop, $val);
        } else {
            return SchCancelAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Car($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCar::getInstance()->prop($prop, $val);
        } else {
            return SchCar::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Casino($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCasino::getInstance()->prop($prop, $val);
        } else {
            return SchCasino::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CatholicChurch($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCatholicChurch::getInstance()->prop($prop, $val);
        } else {
            return SchCatholicChurch::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Cemetery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCemetery::getInstance()->prop($prop, $val);
        } else {
            return SchCemetery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CheckAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCheckAction::getInstance()->prop($prop, $val);
        } else {
            return SchCheckAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CheckInAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCheckInAction::getInstance()->prop($prop, $val);
        } else {
            return SchCheckInAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CheckOutAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCheckOutAction::getInstance()->prop($prop, $val);
        } else {
            return SchCheckOutAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CheckoutPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCheckoutPage::getInstance()->prop($prop, $val);
        } else {
            return SchCheckoutPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ChildCare($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchChildCare::getInstance()->prop($prop, $val);
        } else {
            return SchChildCare::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ChildrensEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchChildrensEvent::getInstance()->prop($prop, $val);
        } else {
            return SchChildrensEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ChooseAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchChooseAction::getInstance()->prop($prop, $val);
        } else {
            return SchChooseAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Church($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchChurch::getInstance()->prop($prop, $val);
        } else {
            return SchChurch::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function City($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCity::getInstance()->prop($prop, $val);
        } else {
            return SchCity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CityHall($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCityHall::getInstance()->prop($prop, $val);
        } else {
            return SchCityHall::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CivicStructure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCivicStructure::getInstance()->prop($prop, $val);
        } else {
            return SchCivicStructure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    static function Clip($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchClip::getInstance()->prop($prop, $val);
        } else {
            return SchClip::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ClothingStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchClothingStore::getInstance()->prop($prop, $val);
        } else {
            return SchClothingStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Code($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCode::getInstance()->prop($prop, $val);
        } else {
            return SchCode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CollectionPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCollectionPage::getInstance()->prop($prop, $val);
        } else {
            return SchCollectionPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CollegeOrUniversity($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCollegeOrUniversity::getInstance()->prop($prop, $val);
        } else {
            return SchCollegeOrUniversity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ComedyClub($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchComedyClub::getInstance()->prop($prop, $val);
        } else {
            return SchComedyClub::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ComedyEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchComedyEvent::getInstance()->prop($prop, $val);
        } else {
            return SchComedyEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Comment($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchComment::getInstance()->prop($prop, $val);
        } else {
            return SchComment::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CommentAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCommentAction::getInstance()->prop($prop, $val);
        } else {
            return SchCommentAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CommunicateAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCommunicateAction::getInstance()->prop($prop, $val);
        } else {
            return SchCommunicateAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ComputerStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchComputerStore::getInstance()->prop($prop, $val);
        } else {
            return SchComputerStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ConfirmAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchConfirmAction::getInstance()->prop($prop, $val);
        } else {
            return SchConfirmAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ConsumeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchConsumeAction::getInstance()->prop($prop, $val);
        } else {
            return SchConsumeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ContactPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchContactPage::getInstance()->prop($prop, $val);
        } else {
            return SchContactPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ContactPoint($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchContactPoint::getInstance()->prop($prop, $val);
        } else {
            return SchContactPoint::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ContactPointOption($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchContactPointOption::getInstance()->prop($prop, $val);
        } else {
            return SchContactPointOption::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Continent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchContinent::getInstance()->prop($prop, $val);
        } else {
            return SchContinent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ConvenienceStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchConvenienceStore::getInstance()->prop($prop, $val);
        } else {
            return SchConvenienceStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CookAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCookAction::getInstance()->prop($prop, $val);
        } else {
            return SchCookAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Corporation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCorporation::getInstance()->prop($prop, $val);
        } else {
            return SchCorporation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Country($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCountry::getInstance()->prop($prop, $val);
        } else {
            return SchCountry::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Courthouse($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCourthouse::getInstance()->prop($prop, $val);
        } else {
            return SchCourthouse::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CreateAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCreateAction::getInstance()->prop($prop, $val);
        } else {
            return SchCreateAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CreativeWork($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCreativeWork::getInstance()->prop($prop, $val);
        } else {
            return SchCreativeWork::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function CreditCard($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCreditCard::getInstance()->prop($prop, $val);
        } else {
            return SchCreditCard::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Crematorium($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchCrematorium::getInstance()->prop($prop, $val);
        } else {
            return SchCrematorium::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DDxElement($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDDxElement::getInstance()->prop($prop, $val);
        } else {
            return SchDDxElement::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DanceEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDanceEvent::getInstance()->prop($prop, $val);
        } else {
            return SchDanceEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DanceGroup($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDanceGroup::getInstance()->prop($prop, $val);
        } else {
            return SchDanceGroup::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DataCatalog($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDataCatalog::getInstance()->prop($prop, $val);
        } else {
            return SchDataCatalog::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Dataset($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDataset::getInstance()->prop($prop, $val);
        } else {
            return SchDataset::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DayOfWeek($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDayOfWeek::getInstance()->prop($prop, $val);
        } else {
            return SchDayOfWeek::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DaySpa($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDaySpa::getInstance()->prop($prop, $val);
        } else {
            return SchDaySpa::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DefenceEstablishment($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDefenceEstablishment::getInstance()->prop($prop, $val);
        } else {
            return SchDefenceEstablishment::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DeleteAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDeleteAction::getInstance()->prop($prop, $val);
        } else {
            return SchDeleteAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DeliveryChargeSpecification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDeliveryChargeSpecification::getInstance()->prop($prop, $val);
        } else {
            return SchDeliveryChargeSpecification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DeliveryEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDeliveryEvent::getInstance()->prop($prop, $val);
        } else {
            return SchDeliveryEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DeliveryMethod($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDeliveryMethod::getInstance()->prop($prop, $val);
        } else {
            return SchDeliveryMethod::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Demand($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDemand::getInstance()->prop($prop, $val);
        } else {
            return SchDemand::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Dentist($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDentist::getInstance()->prop($prop, $val);
        } else {
            return SchDentist::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DepartAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDepartAction::getInstance()->prop($prop, $val);
        } else {
            return SchDepartAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DepartmentStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDepartmentStore::getInstance()->prop($prop, $val);
        } else {
            return SchDepartmentStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DiagnosticLab($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDiagnosticLab::getInstance()->prop($prop, $val);
        } else {
            return SchDiagnosticLab::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DiagnosticProcedure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDiagnosticProcedure::getInstance()->prop($prop, $val);
        } else {
            return SchDiagnosticProcedure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Diet($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDiet::getInstance()->prop($prop, $val);
        } else {
            return SchDiet::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DietarySupplement($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDietarySupplement::getInstance()->prop($prop, $val);
        } else {
            return SchDietarySupplement::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DisagreeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDisagreeAction::getInstance()->prop($prop, $val);
        } else {
            return SchDisagreeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DiscoverAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDiscoverAction::getInstance()->prop($prop, $val);
        } else {
            return SchDiscoverAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DislikeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDislikeAction::getInstance()->prop($prop, $val);
        } else {
            return SchDislikeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Distance($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDistance::getInstance()->prop($prop, $val);
        } else {
            return SchDistance::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DonateAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDonateAction::getInstance()->prop($prop, $val);
        } else {
            return SchDonateAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Doseedule($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDoseedule::getInstance()->prop($prop, $val);
        } else {
            return SchDoseedule::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DownloadAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDownloadAction::getInstance()->prop($prop, $val);
        } else {
            return SchDownloadAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrawAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrawAction::getInstance()->prop($prop, $val);
        } else {
            return SchDrawAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrinkAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrinkAction::getInstance()->prop($prop, $val);
        } else {
            return SchDrinkAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Drug($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrug::getInstance()->prop($prop, $val);
        } else {
            return SchDrug::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugClass($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugClass::getInstance()->prop($prop, $val);
        } else {
            return SchDrugClass::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugCost($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugCost::getInstance()->prop($prop, $val);
        } else {
            return SchDrugCost::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugCostCategory($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugCostCategory::getInstance()->prop($prop, $val);
        } else {
            return SchDrugCostCategory::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugLegalStatus($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugLegalStatus::getInstance()->prop($prop, $val);
        } else {
            return SchDrugLegalStatus::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugPregnancyCategory($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugPregnancyCategory::getInstance()->prop($prop, $val);
        } else {
            return SchDrugPregnancyCategory::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugPrescriptionStatus($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugPrescriptionStatus::getInstance()->prop($prop, $val);
        } else {
            return SchDrugPrescriptionStatus::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DrugStrength($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDrugStrength::getInstance()->prop($prop, $val);
        } else {
            return SchDrugStrength::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function DryCleaningOrLaundry($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDryCleaningOrLaundry::getInstance()->prop($prop, $val);
        } else {
            return SchDryCleaningOrLaundry::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Duration($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchDuration::getInstance()->prop($prop, $val);
        } else {
            return SchDuration::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EatAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEatAction::getInstance()->prop($prop, $val);
        } else {
            return SchEatAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EducationEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEducationEvent::getInstance()->prop($prop, $val);
        } else {
            return SchEducationEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EducationalAudience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEducationalAudience::getInstance()->prop($prop, $val);
        } else {
            return SchEducationalAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EducationalOrganization($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEducationalOrganization::getInstance()->prop($prop, $val);
        } else {
            return SchEducationalOrganization::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Electrician($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchElectrician::getInstance()->prop($prop, $val);
        } else {
            return SchElectrician::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ElectronicsStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchElectronicsStore::getInstance()->prop($prop, $val);
        } else {
            return SchElectronicsStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Elementaryool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchElementaryool::getInstance()->prop($prop, $val);
        } else {
            return SchElementaryool::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EmailMessage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEmailMessage::getInstance()->prop($prop, $val);
        } else {
            return SchEmailMessage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Embassy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEmbassy::getInstance()->prop($prop, $val);
        } else {
            return SchEmbassy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EmergencyService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEmergencyService::getInstance()->prop($prop, $val);
        } else {
            return SchEmergencyService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EmploymentAgency($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEmploymentAgency::getInstance()->prop($prop, $val);
        } else {
            return SchEmploymentAgency::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EndorseAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEndorseAction::getInstance()->prop($prop, $val);
        } else {
            return SchEndorseAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Energy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEnergy::getInstance()->prop($prop, $val);
        } else {
            return SchEnergy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EntertainmentBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEntertainmentBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchEntertainmentBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Enumeration($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEnumeration::getInstance()->prop($prop, $val);
        } else {
            return SchEnumeration::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Episode($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEpisode::getInstance()->prop($prop, $val);
        } else {
            return SchEpisode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Event($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEvent::getInstance()->prop($prop, $val);
        } else {
            return SchEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EventReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEventReservation::getInstance()->prop($prop, $val);
        } else {
            return SchEventReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EventStatusType($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEventStatusType::getInstance()->prop($prop, $val);
        } else {
            return SchEventStatusType::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function EventVenue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchEventVenue::getInstance()->prop($prop, $val);
        } else {
            return SchEventVenue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ExerciseAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchExerciseAction::getInstance()->prop($prop, $val);
        } else {
            return SchExerciseAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ExerciseGym($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchExerciseGym::getInstance()->prop($prop, $val);
        } else {
            return SchExerciseGym::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ExercisePlan($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchExercisePlan::getInstance()->prop($prop, $val);
        } else {
            return SchExercisePlan::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FastFoodRestaurant($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFastFoodRestaurant::getInstance()->prop($prop, $val);
        } else {
            return SchFastFoodRestaurant::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Festival($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFestival::getInstance()->prop($prop, $val);
        } else {
            return SchFestival::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FilmAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFilmAction::getInstance()->prop($prop, $val);
        } else {
            return SchFilmAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FinancialService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFinancialService::getInstance()->prop($prop, $val);
        } else {
            return SchFinancialService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FindAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFindAction::getInstance()->prop($prop, $val);
        } else {
            return SchFindAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FireStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFireStation::getInstance()->prop($prop, $val);
        } else {
            return SchFireStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Flight($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFlight::getInstance()->prop($prop, $val);
        } else {
            return SchFlight::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FlightReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFlightReservation::getInstance()->prop($prop, $val);
        } else {
            return SchFlightReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Florist($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFlorist::getInstance()->prop($prop, $val);
        } else {
            return SchFlorist::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FollowAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFollowAction::getInstance()->prop($prop, $val);
        } else {
            return SchFollowAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FoodEstablishment($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFoodEstablishment::getInstance()->prop($prop, $val);
        } else {
            return SchFoodEstablishment::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FoodEstablishmentReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFoodEstablishmentReservation::getInstance()->prop($prop, $val);
        } else {
            return SchFoodEstablishmentReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FoodEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFoodEvent::getInstance()->prop($prop, $val);
        } else {
            return SchFoodEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function FurnitureStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchFurnitureStore::getInstance()->prop($prop, $val);
        } else {
            return SchFurnitureStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GardenStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGardenStore::getInstance()->prop($prop, $val);
        } else {
            return SchGardenStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GasStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGasStation::getInstance()->prop($prop, $val);
        } else {
            return SchGasStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GatedResidenceCommunity($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGatedResidenceCommunity::getInstance()->prop($prop, $val);
        } else {
            return SchGatedResidenceCommunity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GeneralContractor($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGeneralContractor::getInstance()->prop($prop, $val);
        } else {
            return SchGeneralContractor::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GeoCoordinates($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGeoCoordinates::getInstance()->prop($prop, $val);
        } else {
            return SchGeoCoordinates::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GeoShape($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGeoShape::getInstance()->prop($prop, $val);
        } else {
            return SchGeoShape::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GiveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGiveAction::getInstance()->prop($prop, $val);
        } else {
            return SchGiveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GolfCourse($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGolfCourse::getInstance()->prop($prop, $val);
        } else {
            return SchGolfCourse::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GovernmentBuilding($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGovernmentBuilding::getInstance()->prop($prop, $val);
        } else {
            return SchGovernmentBuilding::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GovernmentOffice($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGovernmentOffice::getInstance()->prop($prop, $val);
        } else {
            return SchGovernmentOffice::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GovernmentOrganization($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGovernmentOrganization::getInstance()->prop($prop, $val);
        } else {
            return SchGovernmentOrganization::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GovernmentPermit($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGovernmentPermit::getInstance()->prop($prop, $val);
        } else {
            return SchGovernmentPermit::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GovernmentService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGovernmentService::getInstance()->prop($prop, $val);
        } else {
            return SchGovernmentService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function GroceryStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchGroceryStore::getInstance()->prop($prop, $val);
        } else {
            return SchGroceryStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HVACBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHVACBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchHVACBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HairSalon($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHairSalon::getInstance()->prop($prop, $val);
        } else {
            return SchHairSalon::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HardwareStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHardwareStore::getInstance()->prop($prop, $val);
        } else {
            return SchHardwareStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HealthAndBeautyBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHealthAndBeautyBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchHealthAndBeautyBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HealthClub($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHealthClub::getInstance()->prop($prop, $val);
        } else {
            return SchHealthClub::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Highool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHighool::getInstance()->prop($prop, $val);
        } else {
            return SchHighool::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HinduTemple($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHinduTemple::getInstance()->prop($prop, $val);
        } else {
            return SchHinduTemple::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HobbyShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHobbyShop::getInstance()->prop($prop, $val);
        } else {
            return SchHobbyShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HomeAndConstructionBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHomeAndConstructionBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchHomeAndConstructionBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HomeGoodsStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHomeGoodsStore::getInstance()->prop($prop, $val);
        } else {
            return SchHomeGoodsStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Hospital($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHospital::getInstance()->prop($prop, $val);
        } else {
            return SchHospital::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Hostel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHostel::getInstance()->prop($prop, $val);
        } else {
            return SchHostel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Hotel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHotel::getInstance()->prop($prop, $val);
        } else {
            return SchHotel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function HousePainter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchHousePainter::getInstance()->prop($prop, $val);
        } else {
            return SchHousePainter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function IceCreamShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchIceCreamShop::getInstance()->prop($prop, $val);
        } else {
            return SchIceCreamShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function IgnoreAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchIgnoreAction::getInstance()->prop($prop, $val);
        } else {
            return SchIgnoreAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ImageGallery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchImageGallery::getInstance()->prop($prop, $val);
        } else {
            return SchImageGallery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ImageObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchImageObject::getInstance()->prop($prop, $val);
        } else {
            return SchImageObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ImagingTest($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchImagingTest::getInstance()->prop($prop, $val);
        } else {
            return SchImagingTest::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function IndividualProduct($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchIndividualProduct::getInstance()->prop($prop, $val);
        } else {
            return SchIndividualProduct::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InfectiousAgentClass($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInfectiousAgentClass::getInstance()->prop($prop, $val);
        } else {
            return SchInfectiousAgentClass::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InfectiousDisease($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInfectiousDisease::getInstance()->prop($prop, $val);
        } else {
            return SchInfectiousDisease::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InformAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInformAction::getInstance()->prop($prop, $val);
        } else {
            return SchInformAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InsertAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInsertAction::getInstance()->prop($prop, $val);
        } else {
            return SchInsertAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InstallAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInstallAction::getInstance()->prop($prop, $val);
        } else {
            return SchInstallAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InsuranceAgency($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInsuranceAgency::getInstance()->prop($prop, $val);
        } else {
            return SchInsuranceAgency::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Intangible($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchIntangible::getInstance()->prop($prop, $val);
        } else {
            return SchIntangible::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InteractAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInteractAction::getInstance()->prop($prop, $val);
        } else {
            return SchInteractAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InternetCafe($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInternetCafe::getInstance()->prop($prop, $val);
        } else {
            return SchInternetCafe::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function InviteAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchInviteAction::getInstance()->prop($prop, $val);
        } else {
            return SchInviteAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ItemAvailability($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchItemAvailability::getInstance()->prop($prop, $val);
        } else {
            return SchItemAvailability::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ItemList($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchItemList::getInstance()->prop($prop, $val);
        } else {
            return SchItemList::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ItemPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchItemPage::getInstance()->prop($prop, $val);
        } else {
            return SchItemPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function JewelryStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchJewelryStore::getInstance()->prop($prop, $val);
        } else {
            return SchJewelryStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function JobPosting($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchJobPosting::getInstance()->prop($prop, $val);
        } else {
            return SchJobPosting::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function JoinAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchJoinAction::getInstance()->prop($prop, $val);
        } else {
            return SchJoinAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Joint($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchJoint::getInstance()->prop($prop, $val);
        } else {
            return SchJoint::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LakeBodyOfWater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLakeBodyOfWater::getInstance()->prop($prop, $val);
        } else {
            return SchLakeBodyOfWater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Landform($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLandform::getInstance()->prop($prop, $val);
        } else {
            return SchLandform::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LandmarksOrHistoricalBuildings($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLandmarksOrHistoricalBuildings::getInstance()->prop($prop, $val);
        } else {
            return SchLandmarksOrHistoricalBuildings::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Language($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLanguage::getInstance()->prop($prop, $val);
        } else {
            return SchLanguage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LeaveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLeaveAction::getInstance()->prop($prop, $val);
        } else {
            return SchLeaveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LegislativeBuilding($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLegislativeBuilding::getInstance()->prop($prop, $val);
        } else {
            return SchLegislativeBuilding::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LendAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLendAction::getInstance()->prop($prop, $val);
        } else {
            return SchLendAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Library($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLibrary::getInstance()->prop($prop, $val);
        } else {
            return SchLibrary::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LifestyleModification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLifestyleModification::getInstance()->prop($prop, $val);
        } else {
            return SchLifestyleModification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Ligament($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLigament::getInstance()->prop($prop, $val);
        } else {
            return SchLigament::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LikeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLikeAction::getInstance()->prop($prop, $val);
        } else {
            return SchLikeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LiquorStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLiquorStore::getInstance()->prop($prop, $val);
        } else {
            return SchLiquorStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ListenAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchListenAction::getInstance()->prop($prop, $val);
        } else {
            return SchListenAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LiteraryEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLiteraryEvent::getInstance()->prop($prop, $val);
        } else {
            return SchLiteraryEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LocalBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLocalBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchLocalBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LockerDelivery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLockerDelivery::getInstance()->prop($prop, $val);
        } else {
            return SchLockerDelivery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Locksmith($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLocksmith::getInstance()->prop($prop, $val);
        } else {
            return SchLocksmith::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LodgingBusiness($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLodgingBusiness::getInstance()->prop($prop, $val);
        } else {
            return SchLodgingBusiness::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LodgingReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLodgingReservation::getInstance()->prop($prop, $val);
        } else {
            return SchLodgingReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LoseAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLoseAction::getInstance()->prop($prop, $val);
        } else {
            return SchLoseAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function LymphaticVessel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchLymphaticVessel::getInstance()->prop($prop, $val);
        } else {
            return SchLymphaticVessel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Map($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMap::getInstance()->prop($prop, $val);
        } else {
            return SchMap::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MarryAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMarryAction::getInstance()->prop($prop, $val);
        } else {
            return SchMarryAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Mass($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMass::getInstance()->prop($prop, $val);
        } else {
            return SchMass::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MaximumDoseedule($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMaximumDoseedule::getInstance()->prop($prop, $val);
        } else {
            return SchMaximumDoseedule::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MediaObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMediaObject::getInstance()->prop($prop, $val);
        } else {
            return SchMediaObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalAudience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalAudience::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalCause($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalCause::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalCause::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalClinic($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalClinic::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalClinic::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalCode($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalCode::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalCode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalCondition($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalCondition::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalCondition::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalConditionStage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalConditionStage::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalConditionStage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalContraindication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalContraindication::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalContraindication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalDevice($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalDevice::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalDevice::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalDevicePurpose($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalDevicePurpose::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalDevicePurpose::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalEntity($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalEntity::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalEntity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalEnumeration($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalEnumeration::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalEnumeration::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalEvidenceLevel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalEvidenceLevel::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalEvidenceLevel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalGuideline($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalGuideline::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalGuideline::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalGuidelineContraindication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalGuidelineContraindication::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalGuidelineContraindication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalGuidelineRecommendation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalGuidelineRecommendation::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalGuidelineRecommendation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalImagingTechnique($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalImagingTechnique::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalImagingTechnique::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalIndication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalIndication::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalIndication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalIntangible($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalIntangible::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalIntangible::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalObservationalStudy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalObservationalStudy::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalObservationalStudy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalObservationalStudyDesign($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalObservationalStudyDesign::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalObservationalStudyDesign::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalOrganization($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalOrganization::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalOrganization::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalProcedure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalProcedure::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalProcedure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalProcedureType($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalProcedureType::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalProcedureType::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalRiskCalculator($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalRiskCalculator::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalRiskCalculator::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalRiskEstimator($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalRiskEstimator::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalRiskEstimator::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalRiskFactor($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalRiskFactor::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalRiskFactor::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalRiskScore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalRiskScore::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalRiskScore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalolarlyArticle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalolarlyArticle::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalolarlyArticle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalSign($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalSign::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalSign::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalSignOrSymptom($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalSignOrSymptom::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalSignOrSymptom::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalSpecialty($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalSpecialty::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalSpecialty::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalStudy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalStudy::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalStudy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalStudyStatus($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalStudyStatus::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalStudyStatus::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalSymptom($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalSymptom::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalSymptom::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalTest($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalTest::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalTest::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalTestPanel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalTestPanel::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalTestPanel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalTherapy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalTherapy::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalTherapy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalTrial($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalTrial::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalTrial::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalTrialDesign($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalTrialDesign::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalTrialDesign::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicalWebPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicalWebPage::getInstance()->prop($prop, $val);
        } else {
            return SchMedicalWebPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MedicineSystem($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMedicineSystem::getInstance()->prop($prop, $val);
        } else {
            return SchMedicineSystem::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MensClothingStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMensClothingStore::getInstance()->prop($prop, $val);
        } else {
            return SchMensClothingStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Middleool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMiddleool::getInstance()->prop($prop, $val);
        } else {
            return SchMiddleool::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MobileApplication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMobileApplication::getInstance()->prop($prop, $val);
        } else {
            return SchMobileApplication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MobilePhoneStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMobilePhoneStore::getInstance()->prop($prop, $val);
        } else {
            return SchMobilePhoneStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Mosque($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMosque::getInstance()->prop($prop, $val);
        } else {
            return SchMosque::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Motel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMotel::getInstance()->prop($prop, $val);
        } else {
            return SchMotel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MotorcycleDealer($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMotorcycleDealer::getInstance()->prop($prop, $val);
        } else {
            return SchMotorcycleDealer::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MotorcycleRepair($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMotorcycleRepair::getInstance()->prop($prop, $val);
        } else {
            return SchMotorcycleRepair::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Mountain($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMountain::getInstance()->prop($prop, $val);
        } else {
            return SchMountain::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MoveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMoveAction::getInstance()->prop($prop, $val);
        } else {
            return SchMoveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Movie($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMovie::getInstance()->prop($prop, $val);
        } else {
            return SchMovie::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MovieRentalStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMovieRentalStore::getInstance()->prop($prop, $val);
        } else {
            return SchMovieRentalStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MovieTheater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMovieTheater::getInstance()->prop($prop, $val);
        } else {
            return SchMovieTheater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MovingCompany($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMovingCompany::getInstance()->prop($prop, $val);
        } else {
            return SchMovingCompany::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Muscle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMuscle::getInstance()->prop($prop, $val);
        } else {
            return SchMuscle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Museum($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMuseum::getInstance()->prop($prop, $val);
        } else {
            return SchMuseum::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicAlbum($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicAlbum::getInstance()->prop($prop, $val);
        } else {
            return SchMusicAlbum::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicEvent::getInstance()->prop($prop, $val);
        } else {
            return SchMusicEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicGroup($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicGroup::getInstance()->prop($prop, $val);
        } else {
            return SchMusicGroup::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicPlaylist($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicPlaylist::getInstance()->prop($prop, $val);
        } else {
            return SchMusicPlaylist::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicRecording($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicRecording::getInstance()->prop($prop, $val);
        } else {
            return SchMusicRecording::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicStore::getInstance()->prop($prop, $val);
        } else {
            return SchMusicStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicVenue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicVenue::getInstance()->prop($prop, $val);
        } else {
            return SchMusicVenue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function MusicVideoObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchMusicVideoObject::getInstance()->prop($prop, $val);
        } else {
            return SchMusicVideoObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function NGO($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNGO::getInstance()->prop($prop, $val);
        } else {
            return SchNGO::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function NailSalon($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNailSalon::getInstance()->prop($prop, $val);
        } else {
            return SchNailSalon::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Nerve($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNerve::getInstance()->prop($prop, $val);
        } else {
            return SchNerve::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function NewsArticle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNewsArticle::getInstance()->prop($prop, $val);
        } else {
            return SchNewsArticle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function NightClub($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNightClub::getInstance()->prop($prop, $val);
        } else {
            return SchNightClub::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Notary($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNotary::getInstance()->prop($prop, $val);
        } else {
            return SchNotary::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function NutritionInformation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchNutritionInformation::getInstance()->prop($prop, $val);
        } else {
            return SchNutritionInformation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OceanBodyOfWater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOceanBodyOfWater::getInstance()->prop($prop, $val);
        } else {
            return SchOceanBodyOfWater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Offer($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOffer::getInstance()->prop($prop, $val);
        } else {
            return SchOffer::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OfferItemCondition($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOfferItemCondition::getInstance()->prop($prop, $val);
        } else {
            return SchOfferItemCondition::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OfficeEquipmentStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOfficeEquipmentStore::getInstance()->prop($prop, $val);
        } else {
            return SchOfficeEquipmentStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OnDemandEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOnDemandEvent::getInstance()->prop($prop, $val);
        } else {
            return SchOnDemandEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OnSitePickup($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOnSitePickup::getInstance()->prop($prop, $val);
        } else {
            return SchOnSitePickup::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OpeningHoursSpecification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOpeningHoursSpecification::getInstance()->prop($prop, $val);
        } else {
            return SchOpeningHoursSpecification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Optician($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOptician::getInstance()->prop($prop, $val);
        } else {
            return SchOptician::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Order($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOrder::getInstance()->prop($prop, $val);
        } else {
            return SchOrder::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OrderAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOrderAction::getInstance()->prop($prop, $val);
        } else {
            return SchOrderAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OrderStatus($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOrderStatus::getInstance()->prop($prop, $val);
        } else {
            return SchOrderStatus::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Organization($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOrganization::getInstance()->prop($prop, $val);
        } else {
            return SchOrganization::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OrganizeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOrganizeAction::getInstance()->prop($prop, $val);
        } else {
            return SchOrganizeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OutletStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOutletStore::getInstance()->prop($prop, $val);
        } else {
            return SchOutletStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function OwnershipInfo($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchOwnershipInfo::getInstance()->prop($prop, $val);
        } else {
            return SchOwnershipInfo::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PaintAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPaintAction::getInstance()->prop($prop, $val);
        } else {
            return SchPaintAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Painting($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPainting::getInstance()->prop($prop, $val);
        } else {
            return SchPainting::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PalliativeProcedure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPalliativeProcedure::getInstance()->prop($prop, $val);
        } else {
            return SchPalliativeProcedure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ParcelDelivery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchParcelDelivery::getInstance()->prop($prop, $val);
        } else {
            return SchParcelDelivery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ParcelService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchParcelService::getInstance()->prop($prop, $val);
        } else {
            return SchParcelService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ParentAudience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchParentAudience::getInstance()->prop($prop, $val);
        } else {
            return SchParentAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Park($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPark::getInstance()->prop($prop, $val);
        } else {
            return SchPark::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ParkingFacility($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchParkingFacility::getInstance()->prop($prop, $val);
        } else {
            return SchParkingFacility::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PathologyTest($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPathologyTest::getInstance()->prop($prop, $val);
        } else {
            return SchPathologyTest::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PawnShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPawnShop::getInstance()->prop($prop, $val);
        } else {
            return SchPawnShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PayAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPayAction::getInstance()->prop($prop, $val);
        } else {
            return SchPayAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PaymentChargeSpecification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPaymentChargeSpecification::getInstance()->prop($prop, $val);
        } else {
            return SchPaymentChargeSpecification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PaymentMethod($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPaymentMethod::getInstance()->prop($prop, $val);
        } else {
            return SchPaymentMethod::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PeopleAudience($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPeopleAudience::getInstance()->prop($prop, $val);
        } else {
            return SchPeopleAudience::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PerformAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPerformAction::getInstance()->prop($prop, $val);
        } else {
            return SchPerformAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PerformingArtsTheater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPerformingArtsTheater::getInstance()->prop($prop, $val);
        } else {
            return SchPerformingArtsTheater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PerformingGroup($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPerformingGroup::getInstance()->prop($prop, $val);
        } else {
            return SchPerformingGroup::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Periodical($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPeriodical::getInstance()->prop($prop, $val);
        } else {
            return SchPeriodical::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Permit($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPermit::getInstance()->prop($prop, $val);
        } else {
            return SchPermit::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Person($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPerson::getInstance()->prop($prop, $val);
        } else {
            return SchPerson::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PetStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPetStore::getInstance()->prop($prop, $val);
        } else {
            return SchPetStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Pharmacy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPharmacy::getInstance()->prop($prop, $val);
        } else {
            return SchPharmacy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Photograph($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhotograph::getInstance()->prop($prop, $val);
        } else {
            return SchPhotograph::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PhotographAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhotographAction::getInstance()->prop($prop, $val);
        } else {
            return SchPhotographAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PhysicalActivity($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhysicalActivity::getInstance()->prop($prop, $val);
        } else {
            return SchPhysicalActivity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PhysicalActivityCategory($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhysicalActivityCategory::getInstance()->prop($prop, $val);
        } else {
            return SchPhysicalActivityCategory::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PhysicalExam($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhysicalExam::getInstance()->prop($prop, $val);
        } else {
            return SchPhysicalExam::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PhysicalTherapy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhysicalTherapy::getInstance()->prop($prop, $val);
        } else {
            return SchPhysicalTherapy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Physician($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPhysician::getInstance()->prop($prop, $val);
        } else {
            return SchPhysician::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Place($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlace::getInstance()->prop($prop, $val);
        } else {
            return SchPlace::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PlaceOfWorship($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlaceOfWorship::getInstance()->prop($prop, $val);
        } else {
            return SchPlaceOfWorship::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PlanAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlanAction::getInstance()->prop($prop, $val);
        } else {
            return SchPlanAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PlayAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlayAction::getInstance()->prop($prop, $val);
        } else {
            return SchPlayAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Playground($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlayground::getInstance()->prop($prop, $val);
        } else {
            return SchPlayground::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Plumber($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPlumber::getInstance()->prop($prop, $val);
        } else {
            return SchPlumber::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PoliceStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPoliceStation::getInstance()->prop($prop, $val);
        } else {
            return SchPoliceStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Pond($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPond::getInstance()->prop($prop, $val);
        } else {
            return SchPond::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PostOffice($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPostOffice::getInstance()->prop($prop, $val);
        } else {
            return SchPostOffice::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PostalAddress($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPostalAddress::getInstance()->prop($prop, $val);
        } else {
            return SchPostalAddress::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PrependAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPrependAction::getInstance()->prop($prop, $val);
        } else {
            return SchPrependAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Preschool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPreschool::getInstance()->prop($prop, $val);
        } else {
            return SchPreschool::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PreventionIndication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPreventionIndication::getInstance()->prop($prop, $val);
        } else {
            return SchPreventionIndication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PriceSpecification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPriceSpecification::getInstance()->prop($prop, $val);
        } else {
            return SchPriceSpecification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Product($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProduct::getInstance()->prop($prop, $val);
        } else {
            return SchProduct::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ProductModel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProductModel::getInstance()->prop($prop, $val);
        } else {
            return SchProductModel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ProfessionalService($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProfessionalService::getInstance()->prop($prop, $val);
        } else {
            return SchProfessionalService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ProfilePage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProfilePage::getInstance()->prop($prop, $val);
        } else {
            return SchProfilePage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ProgramMembership($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProgramMembership::getInstance()->prop($prop, $val);
        } else {
            return SchProgramMembership::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Property($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchProperty::getInstance()->prop($prop, $val);
        } else {
            return SchProperty::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PsychologicalTreatment($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPsychologicalTreatment::getInstance()->prop($prop, $val);
        } else {
            return SchPsychologicalTreatment::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PublicSwimmingPool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPublicSwimmingPool::getInstance()->prop($prop, $val);
        } else {
            return SchPublicSwimmingPool::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PublicationEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPublicationEvent::getInstance()->prop($prop, $val);
        } else {
            return SchPublicationEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PublicationIssue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPublicationIssue::getInstance()->prop($prop, $val);
        } else {
            return SchPublicationIssue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function PublicationVolume($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchPublicationVolume::getInstance()->prop($prop, $val);
        } else {
            return SchPublicationVolume::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function QAPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQAPage::getInstance()->prop($prop, $val);
        } else {
            return SchQAPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function QualitativeValue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQualitativeValue::getInstance()->prop($prop, $val);
        } else {
            return SchQualitativeValue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function QuantitativeValue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQuantitativeValue::getInstance()->prop($prop, $val);
        } else {
            return SchQuantitativeValue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Quantity($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQuantity::getInstance()->prop($prop, $val);
        } else {
            return SchQuantity::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Question($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQuestion::getInstance()->prop($prop, $val);
        } else {
            return SchQuestion::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function QuoteAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchQuoteAction::getInstance()->prop($prop, $val);
        } else {
            return SchQuoteAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RVPark($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRVPark::getInstance()->prop($prop, $val);
        } else {
            return SchRVPark::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadiationTherapy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadiationTherapy::getInstance()->prop($prop, $val);
        } else {
            return SchRadiationTherapy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadioClip($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadioClip::getInstance()->prop($prop, $val);
        } else {
            return SchRadioClip::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadioEpisode($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadioEpisode::getInstance()->prop($prop, $val);
        } else {
            return SchRadioEpisode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadioSeason($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadioSeason::getInstance()->prop($prop, $val);
        } else {
            return SchRadioSeason::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadioSeries($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadioSeries::getInstance()->prop($prop, $val);
        } else {
            return SchRadioSeries::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RadioStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRadioStation::getInstance()->prop($prop, $val);
        } else {
            return SchRadioStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Rating($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRating::getInstance()->prop($prop, $val);
        } else {
            return SchRating::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReactAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReactAction::getInstance()->prop($prop, $val);
        } else {
            return SchReactAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReadAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReadAction::getInstance()->prop($prop, $val);
        } else {
            return SchReadAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RealEstateAgent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRealEstateAgent::getInstance()->prop($prop, $val);
        } else {
            return SchRealEstateAgent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReceiveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReceiveAction::getInstance()->prop($prop, $val);
        } else {
            return SchReceiveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Recipe($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRecipe::getInstance()->prop($prop, $val);
        } else {
            return SchRecipe::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RecommendedDoseedule($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRecommendedDoseedule::getInstance()->prop($prop, $val);
        } else {
            return SchRecommendedDoseedule::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RecyclingCenter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRecyclingCenter::getInstance()->prop($prop, $val);
        } else {
            return SchRecyclingCenter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RegisterAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRegisterAction::getInstance()->prop($prop, $val);
        } else {
            return SchRegisterAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RejectAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRejectAction::getInstance()->prop($prop, $val);
        } else {
            return SchRejectAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RentAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRentAction::getInstance()->prop($prop, $val);
        } else {
            return SchRentAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RentalCarReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRentalCarReservation::getInstance()->prop($prop, $val);
        } else {
            return SchRentalCarReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReplaceAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReplaceAction::getInstance()->prop($prop, $val);
        } else {
            return SchReplaceAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReplyAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReplyAction::getInstance()->prop($prop, $val);
        } else {
            return SchReplyAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReportedDoseedule($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReportedDoseedule::getInstance()->prop($prop, $val);
        } else {
            return SchReportedDoseedule::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Reservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReservation::getInstance()->prop($prop, $val);
        } else {
            return SchReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReservationPackage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReservationPackage::getInstance()->prop($prop, $val);
        } else {
            return SchReservationPackage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReservationStatusType($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReservationStatusType::getInstance()->prop($prop, $val);
        } else {
            return SchReservationStatusType::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReserveAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReserveAction::getInstance()->prop($prop, $val);
        } else {
            return SchReserveAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Reservoir($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReservoir::getInstance()->prop($prop, $val);
        } else {
            return SchReservoir::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Residence($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchResidence::getInstance()->prop($prop, $val);
        } else {
            return SchResidence::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Restaurant($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRestaurant::getInstance()->prop($prop, $val);
        } else {
            return SchRestaurant::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReturnAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReturnAction::getInstance()->prop($prop, $val);
        } else {
            return SchReturnAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Review($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReview::getInstance()->prop($prop, $val);
        } else {
            return SchReview::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ReviewAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchReviewAction::getInstance()->prop($prop, $val);
        } else {
            return SchReviewAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RiverBodyOfWater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRiverBodyOfWater::getInstance()->prop($prop, $val);
        } else {
            return SchRiverBodyOfWater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RoofingContractor($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRoofingContractor::getInstance()->prop($prop, $val);
        } else {
            return SchRoofingContractor::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function RsvpAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchRsvpAction::getInstance()->prop($prop, $val);
        } else {
            return SchRsvpAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SaleEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSaleEvent::getInstance()->prop($prop, $val);
        } else {
            return SchSaleEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function eduleAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return ScheduleAction::getInstance()->prop($prop, $val);
        } else {
            return ScheduleAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function olarlyArticle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return ScholarlyArticle::getInstance()->prop($prop, $val);
        } else {
            return ScholarlyArticle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ool($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return School::getInstance()->prop($prop, $val);
        } else {
            return School::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Sculpture($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSculpture::getInstance()->prop($prop, $val);
        } else {
            return SchSculpture::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SeaBodyOfWater($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSeaBodyOfWater::getInstance()->prop($prop, $val);
        } else {
            return SchSeaBodyOfWater::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SearchAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSearchAction::getInstance()->prop($prop, $val);
        } else {
            return SchSearchAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SearchResultsPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSearchResultsPage::getInstance()->prop($prop, $val);
        } else {
            return SchSearchResultsPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Season($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSeason::getInstance()->prop($prop, $val);
        } else {
            return SchSeason::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Seat($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSeat::getInstance()->prop($prop, $val);
        } else {
            return SchSeat::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SelfStorage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSelfStorage::getInstance()->prop($prop, $val);
        } else {
            return SchSelfStorage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SellAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSellAction::getInstance()->prop($prop, $val);
        } else {
            return SchSellAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SendAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSendAction::getInstance()->prop($prop, $val);
        } else {
            return SchSendAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Series($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSeries::getInstance()->prop($prop, $val);
        } else {
            return SchSeries::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Service($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchService::getInstance()->prop($prop, $val);
        } else {
            return SchService::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ServiceChannel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchServiceChannel::getInstance()->prop($prop, $val);
        } else {
            return SchServiceChannel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ShareAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchShareAction::getInstance()->prop($prop, $val);
        } else {
            return SchShareAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ShoeStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchShoeStore::getInstance()->prop($prop, $val);
        } else {
            return SchShoeStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ShoppingCenter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchShoppingCenter::getInstance()->prop($prop, $val);
        } else {
            return SchShoppingCenter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SingleFamilyResidence($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSingleFamilyResidence::getInstance()->prop($prop, $val);
        } else {
            return SchSingleFamilyResidence::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SiteNavigationElement($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSiteNavigationElement::getInstance()->prop($prop, $val);
        } else {
            return SchSiteNavigationElement::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SkiResort($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSkiResort::getInstance()->prop($prop, $val);
        } else {
            return SchSkiResort::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SocialEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSocialEvent::getInstance()->prop($prop, $val);
        } else {
            return SchSocialEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SoftwareApplication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSoftwareApplication::getInstance()->prop($prop, $val);
        } else {
            return SchSoftwareApplication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SomeProducts($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSomeProducts::getInstance()->prop($prop, $val);
        } else {
            return SchSomeProducts::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Specialty($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSpecialty::getInstance()->prop($prop, $val);
        } else {
            return SchSpecialty::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SportingGoodsStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSportingGoodsStore::getInstance()->prop($prop, $val);
        } else {
            return SchSportingGoodsStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SportsActivityLocation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSportsActivityLocation::getInstance()->prop($prop, $val);
        } else {
            return SchSportsActivityLocation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SportsClub($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSportsClub::getInstance()->prop($prop, $val);
        } else {
            return SchSportsClub::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SportsEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSportsEvent::getInstance()->prop($prop, $val);
        } else {
            return SchSportsEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SportsTeam($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSportsTeam::getInstance()->prop($prop, $val);
        } else {
            return SchSportsTeam::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function StadiumOrArena($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchStadiumOrArena::getInstance()->prop($prop, $val);
        } else {
            return SchStadiumOrArena::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function State($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchState::getInstance()->prop($prop, $val);
        } else {
            return SchState::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Store($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchStore::getInstance()->prop($prop, $val);
        } else {
            return SchStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function StructuredValue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchStructuredValue::getInstance()->prop($prop, $val);
        } else {
            return SchStructuredValue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SubscribeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSubscribeAction::getInstance()->prop($prop, $val);
        } else {
            return SchSubscribeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SubwayStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSubwayStation::getInstance()->prop($prop, $val);
        } else {
            return SchSubwayStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function SuperficialAnatomy($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSuperficialAnatomy::getInstance()->prop($prop, $val);
        } else {
            return SchSuperficialAnatomy::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Synagogue($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchSynagogue::getInstance()->prop($prop, $val);
        } else {
            return SchSynagogue::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TVClip($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTVClip::getInstance()->prop($prop, $val);
        } else {
            return SchTVClip::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TVEpisode($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTVEpisode::getInstance()->prop($prop, $val);
        } else {
            return SchTVEpisode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TVSeason($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTVSeason::getInstance()->prop($prop, $val);
        } else {
            return SchTVSeason::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TVSeries($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTVSeries::getInstance()->prop($prop, $val);
        } else {
            return SchTVSeries::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Table($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTable::getInstance()->prop($prop, $val);
        } else {
            return SchTable::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TakeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTakeAction::getInstance()->prop($prop, $val);
        } else {
            return SchTakeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TattooParlor($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTattooParlor::getInstance()->prop($prop, $val);
        } else {
            return SchTattooParlor::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Taxi($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTaxi::getInstance()->prop($prop, $val);
        } else {
            return SchTaxi::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TaxiReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTaxiReservation::getInstance()->prop($prop, $val);
        } else {
            return SchTaxiReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TaxiStand($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTaxiStand::getInstance()->prop($prop, $val);
        } else {
            return SchTaxiStand::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TechArticle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTechArticle::getInstance()->prop($prop, $val);
        } else {
            return SchTechArticle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TelevisionStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTelevisionStation::getInstance()->prop($prop, $val);
        } else {
            return SchTelevisionStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TennisComplex($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTennisComplex::getInstance()->prop($prop, $val);
        } else {
            return SchTennisComplex::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TheaterEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTheaterEvent::getInstance()->prop($prop, $val);
        } else {
            return SchTheaterEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TheaterGroup($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTheaterGroup::getInstance()->prop($prop, $val);
        } else {
            return SchTheaterGroup::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TherapeuticProcedure($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTherapeuticProcedure::getInstance()->prop($prop, $val);
        } else {
            return SchTherapeuticProcedure::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Thing($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchThing::getInstance()->prop($prop, $val);
        } else {
            return SchThing::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Ticket($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTicket::getInstance()->prop($prop, $val);
        } else {
            return SchTicket::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TieAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTieAction::getInstance()->prop($prop, $val);
        } else {
            return SchTieAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TipAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTipAction::getInstance()->prop($prop, $val);
        } else {
            return SchTipAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TireShop($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTireShop::getInstance()->prop($prop, $val);
        } else {
            return SchTireShop::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TouristAttraction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTouristAttraction::getInstance()->prop($prop, $val);
        } else {
            return SchTouristAttraction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TouristInformationCenter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTouristInformationCenter::getInstance()->prop($prop, $val);
        } else {
            return SchTouristInformationCenter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ToyStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchToyStore::getInstance()->prop($prop, $val);
        } else {
            return SchToyStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TrackAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTrackAction::getInstance()->prop($prop, $val);
        } else {
            return SchTrackAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TradeAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTradeAction::getInstance()->prop($prop, $val);
        } else {
            return SchTradeAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TrainReservation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTrainReservation::getInstance()->prop($prop, $val);
        } else {
            return SchTrainReservation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TrainStation($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTrainStation::getInstance()->prop($prop, $val);
        } else {
            return SchTrainStation::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TrainTrip($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTrainTrip::getInstance()->prop($prop, $val);
        } else {
            return SchTrainTrip::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TransferAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTransferAction::getInstance()->prop($prop, $val);
        } else {
            return SchTransferAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TravelAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTravelAction::getInstance()->prop($prop, $val);
        } else {
            return SchTravelAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TravelAgency($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTravelAgency::getInstance()->prop($prop, $val);
        } else {
            return SchTravelAgency::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TreatmentIndication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTreatmentIndication::getInstance()->prop($prop, $val);
        } else {
            return SchTreatmentIndication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function TypeAndQuantityNode($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchTypeAndQuantityNode::getInstance()->prop($prop, $val);
        } else {
            return SchTypeAndQuantityNode::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function URL($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchURL::getInstance()->prop($prop, $val);
        } else {
            return SchURL::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UnRegisterAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUnRegisterAction::getInstance()->prop($prop, $val);
        } else {
            return SchUnRegisterAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UnitPriceSpecification($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUnitPriceSpecification::getInstance()->prop($prop, $val);
        } else {
            return SchUnitPriceSpecification::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UpdateAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUpdateAction::getInstance()->prop($prop, $val);
        } else {
            return SchUpdateAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UseAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUseAction::getInstance()->prop($prop, $val);
        } else {
            return SchUseAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserBlocks($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserBlocks::getInstance()->prop($prop, $val);
        } else {
            return SchUserBlocks::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserCheckins($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserCheckins::getInstance()->prop($prop, $val);
        } else {
            return SchUserCheckins::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserComments($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserComments::getInstance()->prop($prop, $val);
        } else {
            return SchUserComments::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserDownloads($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserDownloads::getInstance()->prop($prop, $val);
        } else {
            return SchUserDownloads::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserInteraction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserInteraction::getInstance()->prop($prop, $val);
        } else {
            return SchUserInteraction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserLikes($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserLikes::getInstance()->prop($prop, $val);
        } else {
            return SchUserLikes::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserPageVisits($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserPageVisits::getInstance()->prop($prop, $val);
        } else {
            return SchUserPageVisits::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserPlays($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserPlays::getInstance()->prop($prop, $val);
        } else {
            return SchUserPlays::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserPlusOnes($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserPlusOnes::getInstance()->prop($prop, $val);
        } else {
            return SchUserPlusOnes::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function UserTweets($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchUserTweets::getInstance()->prop($prop, $val);
        } else {
            return SchUserTweets::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Vehicle($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVehicle::getInstance()->prop($prop, $val);
        } else {
            return SchVehicle::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Vein($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVein::getInstance()->prop($prop, $val);
        } else {
            return SchVein::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Vessel($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVessel::getInstance()->prop($prop, $val);
        } else {
            return SchVessel::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function VeterinaryCare($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVeterinaryCare::getInstance()->prop($prop, $val);
        } else {
            return SchVeterinaryCare::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function VideoGallery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVideoGallery::getInstance()->prop($prop, $val);
        } else {
            return SchVideoGallery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function VideoObject($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVideoObject::getInstance()->prop($prop, $val);
        } else {
            return SchVideoObject::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function ViewAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchViewAction::getInstance()->prop($prop, $val);
        } else {
            return SchViewAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function VisualArtsEvent($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVisualArtsEvent::getInstance()->prop($prop, $val);
        } else {
            return SchVisualArtsEvent::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Volcano($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVolcano::getInstance()->prop($prop, $val);
        } else {
            return SchVolcano::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function VoteAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchVoteAction::getInstance()->prop($prop, $val);
        } else {
            return SchVoteAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WPAdBlock($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWPAdBlock::getInstance()->prop($prop, $val);
        } else {
            return SchWPAdBlock::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WPFooter($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWPFooter::getInstance()->prop($prop, $val);
        } else {
            return SchWPFooter::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WPHeader($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWPHeader::getInstance()->prop($prop, $val);
        } else {
            return SchWPHeader::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WPSideBar($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWPSideBar::getInstance()->prop($prop, $val);
        } else {
            return SchWPSideBar::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WantAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWantAction::getInstance()->prop($prop, $val);
        } else {
            return SchWantAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WarrantyPromise($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWarrantyPromise::getInstance()->prop($prop, $val);
        } else {
            return SchWarrantyPromise::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WarrantyScope($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWarrantyScope::getInstance()->prop($prop, $val);
        } else {
            return SchWarrantyScope::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WatchAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWatchAction::getInstance()->prop($prop, $val);
        } else {
            return SchWatchAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Waterfall($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWaterfall::getInstance()->prop($prop, $val);
        } else {
            return SchWaterfall::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WearAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWearAction::getInstance()->prop($prop, $val);
        } else {
            return SchWearAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WebApplication($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWebApplication::getInstance()->prop($prop, $val);
        } else {
            return SchWebApplication::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WebPage($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWebPage::getInstance()->prop($prop, $val);
        } else {
            return SchWebPage::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WebPageElement($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWebPageElement::getInstance()->prop($prop, $val);
        } else {
            return SchWebPageElement::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WholesaleStore($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWholesaleStore::getInstance()->prop($prop, $val);
        } else {
            return SchWholesaleStore::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WinAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWinAction::getInstance()->prop($prop, $val);
        } else {
            return SchWinAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Winery($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWinery::getInstance()->prop($prop, $val);
        } else {
            return SchWinery::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function WriteAction($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchWriteAction::getInstance()->prop($prop, $val);
        } else {
            return SchWriteAction::getInstance()->scope();
        }
    }

    /**
     * @param null $prop
     * @param null $val
     * @return mixed
     */
    public function Zoo($prop = null, $val = null)
    {
        if ( $prop != null ) {
            return SchZoo::getInstance()->prop($prop, $val);
        } else {
            return SchZoo::getInstance()->scope();
        }
    }
}