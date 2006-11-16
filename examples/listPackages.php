<?php
require_once 'QA/Pear/PackageStatus/PackageXmlFinder.php';

if ($argc < 2) {
    echo <<<EOD
Lists all packages found.
Usage:
    php listPackages.php path/to/pearcvs/

EOD;
    exit(1);
}

$strPearDir     = $argv[1];

foreach (QA_Pear_PackageStatus_PackageXmlFinder::findPackageXmlFiles(
    $strPearDir
    )
    as $strPackageName => $strPackageFile)
{
    echo str_pad($strPackageName, 20, ' ') . ' ' . $strPackageFile . "\n";
}
?>