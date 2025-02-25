<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use AdamNatad\LaravelNTPayments\Facades\NTPayments;

class NTPackageTest extends TestCase
{
    /**
     * Test if Laravel-NTPayments package is loaded successfully.
     */
    public function testPackageIsLoaded()
    {
        $this->assertTrue(class_exists(NTPayments::class), "Laravel-NTPayments package is not loaded.");
    }

    /**
     * Test if service provider is registered.
     */
    public function testServiceProviderIsRegistered()
    {
        $this->assertTrue(class_exists('AdamNatad\LaravelNTPayments\Providers\NTPProvider'), "Service provider is not registered.");
    }

    /**
     * Test if facade is accessible.
     */
    public function testFacadeIsAccessible()
    {
        $this->assertTrue(class_exists(NTPayments::class), "Facade NTPayments is not accessible.");
    }
}
