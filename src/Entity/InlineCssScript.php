<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class InlineCssScript extends \Tulinkry\Script\Entity\Script
{

    public function getHtml () {
        return Html::el( "link" ) -> rel( "stylesheet" ) -> type( 'text/css' ) -> setHtml( $this -> source );
    }

}
