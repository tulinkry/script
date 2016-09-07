<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class InlineJsScript extends \Tulinkry\Script\Entity\Script
{

    public function getHtml () {
        return Html::el( "script" ) -> type( "text/javascript" ) -> setHtml(
                        "\n//<![CDATA[\n"
                        . $this -> source
                        . "\n//]]>\n" );
    }

}
