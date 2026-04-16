<?php
require_once __DIR__."/../php/NotFoundEx.php";
require_once __DIR__."/../php/Country.php";
require_once __DIR__."/../php/SubDivision.php";
require_once __DIR__."/../php/Store.php";

/**
 * Class BasicTest
 * @package ${NAMESPACE}
 * @author Chris Seufert <chris@modd.com.au>
 */
class BasicTest extends \PHPUnit\Framework\TestCase {

  public function testFound() {
    $store = new Seufert\ISO3166\Store();
    $aus = $store->countryByID("AU");
    $this->assertEquals("Australia",$aus->name);
    $this->assertEquals("AU",$aus->code);
    $this->assertEquals("AUD",$aus->currency);
    $this->assertEquals("61",$aus->phonePrefix);
    $states = $store->getAllStates($aus);
    $this->assertEquals(8,count($states));
  }

  public function testFound2() {
    $store = new Seufert\ISO3166\Store();
    $aus = $store->countryByID("NZ");
    $this->assertEquals("New Zealand",$aus->name);
    $this->assertEquals("NZ",$aus->code);
    $this->assertEquals("NZD",$aus->currency);
    $this->assertEquals("64",$aus->phonePrefix);
    $states = $store->getAllStates($aus);
    $this->assertEquals(17,count($states));
  }

  public function testSubDivFound() {
    $store = new Seufert\ISO3166\Store();
    $aus = $store->countryByID("AU");
    $vic = $store->subDivByID($aus, "VIC");
    $this->assertEquals("Victoria", $vic->name);
    $this->assertEquals("VIC", $vic->code);
    $usa = $store->countryByID("US");
    $ca = $store->subDivByID($usa, "CA");
    $this->assertEquals("California", $ca->name);
    $this->assertEquals("CA", $ca->code);
  }

  public function testSubDivList() {
    $store = new Seufert\ISO3166\Store();
    $aus = $store->countryByID("AU");
    $auStates = $store->subDivByCountry($aus);
    $this->assertEquals(8,count($auStates));
    $us = $store->countryByID("US");
    $usStates = $store->subDivByCountry($us);
    $this->assertEquals(51,count($usStates));
  }

  /**
   * @throws \Seufert\ISO3166\NotFoundEx
   *
   */
  public function testNotFound() {
      $this->expectException(Seufert\ISO3166\NotFoundEx::class);
      $store = new Seufert\ISO3166\Store();
    $notfound = $store->countryByID("ZZ");
  }
  
  /**
   * @throws \Seufert\ISO3166\NotFoundEx
   *
   */
  public function testNotFoundSubDiv() {
      $this->expectException(Seufert\ISO3166\NotFoundEx::class);
      $store = new Seufert\ISO3166\Store();
    $au = $store->countryByID("AU");
    $zzz = $store->subDivByID($au,'ZZZ');
  }

  public function testlist() {
    $store = new seufert\iso3166\store();
    $list = $store->listbycode(['AU','NZ','GB','US']);
    $this->assertequals(4,count($list));

  }

    public function testTaiwan() {
        $store = new Seufert\ISO3166\Store();
        $list = $store->listByCode(['TW']);
        $this->assertEquals(1,count($list));
        $this->assertEquals('Taiwan', $list[0]->name);
    }

    public function testHongKong() {
        $store = new Seufert\ISO3166\Store();
        $list = $store->listByCode(['HK']);
        $this->assertEquals(1,count($list));
        $this->assertEquals('Hong Kong', $list[0]->name);
    }

}
