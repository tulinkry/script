<?php

namespace Tulinkry\Script\Services;

use Tulinkry\Script\Entity\InlineScript;
use Tulinkry\Script\Entity\Script;

class ScriptService extends AbstractScriptProvider
{
    protected $inlineScripts;
    protected $fileScripts;

    /**
     * ScriptService constructor.
     */
    public function __construct()
    {
        $this->inlineScripts = array(NULL => self::createEmptyData());
        $this->fileScripts = array(NULL => self::createEmptyData());
    }

    public function addScript(Script $script)
    {
        if ($script instanceof InlineScript) {
            return $this->addEntity($script, $this->inlineScripts);
        }
        return $this->addEntity($script, $this->fileScripts);
    }

    public function getFiles($tags = NULL)
    {
        return $this->getByTag($tags, $this->fileScripts);
    }

    public function getInlines($tags = NULL)
    {
        return $this->getByTag($tags, $this->inlineScripts);
    }
}
