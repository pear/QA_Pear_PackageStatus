<?php
require_once 'QA/Pear/PackageStatus.php';

if ($argc < 2) {
    echo <<<EOD
Dumps statistics to the console.
Usage:
    php dumpStats.php path/to/pearcvs/

EOD;
    exit(1);
}

$strPearDir     = $argv[1];

$ps = new QA_Pear_PackageStatus($strPearDir);
$ps->gatherStats();
var_dump($ps->getStats());
?>