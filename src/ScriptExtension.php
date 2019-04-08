<?php

namespace Tulinkry\DI;

use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;

class ScriptExtension extends CompilerExtension
{

    public $defaults = array(
        'dir' => 'generated/js'
    );

    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        Validators::assertField($config, 'dir', 'string:1..', 'configuration of \'%s\' in the script extension');

        $builder->addDefinition($this->prefix("scripts"))
            ->setClass("Tulinkry\Script\Services\ScriptService")
            ->setInject(true);

        $builder->addDefinition($this->prefix("styles"))
            ->setClass("Tulinkry\Script\Services\StyleService")
            ->setInject(true);

        $builder->addDefinition($this->prefix("url"))
            ->setClass("Tulinkry\Script\Services\UrlService")
            ->addSetup("setOutputDirectory", [$config['dir']])
            ->addSetup("setWwwDirectory", [$builder->parameters['wwwDir']])
            ->setInject(true);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $latte = $builder->getByType('Nette\Bridges\ApplicationLatte\ILatteFactory') ?: 'nette.latteFactory';
        if ($builder->hasDefinition($latte)) {
            $builder->getDefinition($latte)
                ->addSetup('?->onCompile[] = function($engine) { '
                    . '\Tulinkry\Script\Macros\ScriptMacros::install($engine->getCompiler()); }', ['@self']);
        }

        if ($builder->hasDefinition('nette.latte')) {
            $builder->getDefinition('nette.latte')
                ->addSetup('?->onCompile[] = function($engine) { '
                    . '\Tulinkry\Script\Macros\ScriptMacros::install($engine->getCompiler()); }', ['@self']);
        }
    }

}
