<?php
// PackageBuilder.php

// Abstract Builder Class
abstract class PackageBuilder
{
    protected $package;

    public function __construct()
    {
        $this->package = new Package(); // Initialize the package object
    }

    abstract public function addDestinations();
    abstract public function addMoneySaved($moneySaved);
    abstract public function addDayCount($dayCount);
    abstract public function addPickup($pickup);
    abstract public function addTransportType($transportType);
    abstract public function addCost($cost);
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
    public $cost = [];
    public $details;
    public $image;
}

// Concrete Builder for Tour Package
class TourPackageBuilder extends PackageBuilder
{
    private $selectedDestinations;

    public function __construct($selectedDestinations)
    {
        parent::__construct();
        $this->selectedDestinations = $selectedDestinations;
    }

    public function addDestinations()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->destinations[] = $destination['name'];
        }
    }

    public function addMoneySaved($moneySaved)
    {
        $this->package->moneySaved[] = $moneySaved;
    }

    public function addDayCount($dayCount)
    {
        $this->package->dayCount[] = $dayCount;
    }

    public function addPickup($pickup)
    {
        $this->package->pickup[] = $pickup;
    }

    public function addTransportType($transportType)
    {
        $this->package->transportType[] = $transportType;
    }

    public function addCost($cost)
    {
        $this->package->cost[] = $cost;
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

    public function buildPackage($moneySaved, $dayCount, $pickup, $transportType, $cost, $details, $image)
    {
        $this->builder->addDestinations();
        $this->builder->addMoneySaved($moneySaved);
        $this->builder->addDayCount($dayCount);
        $this->builder->addPickup($pickup);
        $this->builder->addTransportType($transportType);
        $this->builder->addCost($cost);
        $this->builder->addDetails($details);
        $this->builder->addImage($image);
    }
}
?>
