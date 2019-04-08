<?php
/**
 * Created by PhpStorm.
 * User: ktulinger
 * Date: 2019-04-08
 * Time: 17:37
 */

namespace Tulinkry\Script\Services;


use Nette\InvalidArgumentException;
use ReflectionClass;
use Tulinkry\Script\Entity\Script;

abstract class AbstractScriptProvider implements ScriptProvider
{

    abstract public function addScript(Script $script);

    abstract public function getFiles($tags = NULL);

    abstract public function getInlines($tags = NULL);

    protected function addEntity(Script $script, &$collection)
    {
        if (count($script->getTags())) {
            foreach ($script->getTags() as $tag) {
                if (!isset($collection[$tag])) {
                    $collection[$tag] = self::createEmptyData();
                }

                if ($script->getUniqueKey() !== null) {
                    $collection[$tag]->uniques[$script->getUniqueKey()] = $script;
                } else {
                    $collection[$tag]->entities[] = $script;
                }
            }
        }

        if ($script->getUniqueKey() !== null) {
            $collection[NULL]->uniques[$script->getUniqueKey()] = $script;
        } else {
            $collection[NULL]->entities[] = $script;
        }

        //\Tracy\Debugger::$maxDepth = 10;
        //dump($this);

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

        $rc = new ReflectionClass($class);
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
        return isset($collection[$tag]) ? array_merge($collection[$tag]->entities, $collection[$tag]->uniques) : array();
    }

    protected function getByTags($tags, $collection)
    {
        if (count($tags) === 0) {
            return array_merge($collection[NULL]->entities, $collection[NULL]->uniques);
        }

        $ret = array();
        foreach ($tags as $tag) {
            $ret = array_merge($ret, $this->getByTag($tag, $collection));
        }

        return $ret;
    }

    protected static function createEmptyData()
    {
        $scriptsCollection = new \StdClass;
        $scriptsCollection->entities = array();
        $scriptsCollection->uniques = array();
        return $scriptsCollection;
    }
}