<?php
/**
 * Created by PhpStorm.
 * User: ktulinger
 * Date: 2019-04-08
 * Time: 17:36
 */

namespace Tulinkry\Script\Services;

use Tulinkry\Script\Entity\Script;

interface ScriptProvider
{
    public function addScript(Script $script);

    public function getFiles($tags = NULL);

    public function getInlines($tags = NULL);

    public function createElement();
}