<?php

/**
*   Finds package.xml files in directories
*/
class QA_Pear_PackageStatus_PackageXmlFinder
{
    /**
    *   Finds package.xml files in the given PEAR
    *   cvs root directory
    *
    *   @param string   PEAR cvs checkout root directory
    *   @return array   array with Package name => package.xml paths
    */
    public static function findPackageXmlFiles($strPearCvsDirectory)
    {
        $ar = glob($strPearCvsDirectory . '/*/package{2,}.xml', GLOB_BRACE);
        $arFiles = array();
        foreach ($ar as $strFile) {
            $strPackageName = self::guessPackageNameFromXml($strFile);
            if (isset($arFiles[$strPackageName])) {
                continue;
            }
            $arFiles[$strPackageName] = $strFile;
        }
        return $arFiles;
    }//public static function findPackageXmlFiles($strPearCvsDirectory)



    public static function guessPackageNameFromXml($strPackageFile)
    {
        $s = file_get_contents($strPackageFile);
        $nBegin = strpos($s, '<name>');
        $nEnd = strpos($s, '</name>');
        return substr($s, $nBegin + 6, $nEnd - $nBegin - 6);
    }//public static guessPackageNameFromXml($strPackageFile)

}//class QA_Pear_PackageStatus_PackageXmlFinder
?>