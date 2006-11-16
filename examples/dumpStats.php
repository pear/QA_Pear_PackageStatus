<?php
require_once 'QA/Pear/PackageStatus.php';

$ps = new QA_Pear_PackageStatus('/data/cvs/pear/pear/');
$ps->gatherStats();
var_dump($ps->getStats());
?>