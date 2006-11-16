<?php
require_once 'QA/Pear/PackageStatus/PackageXmlFinder.php';

/**
*   Displays information about PEAR packages
*   like package.xml version, time since last stable
*   release or since the last release at all.
*/
class QA_Pear_PackageStatus
{
    /**
    *   Array with package name => package.xml file
    */
    protected $arFiles = array();



    /**
    *   Array with stats about the packages
    */
    protected $arStats = array();



    public function __construct($strPearCvsDirectory)
    {
        $this->arFiles = QA_Pear_PackageStatus_PackageXmlFinder::findPackageXmlFiles($strPearCvsDirectory);
    }//public function __construct($strPearCvsDirectory)



    public function getStats()
    {
        return $this->arStats;
    }//public getStats()



    public function gatherStats()
    {
        foreach ($this->arFiles as $strPackage => $strFile) {
            $pack =& $this->arStats[$strPackage];
            $pack['file']   = $strFile;

            $doc = @simplexml_load_file($strFile);
            if ($doc === false) {
                //FIXME
                $pack['error']  = 'package.xml cannot be loaded';
                continue;
            }
            $pack['packageXmlVersion']  = $this->getPackageXmlVersion($doc);
            $pack['packageXmlVersionGuessed'] = $this->getPackageXmlVersionGuessed($doc);
            $pack['releaseStatus']      = $this->getReleaseStatus($doc, $pack['packageXmlVersion']);
            $pack['releaseDate']        = $this->getReleaseDate($doc, $pack['packageXmlVersion']);
            $pack['dateDiff']           = time() - strtotime($pack['releaseDate']);
            if ($pack['releaseStatus'] == 'stable') {
                $pack['badTime']        = false;
            } else {
                $nDays = $pack['dateDiff'] / 86400;
                $pack['badTime']        = ($nDays > 180)
                                        || ($pack['releaseStatus'] == 'alpha' && $nDays > 60)
                                        || ($pack['releaseStatus'] == 'devel' && $nDays > 30);
            }
        }

        ksort($this->arStats);
    }//public function gatherStats()



    protected function getPackageXmlVersion(SimpleXmlElement $doc)
    {
        if (isset($doc['version'])) {
            return (string)$doc['version'];
        } else {
            return '1.0';
        }
    }//protected function getPackageXmlVersion(SimpleXmlElement $doc)



    protected function getPackageXmlVersionGuessed(SimpleXmlElement $doc)
    {
        return !isset($doc['version']);
    }//protected function getPackageXmlVersionGuessed(SimpleXmlElement $doc)



    protected function getReleaseStatus(SimpleXmlElement $doc, $strXmlVersion)
    {
        if ($strXmlVersion == '2.0') {
            return (string)$doc->stability->release;
        } else if ($strXmlVersion == '1.0') {
            return (string)$doc->release->state;
        } else {
            throw new Exception('Unsupported package.xml version: ' . $strXmlVersion);
        }
    }//protected function getReleaseStatus(SimpleXmlElement $doc, $strXmlVersion)



    protected function getReleaseDate(SimpleXmlElement $doc, $strXmlVersion)
    {
        if ($strXmlVersion == '2.0') {
            return (string)$doc->date;
        } else if ($strXmlVersion == '1.0') {
            return (string)$doc->release->date;
        } else {
            throw new Exception('Unsupported package.xml version: ' . $strXmlVersion);
        }
    }//protected function getReleaseDate(SimpleXmlElement $doc, $strXmlVersion)

}//class QA_Pear_PackageStatus
?>