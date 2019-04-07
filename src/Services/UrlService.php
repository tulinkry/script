<?php

namespace Tulinkry\Script\Services;

use Nette\Http\IRequest;
use Tulinkry\Script\Entity\FileCssScript;
use Tulinkry\Script\Entity\FileJsScript;

class UrlService
{

    const DEFAULT_TAG = "all-together";

    protected $wwwDir;
    protected $directory;

    /**
     * @var ScriptService
     * @inject
     */
    public $scripts;

    /** @var IRequest @inject */
    public $request;

    public function getOutputDirectory()
    {
        return $this->directory;
    }

    public function setOutputDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function getWwwDirectory()
    {
        return $this->wwwDir;
    }

    public function setWwwDirectory($wwwDir)
    {
        $this->wwwDir = $wwwDir;
        return $this;
    }

    public function getBasePath()
    {
        return rtrim($this->request->getUrl()->getBasePath(), '/');
    }

    public function getScriptElement($tags)
    {
        if (($path = $this->getFile($tags, array($this->scripts, 'getAllScripts'), array($this->scripts, 'getScriptsByTags'), '.js')) !== NULL)
            return new FileJsScript($path);
        return NULL;
    }

    public function getStyleElement($tags)
    {
        if (($path = $this->getFile($tags, array($this->scripts, 'getAllStyles'), array($this->scripts, 'getStylesByTags'), '.css')) !== NULL)
            return new FileCssScript($path);
        return NULL;
    }

    protected function getByTags($tagCallback, $tags)
    {
        $data = array();
        $scripts = $tagCallback($tags);
        $scripts = $this->sortScripts($scripts);
        foreach ($scripts as $script) {
            $data[] = $script->getRaw();
        }
        return $data;
    }

    protected function getAll($allCallback)
    {
        $data = array();
        $scripts = $allCallback();
        $scripts = $this->sortScripts($scripts);
        foreach ($scripts as $script) {
            $data[] = $script->getRaw();
        }
        return $data;
    }

    protected function sortScripts(&$scripts)
    {
        uasort($scripts, function ($a, $b) {
            return $a->getPriority() - $b->getPriority();
        });
        return $scripts;
    }

    protected function getFile($tags, $allCallback, $tagCallback, $suffix = '.js')
    {
        @mkdir($this->wwwDir . DIRECTORY_SEPARATOR . $this->directory, 0775, true);

        $tags = is_array($tags) ? $tags : array($tags);
        $all = count($tags) === 0;

        sort($tags);
        $data = [];

        if ($all) {
            $data = array_merge($data, $this->getAll($allCallback));
            $tags = array(self::DEFAULT_TAG);
        } else {
            $data = array_merge($data, $this->getByTags($tagCallback, $tags));
        }

        $data = implode(PHP_EOL . PHP_EOL . "// " . str_repeat('-', 100) . PHP_EOL . PHP_EOL, $data);

        if (trim($data) === '') {
            return NULL;
        }

        $tmpName = implode("-", $tags) . $suffix;
        file_put_contents($this->wwwDir . DIRECTORY_SEPARATOR . $this->directory . DIRECTORY_SEPARATOR . $tmpName, $data);

        return $this->getBasePath() . '/' . $this->directory . '/' . $tmpName;
    }

}
