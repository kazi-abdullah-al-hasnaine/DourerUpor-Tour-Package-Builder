<?php
// PackageBuilder.php

// Abstract Builder Class
abstract class PackageBuilder
{
    protected $package;

    public function __construct()
    {
        $this->package = new Package();
    }

    abstract public function addDestinations($destinations);
    abstract public function addMoneySaved($moneySaved);
    abstract public function addDayCount($dayCount);
    abstract public function addPickup($pickup);
    abstract public function addTransportType($transportType);
    abstract public function addTransportCost($transportCost);
    abstract public function addDetails($details);
    abstract public function addImage($image);

    public function getPackage()
    {
        return $this->package;
    }
}

// Product Class
class Package
{
    public $destinations = [];
    public $moneySaved = [];
    public $dayCount = [];
    public $pickup = [];
    public $transportType = [];
    public $transportCost = [];
    public $details;
    public $image;
}

// Concrete Builder for Tour Package
class TourPackageBuilder extends PackageBuilder
{
    public function addDestinations($destinations)
    {
        $this->package->destinations = $destinations;
    }

    public function addMoneySaved($moneySaved)
    {
        $this->package->moneySaved = $moneySaved;
    }

    public function addDayCount($dayCount)
    {
        $this->package->dayCount = $dayCount;
    }

    public function addPickup($pickup)
    {
        $this->package->pickup = $pickup;
    }

    public function addTransportType($transportType)
    {
        $this->package->transportType = $transportType;
    }

    public function addTransportCost($transportCost)
    {
        $this->package->transportCost = $transportCost;
    }

    public function addDetails($details)
    {
        $this->package->details = $details;
    }

    public function addImage($image)
    {
        $this->package->image = $image;
    }
}

// Director Class
class PackageDirector
{
    private $builder;

    public function __construct(PackageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function buildPackage($destinations, $moneySaved, $dayCount, $pickup, $transportType, $transportCost, $details, $image)
    {
        $this->builder->addDestinations($destinations);
        $this->builder->addMoneySaved($moneySaved);
        $this->builder->addDayCount($dayCount);
        $this->builder->addPickup($pickup);
        $this->builder->addTransportType($transportType);
        $this->builder->addTransportCost($transportCost);
        $this->builder->addDetails($details);
        $this->builder->addImage($image);
    }
}
