<?php

namespace Tulinkry\Script\Services;

use Nette\Http\IRequest;
use Tulinkry\Script\Entity\FileJsScript;
use Tulinkry\Script\Entity\FileScript;
use Tulinkry\Script\Entity\InlineScript;

class UrlService
{
    const DEFAULT_TAG = "all-together";

    protected $wwwDir;
    protected $directory;

    /**
     * @var ScriptService @inject
     */
    public $scripts;

    /**
     * @var StyleService @inject
     */
    public $styles;

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

    public function getScriptElements($tags)
    {
        return $this->prepareElements($tags, 'scripts', '.js');
    }

    public function getStyleElements($tags)
    {
        return $this->prepareElements($tags, 'styles', '.css');
    }

    /**
     * Sort the input scripts by a priority.
     * @param $scripts array of scripts
     * @return array of scripts
     */
    protected function sortScripts(&$scripts)
    {
        uasort($scripts, function ($a, $b) {
            return $a->getPriority() - $b->getPriority();
        });
        return $scripts;
    }

    /**
     * Prepare the elements in an array for all script of single type.
     *
     * @param $tags filter the scripts by tag before returning
     * @param $type use for this type (either scripts or styles)
     * @param string $suffix the file suffix
     * @return array of scripts/styles sorted based on priority
     */
    protected function prepareElements($tags, $type, $suffix = '.js')
    {
        $results = [];

        $tags = is_array($tags) ? $tags : array($tags);

        if (count($tags) === 0) {
            $tags = array(self::DEFAULT_TAG);
        }

        sort($tags);

        switch ($type) {
            case 'scripts':
                $elements = $this->scripts->getFiles();
                $elements += $this->scripts->getInlines();
                break;
            case 'styles':
                $elements = $this->styles->getFiles();
                $elements += $this->styles->getInlines();
                break;
        }

        foreach ($elements as $element) {
            if ($element instanceof FileScript) {
                $results[] = $element;
            }
        }

        $data = [];
        foreach ($elements as $element) {
            if ($element instanceof InlineScript) {
                $data[] = $element->getRaw();
            }
        }

        if (($generatedFile = $this->generateDataFile($data, $tags, $suffix)) !== null) {
            $results[] = $generatedFile;
        }

        return $this->sortScripts($results);
    }

    /**
     * Generates data file from the data be joining them and creating a file in the www directory.
     *
     * @param $data array of inline javascript snippets
     * @param $tags tags which were used to obtain such data
     * @param string $suffix the file suffix
     * @return FileJsScript|null the new instance of file js script or null of can not be created
     */
    protected function generateDataFile($data, $tags, $suffix = '.js')
    {
        @mkdir($this->wwwDir . DIRECTORY_SEPARATOR . $this->directory, 0775, true);

        $data = implode(PHP_EOL . PHP_EOL . "// " . str_repeat('-', 100) . PHP_EOL . PHP_EOL, $data);

        if (trim($data) === '') {
            return NULL;
        }

        $tmpName = implode("-", $tags) . $suffix;
        file_put_contents($this->wwwDir . DIRECTORY_SEPARATOR . $this->directory . DIRECTORY_SEPARATOR . $tmpName, $data);

        return new FileJsScript(array('url' => $this->getBasePath() . '/' . $this->directory . '/' . $tmpName . '?t=' . time(), 'priority' => PHP_INT_MAX));
    }
}
