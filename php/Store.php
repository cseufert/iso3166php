<?php

namespace Seufert\ISO3166;

/**
 * Class Store
 * @package Seufert\ISO3166
 * @author Chris Seufert <chris@modd.com.au>
 */
class Store
{

    /** @var \SQLite3 Database containing Country Data */
    public $db;

    private ?\SQLite3Stmt $qById = null;

    function __construct()
    {
        $dbPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'iso-3166.sqlite3']);
        $this->db = new \SQLite3($dbPath, SQLITE3_OPEN_READONLY);
    }

    /**
     * @param $code 2 Letter ISO3166 country code (CAPS)
     * @return Country
     * @throws NotFoundEx
     */
    function countryByID(string $code): Country
    {
        $this->qById ??= $this->db->prepare("SELECT CommonName as name, ISO4217CurrencyCode as currency, \"ITU-TTelephoneCode\" as phonePrefix, \"ISO3166-12LetterCode\" as code FROM country " .
            "WHERE Type = 'Independent State' AND  \"ISO3166-12LetterCode\" = :code");
        $this->qById->bindValue(':code', $code, SQLITE3_TEXT);
        $row = $this->qById->execute()->fetchArray(SQLITE3_ASSOC);
        if (!$row)
            throw new NotFoundEx("Country with code $code could not be found");
        $o = new Country();
        foreach (['name', 'currency', 'code', 'phonePrefix'] as $k)
            $o->$k = $row[$k];
        return $o;
    }

    private ?\SQLite3Stmt $qList = null;

    /**
     * @param string[] $codeList List of Country codes to return in list (if none, return all) Always all CAPS
     * @return Country[] List of Countries
     */
    function listByCode(array $codeList = []): array
    {
        if ($codeList) {
            $q =  "SELECT CommonName as name, ISO4217CurrencyCode as currency, \"ITU-TTelephoneCode\" as phonePrefix, \"ISO3166-12LetterCode\" as code FROM country";
            $q .= " WHERE Type = 'Independent State' AND \"ISO3166-12LetterCode\" IN ('" . implode("','", $codeList) . "')";
            $r = $this->db->query($q);
        } else {
            $this->qList ??= $this->db->prepare(
                "SELECT CommonName as name, ISO4217CurrencyCode as currency, \"ITU-TTelephoneCode\" as phonePrefix, \"ISO3166-12LetterCode\" as code FROM country WHERE Type = 'Independent State'",
            );
            $r = $this->qList->execute();
        }
        $out = [];
        while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
            $out[] = $o = new Country();
            foreach (['name', 'currency', 'code', 'phonePrefix'] as $k)
                $o->$k = $row[$k];
        }
        return $out;
    }

    /**
     * List all subdivisions in country
     * @param Country $country
     * @return SubDivision[] List of Subdivisions that match country
     */
    function subDivByCountry(Country $country):array
    {
        $out = [];
        $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}' and type!='Outlying area'");
        while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
            $out[] = new SubDivision($country, $row['divcode'], $row['divname']);
        }
        return $out;
    }

    /**
     * Get one subdivision by code (Exception on not found)
     * @param Country $country
     * @param string $code Country SubDivision (state) code, eg VIC for Victoria Australia, or CA for California, USA
     * @return SubDivision
     * @throws NotFoundEx
     */
    function subDivByID(Country $country, string $code):SubDivision
    {
        $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}' AND divcode='{$code}'");
        $row = $r->fetchArray(SQLITE3_ASSOC);
        if (!$row) throw new NotFoundEx("SubDivision could not be found ({$country->code}-{$code})");
        return new SubDivision($country, $row['divcode'], $row['divname']);
    }

    /**
     * Return all stats for Country
     * @param Country $country
     * @return array<string,string> Assoc Array [ID] => State Name
     */
    function getAllStates(Country $country):array
    {
        $out = [];
        $r = $this->db->query("SELECT divcode, divname FROM country_state WHERE country = '{$country->code}'");
        while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
            $out[$row['divcode']] = $row['divname'];
        }
        return $out;
    }

}
