<?php
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


class QA_Pear_PackageStatus_Renderer_Simple
{
    var $options = array();
    var $options['jsDirectory'] = null;

    public function render($arStats)
    {
        $page = new HTML_Page2();
        $page->setTitle('PEAR package.xml analysis');
        $page->setMetaData('author', __CLASS__);
        $css = <<<EOD
.bad {
    background-color: red;
}
.yellow {
    background-color: yellow;
}
.ok {
    background-color: #0F0;
}
EOD;
        $page->addStyleDeclaration($css);
        if ($this->options['jsDirectory'] !== null) {
            $page->addscript("{$this->options['jsDirectory']}/sorttable.js");
        }

        $table = new HTML_Table(array('border' => '1', 'class' => 'sortable'));
        $table->getHeader()->addRow(array('Package', 'package.xml version', 'Version missing', 'Stability', 'Last release', 'Time since last release'), null, 'th');

        $nCountPackages         = count($arStats);
        $nCountOneDotZero       = 0;
        $nCountBadTime          = 0;
        $nCountNoVersion        = 0;
        $nCountNoProblems       = 0;
        $nCountPackageXmlBroken = 0;

        $body = $table->getBody();
        foreach ($arStats as $strPackage => $pack) {
            if (!isset($pack['packageXmlVersion'])) {
                ++$nCountPackageXmlBroken;
                $row = $body->addRow(array(
                    self::getPackageLink($strPackage),
                    'package.xml broken'
                ));
                $body->setCellAttributes($row, 1, array('class' => 'bad', 'colspan' => 5));
            } else {
                $bProblems = false;
                $row = $body->addRow(array(
                    self::getPackageLink($strPackage),
                    $pack['packageXmlVersion'],
                    $pack['packageXmlVersionGuessed'] ? 'yes' : 'no',
                    $pack['releaseStatus'],
                    $pack['releaseDate'],
                    self::getStringFromTimeDiff($pack['dateDiff'])
                ));

                if ($pack['packageXmlVersion'] != '2.0') {
                    $body->setCellAttributes($row, 1, array('class' => 'yellow'));
                    ++$nCountOneDotZero;
                    $bProblems = true;
                }
                if ($pack['packageXmlVersionGuessed']) {
                    ++$nCountNoVersion;
                    $body->setCellAttributes($row, 2, array('class' => 'bad'));
                    $bProblems = true;
                }
                if ($pack['badTime']) {
                    ++$nCountBadTime;
                    $body->setCellAttributes($row, 5, array('class' => 'bad'));
                    $bProblems = true;
                }

                if (!$bProblems) {
                    ++$nCountNoProblems;
                    $body->setCellAttributes($row, 0, array('class' => 'ok'));
                }
            }
        }

        $page->addBodyContent($table->toHtml());


        $page->addBodyContent('<a name="statistics"></a>');
        $statTable = new HTML_Table(array('border' => 1));
        $statTable->setCaption('Statistics about ' . $nCountPackages . ' packages');
        $statTable->getHeader()->addRow(array('Description', 'Bad packages', 'Correct ones', 'Percentage'), null, 'th');
        $body = $statTable->getBody();

        $flBroken = $nCountPackageXmlBroken / $nCountPackages;
        $body->addRow(array(
            'Broken package.xml',
            $nCountPackageXmlBroken,
            $nCountPackages - $nCountPackageXmlBroken,
            number_format(100 * $flBroken , 2) . '%'
            ), array(
            'style' => 'background-color:' . self::getColor(1 - ($nCountPackageXmlBroken > 0))
        ));

        $flPackagev1 = $nCountOneDotZero / $nCountPackages;
        $body->addRow(array(
            'Still using package.xml v1.0',
            $nCountOneDotZero,
            $nCountPackages - $nCountOneDotZero,
            number_format(100 * $flPackagev1 , 2) . '%'
            ), array(
            'style' => 'background-color:' . self::getColor(1 - $flPackagev1)
        ));

        $flNoVersion = $nCountNoVersion / $nCountPackages;
        $body->addRow(array(
            'No version in package.xml',
            $nCountNoVersion,
            $nCountPackages - $nCountNoVersion,
            number_format(100 * $flNoVersion, 2) . '%'
            ), array(
            'style' => 'background-color:' . self::getColor(1 - $flNoVersion)
        ));

        $flTooLongUnstable = $nCountBadTime / $nCountPackages;
        $body->addRow(array(
            'Too long unstable',
            $nCountBadTime,
            $nCountPackages - $nCountBadTime,
            number_format(100 * $flTooLongUnstable, 2) . '%'
            ), array(
            'style' => 'background-color:' . self::getColor($flTooLongUnstable)
        ));

        $flAllOk = $nCountNoProblems / $nCountPackages;
        $body->addRow(array(
            'All ok',
            $nCountNoProblems,
            $nCountPackages - $nCountNoProblems,
            number_format(100 * $flAllOk, 2) . '%'
            ), array(
            'style' => 'background-color:' . self::getColor($flAllOk)
        ));

        $page->addBodyContent($statTable->toHtml());


        return $page->toHtml();
    }//public function render($arStats)



    public static function getPackageLink($strPackage)
    {
        return '<a name="' . $strPackage . '" href="http://pear.php.net/package/' . $strPackage . '">' . $strPackage . '</a>';
    }//public static function getPackageLink($strPackage)

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public static function getStringFromTimeDiff($nSeconds)
    {
        $nMinutes = intval($nSeconds / 60);
        $nHours   = intval($nSeconds / 3600);
        $nDays    = intval($nSeconds / 86400);
        $nWeeks   = intval($nSeconds / (86400 * 7));
        $nYears   = intval($nSeconds / (86400 * 365));
        if ($nSeconds < 60) {
            return $nSeconds . ' seconds';
        } else if ($nMinutes < 60) {
            return $nMinutes . ' minutes, ' . $nSeconds % 60 . ' seconds';
        } else if ($nHours < 24) {
            return $nHours . ' hours, ' . $nMinutes % 60 . ' minutes';
        } else if ($nDays < 7) {
            return $nDays . ' days, ' . $nHours % 24 . ' hours';
        } else if ($nWeeks < 52) {
            return $nWeeks . ' weeks, ' . $nDays % 7 . ' days';
        } else {
            return $nYears . ' years, ' . $nWeeks % 7 . ' weeks';
        }
    }//public static function getStringFromTimeDiff($nSeconds)



    /**
    *   Returns the color code matching the number.
    *
    *   @param float    $flNumber   Number (x/y), !no! percentage
    *   @return string  HTML color #0AF
    */
    public static function getColor($flNumber)
    {
        if ($flNumber == 1) {
            return '#0F0';
        } else if ($flNumber >= 0.9) {
            return '#dfff00';
        } else if ($flNumber >= 0.6) {
            return '#FF0';
        } else if ($flNumber >= 0.3) {
            return '#F70';
        } else {
            return '#F00';
        }
    }//public static function getColor($flNumber)

}//class QA_Pear_PackageStatus_Renderer_Simple
?>
