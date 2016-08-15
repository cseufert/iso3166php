<?php
require_once __DIR__."/../php/NotFoundEx.php";
require_once __DIR__."/../php/Country.php";
require_once __DIR__."/../php/Store.php";

/**
 * Class BasicTest
 * @package ${NAMESPACE}
 * @author Chris Seufert <chris@modd.com.au>
 */
class BasicTest extends PHPUnit_Framework_TestCase {

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

  /**
   * @throws \Seufert\ISO3166\NotFoundEx
   * @expectedException Seufert\ISO3166\NotFoundEx
   */
  public function testNotFound() {
    $store = new Seufert\ISO3166\Store();
    $notfound = $store->countryByID("ZZ");
  }
  
  public function testList() {
    $store = new Seufert\ISO3166\Store();
    $list = $store->listByCode(['AU','NZ','GB','US']);
    $this->assertEquals(4,count($list));

  }

}