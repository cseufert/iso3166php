<?php
namespace Seufert\ISO3166;

/**
 * Class SubDivision
 * @package Seufert\ISO3166
 * @author Chris Seufert <chris@modd.com.au>
 */
class SubDivision {

  public $country = null;
  public $code = "";
  public $name = "";

  function __construct(Country $country, $code, $name) {
    $this->country = $country;
    $this->code = $code;
    $this->name = $name;
  }
}