<?php

namespace Tulinkry\Script\Entity;

abstract class Script
{

    protected $source;
    protected $tags;
    protected $priority;
    protected $uniqueKey;
    protected $attrs;

    abstract public function getHtml();

    public function __construct($source, $attrs = array())
    {
        $this->source = $source;

        $this->priority = 0;
        if (isset($attrs['priority'])) {
            $this->priority = $attrs['priority'];
            unset($attrs['priority']);
        }

        $this->uniqueKey = null;
        if (isset($attrs['uniqueKey'])) {
            $this->uniqueKey = $attrs['uniqueKey'];
            unset($attrs['uniqueKey']);
        }

        $this->tags = array();
        if (isset($attrs['tags'])) {
            $this->tags = $attrs['tags'];
        } else {
            $this->tags = $attrs;
            $attrs = array();
        }

        $this->tags = is_array($this->tags) ? $this->tags : array($this->tags);
        $this->attrs = $attrs;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * @param mixed $uniqueKey
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function hasTag($tag)
    {
        return !empty($this->tags[$tag]);
    }

    public function getRaw()
    {
        return $this->source;
    }

}
