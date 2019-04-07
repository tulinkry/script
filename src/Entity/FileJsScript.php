<?php

namespace Tulinkry\Script\Entity;

use Nette\Utils\Html;

class FileJsScript extends \Tulinkry\Script\Entity\FileScript
{

    public function getHtml()
    {
        $source = $this->source . '?t=' . time();
        return Html::el("script")->type("text/javascript")->src($source);
    }

}
