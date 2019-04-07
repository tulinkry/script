<?php

namespace Tulinkry\Script\Services;

use Nette\InvalidArgumentException;
use Tulinkry\Script\Entity\Script;

class ScriptService
{

    protected $scripts = array(
        NULL => array() // all scripts
    );
    protected $styles = array(
        NULL => array() // all styles
    );

    public function addScript(Script $script)
    {
        return $this->addEntity($script, $this->scripts);
    }

    public function addStyle(Script $script)
    {
        return $this->addEntity($script, $this->styles);
    }

    public function getAllScripts()
    {
        return $this->scripts[NULL];
    }

    public function getAllStyles()
    {
        return $this->styles[NULL];
    }

    public function getScriptsByTag($tag)
    {
        return $this->getByTag($tag, $this->scripts);
    }

    public function getStylesByTag($tag)
    {
        return $this->getByTag($tag, $this->styles);
    }

    public function getScriptsByTags($tags)
    {
        return $this->getByTags($tags, $this->scripts);
    }

    public function getStylesByTags($tags)
    {
        return $this->getByTags($tags, $this->styles);
    }

    protected function addEntity(Script $script, &$collection)
    {
        if (count($script->getTags())) {
            foreach ($script->getTags() as $tag) {
                if (!isset($collection[$tag])) {
                    $collection[$tag] = array();
                }
                $collection[$tag][] = $script;
            }
        }
        $collection[NULL][] = $script;
        return $this;
    }

    public function createElement()
    {
        $args = func_get_args();
        $name = array_shift($args);

        $class = 'Tulinkry\\Script\Entity\\' . ucfirst($name) . "Script";

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class $class not found.");
        }

        $rc = new \ReflectionClass($class);
        if (!$rc->isInstantiable()) {
            throw new InvalidArgumentException("Class $class is not instantiable.");
        } elseif ($constructor = $rc->getConstructor()) {
            return $rc->newInstanceArgs($args);
        } elseif ($args) {
            throw new InvalidArgumentException("No suitable constructor for class $class was found.");
        }
        return new $class;
    }

    protected function getByTag($tag, $collection)
    {
        return isset($collection[$tag]) ? $collection[$tag] : array();
    }

    protected function getByTags($tags, $collection)
    {
        if (count($tags) === 0) {
            return $collection[NULL];
        }
        $ret = array();
        foreach ($tags as $tag) {
            $ret = array_merge($ret, $this->getByTag($tag, $collection));
        }
        return $ret;
    }

}
