<?php
require_once 'QA/Pear/PackageStatus.php';
require_once 'QA/Pear/PackageStatus/Renderer/Simple.php';

if ($argc < 2) {
    echo <<<EOD
Generates simple PEAR package stats.
Usage:
    php genSimple.php path/to/pearcvs/

EOD;
    exit(1);
}

$strPearDir     = $argv[1];

$ps = new QA_Pear_PackageStatus($strPearDir);
$ps->gatherStats();
$rs = new QA_Pear_PackageStatus_Renderer_Simple();
echo $rs->render($ps->getStats());
?>