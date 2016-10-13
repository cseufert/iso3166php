<?php
namespace Seufert\ISO3166;

/**
 * Class Store
 * @package Seufert\ISO3166
 * @author Chris Seufert <chris@modd.com.au>
 */
class Store {

  /** @var \SQLite3 Database containing Country Data */
  public $db;

  function __construct() {
    $dbPath = implode(DIRECTORY_SEPARATOR,[__DIR__,'..','data','iso-3166.sqlite3']);
    $this->db = new \SQLite3($dbPath,SQLITE3_OPEN_READONLY);
  }

  /**
   * @param $code 2 Letter ISO3166 country code (CAPS)
   * @return Country
   * @throws NotFoundEx
   */
  function countryByID($code) {
    $row = $this->db->querySingle("SELECT CommonName as name, ISO4217CurrencyCode as currency, \"ITU-TTelephoneCode\" as phonePrefix, \"ISO3166-12LetterCode\" as code FROM country ".
      "WHERE \"ISO3166-12LetterCode\" = '$code' AND Type = 'Independent State'", true);
    if(!$row)
      throw new NotFoundEx("Not Found");
    $o = new Country();
    foreach(['name', 'currency','code','phonePrefix'] as $k)
      $o->$k = $row[$k];
    return $o;
  }

  /**
   * @param string[] $codeList List of Country codes to return in list (if none, return all) Always all CAPS
   * @return Country[] List of Countries
   */
  function listByCode($codeList = []) {
    $q = "SELECT CommonName as name, ISO4217CurrencyCode as currency, \"ITU-TTelephoneCode\" as phonePrefix, \"ISO3166-12LetterCode\" as code FROM country WHERE Type='Independent State'";
    if($codeList)
      $q .= " AND \"ISO3166-12LetterCode\" IN ('".implode("','",$codeList)."')";
    $out = [];
    $r = $this->db->query($q);
    while($row = $r->fetchArray(SQLITE3_ASSOC)) {
      $out[] = $o = new Country();
      foreach(['name', 'currency','code','phonePrefix'] as $k)
        $o->$k = $row[$k];
    }
    return $out;
  }

  /**
   * List all subdivisions in country
   * @param Country $country
   * @return SubDivision[] List of Subdivisions that match country
   */
  function subdivByCountry(Country $country) {
    $out = [];
    $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}'");
    while($row = $r->fetchArray(SQLITE_ASSOC)) {
      $out[] = new SubDivision($country, $row['divcode'], $row['divname']);
    }
    return $out;
  }

  /**
   * Get one subdivision by code (Exception on not found)
   * @param Country $country
   * @param $code Country SubDivision (state) code, eg VIC for Victoria Australia, or LA for Los Angeles, USA
   * @return SubDivision
   * @throws \Exception
   */
  function SubDivByID(Country $country, $code) {
    $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}' AND divname='{$code}'");
    $row = $r->fetchArray(SQLITE_ASSOC);
    if(!$row) throw new \Exception("SubDivision could not be found");
    return new SubDivision($country, $row['divcode'], $row['divname']);
  }

  /**
   * Return all stats for Country
   * @param Country $country
   * @return array Assoc Array [ID] => State Name
   */
  function getAllStates(Country $country) {
    $out = [];
    $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}'");
    while($row = $r->fetchArray(SQLITE3_ASSOC)) {
      $out[$row['divcode']] = $row['divname'];
    }
    return $out;
  }

}