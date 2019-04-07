<?php

namespace Tulinkry\Script\Entity;

abstract class FileScript extends \Tulinkry\Script\Entity\Script
{

    public function __construct($attrs)
    {
        $url = null;
        if (isset($attrs['url'])) {
            $url = $attrs['url'];
            unset($attrs['url']);
        } else if (isset($attrs['source'])) {
            $url = $attrs['source'];
            unset($attrs['source']);
        } else if (isset($attrs['file'])) {
            $url = $attrs['file'];
            unset($attrs['file']);
        } else if (is_array($attrs)) {
            $url = array_shift($attrs);
        } else {
            $url = $attrs;
            $attrs = array();
        }
        parent::__construct($url, $attrs);
    }

    public function getRaw()
    {
        return '';
    }

}
