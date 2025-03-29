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
    abstract public function addMoneySaved();
    abstract public function addDayCount();
    abstract public function addPickup();
    abstract public function addTransportType();
    abstract public function addCost();

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
}

// Concrete Builder for Tour Package
class TourPackageBuilder extends PackageBuilder
{
    public function addDestinations()
    {
        $this->package->destinations[] = "Bangladesh";  // Example destination
    }

    public function addMoneySaved()
    {
        $this->package->moneySaved[] = 1000;  // Example money saved
    }

    public function addDayCount()
    {
        $this->package->dayCount[] = 7;  // Example days count
    }

    public function addPickup()
    {
        $this->package->pickup[] = "Hotel Pickup";  // Example pickup
    }

    public function addTransportType()
    {
        $this->package->transportType[] = "Bus";  // Example transport type
    }

    public function addCost()
    {
        $this->package->cost[] = 5000;  // Example cost
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

    public function buildPackage()
    {
        $this->builder->addDestinations();
        $this->builder->addMoneySaved();
        $this->builder->addDayCount();
        $this->builder->addPickup();
        $this->builder->addTransportType();
        $this->builder->addCost();
    }
}
?>
