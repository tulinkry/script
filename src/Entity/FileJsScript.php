<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class FileJsScript extends \Tulinkry\Script\Entity\Script
{

    public function getHtml () {
        return Html::el( "script" ) -> type( "text/javascript" ) -> src( $this -> source );
    }

}
