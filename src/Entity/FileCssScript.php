<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class FileCssScript extends \Tulinkry\Script\Entity\FileScript
{

    public function getHtml () {
        return Html::el( "link" ) -> rel( 'stylesheet' ) -> type( "text/css" ) -> href( $this -> source );
    }

}
