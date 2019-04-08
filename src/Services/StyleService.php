<?php

namespace Tulinkry\Script\Services;

use Tulinkry\Script\Entity\InlineScript;
use Tulinkry\Script\Entity\Script;

class StyleService extends AbstractScriptProvider
{
    protected $fileStyles;
    protected $inlineStyles;

    /**
     * StylesService constructor.
     */
    public function __construct()
    {
        $this->fileStyles = array(NULL => self::createEmptyData());
        $this->inlineStyles = array(NULL => self::createEmptyData());
    }

    public function addScript(Script $script)
    {
        if ($script instanceof InlineScript) {
            return $this->addEntity($script, $this->inlineStyles);
        }
        return $this->addEntity($script, $this->fileStyles);
    }

    public function getFiles($tags = NULL)
    {
        return $this->getByTag($tags, $this->fileStyles);
    }

    public function getInlines($tags = NULL)
    {
        return $this->getByTag($tags, $this->inlineStyles);
    }
}
