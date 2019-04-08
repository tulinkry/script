<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class FileJsScript extends \Tulinkry\Script\Entity\FileScript
{

    public function getHtml()
    {
        $source = $this->source;
        return Html::el("script")
            ->type("text/javascript")
            ->data('priority', $this->getPriority())
            ->src($source);
    }

}
