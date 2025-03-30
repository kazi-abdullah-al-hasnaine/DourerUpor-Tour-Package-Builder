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

    public function addMoneySaved()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->moneySaved[] = 1000; // Example money saved, can be adjusted
        }
    }

    public function addDayCount()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->dayCount[] = 7; // Default days count
        }
    }

    public function addPickup()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->pickup[] = "Hotel Pickup"; // Example pickup, can be dynamic
        }
    }

    public function addTransportType()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->transportType[] = "Bus"; // Example transport type
        }
    }

    public function addCost()
    {
        foreach ($this->selectedDestinations as $destination) {
            $this->package->cost[] = $destination['cost']; // Use the actual cost from the database
        }
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
