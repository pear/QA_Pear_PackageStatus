<?php
require_once 'QA/Pear/PackageStatus/PackageXmlFinder.php';

foreach (QA_Pear_PackageStatus_PackageXmlFinder::findPackageXmlFiles(
    '/data/cvs/pear/pear/'
    )
    as $strPackageName => $strPackageFile)
{
    echo str_pad($strPackageName, 20, ' ') . ' ' . $strPackageFile . "\n";
}
?>