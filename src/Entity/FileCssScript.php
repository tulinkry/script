<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class FileCssScript extends \Tulinkry\Script\Entity\FileScript
{

    public function getHtml()
    {
        $source = $this->source;
        return Html::el("link")
            ->rel('stylesheet')
            ->type("text/css")
            ->data('priority', $this->getPriority())
            ->href($source);
    }

}
