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

    public function addMoneySaved($moneySaved)
    {
        // Add the dynamically received money saved value to the package
        $this->package->moneySaved[] = $moneySaved;
    }

    public function addDayCount($dayCount)
    {
        // Add the dynamically received day count value to the package
        $this->package->dayCount[] = $dayCount;
    }

    public function addPickup($pickup)
    {
        // Add the dynamically received pickup value to the package
        $this->package->pickup[] = $pickup;
    }

    public function addTransportType($transportType)
    {
        // Add the dynamically received transport type value to the package
        $this->package->transportType[] = $transportType;
    }

    public function addCost($cost)
    {
        // Add the dynamically received cost value to the package
        $this->package->cost[] = $cost;
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

    public function buildPackage($moneySaved, $dayCount, $pickup, $transportType, $cost)
    {
        $this->builder->addDestinations();
        
        // Pass dynamic values to builder methods
        $this->builder->addMoneySaved($moneySaved);
        $this->builder->addDayCount($dayCount);
        $this->builder->addPickup($pickup);
        $this->builder->addTransportType($transportType);
        $this->builder->addCost($cost);
    }
}
?>
