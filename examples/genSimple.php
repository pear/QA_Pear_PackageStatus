<?php
require_once 'QA/Pear/PackageStatus.php';
require_once 'QA/Pear/PackageStatus/Renderer/Simple.php';

$ps = new QA_Pear_PackageStatus('/data/cvs/pear/pear/');
$ps->gatherStats();
$rs = new QA_Pear_PackageStatus_Renderer_Simple();
echo $rs->render($ps->getStats());
?>