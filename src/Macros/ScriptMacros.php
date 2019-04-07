<?php

namespace Tulinkry\Script\Macros;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

/**
 * Basic macros for Latte.
 *
 */
class ScriptMacros extends MacroSet
{

    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);

        $me->addMacro('script', array($me, 'macroScript'), array($me, 'macroScript'));
        $me->addMacro('style', array($me, 'macroStyle'), array($me, 'macroStyle'));
        $me->addMacro('fileScript', array($me, 'macroFileScript'));
        $me->addMacro('fileStyle', array($me, 'macroFileStyle'));
        $me->addMacro('scripts', array($me, 'macroScripts'));
        $me->addMacro('styles', array($me, 'macroStyles'));
    }

    /**
     * {script $tags, $priority}
     */
    public function macroScript(MacroNode $node, PhpWriter $writer)
    {
        if ($node->closing) {
            $this->getCompiler()->setContext(NULL);
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
                . '$_l->tmp->addScript($_l->tmp->createElement("inlineJs", %modify(ob_get_clean()), %node.array)); '
                . '}');
        } else {
            $this->getCompiler()->setContext(Compiler::CONTENT_JS);
            return 'ob_start(function () {})';
        }
    }

    /**
     * {style $tags, $priority}
     */
    public function macroStyle(MacroNode $node, PhpWriter $writer)
    {
        if ($node->closing) {
            $this->getCompiler()->setContext(NULL);
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
                . '$_l->tmp->addStyle($_l->tmp->createElement("inlineCss", %modify(ob_get_clean()), %node.array)); '
                . '}');
        } else {
            $this->getCompiler()->setContext(Compiler::CONTENT_CSS);
            return 'ob_start(function () {})';
        }
    }

    /**
     * {fileScript $path}
     */
    public function macroFileScript(MacroNode $node, PhpWriter $writer)
    {
        if ($node->args === '') {
            throw new CompileException('Missing arguments in {fileScript} macro.');
        }

        return $writer->write(''
            . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
            . 'echo $_l->tmp->createElement("fileJs", %node.array)->getHtml()->render(); '
            . '}');
    }

    /**
     * {fileStyle $path}
     */
    public function macroFileStyle(MacroNode $node, PhpWriter $writer)
    {
        if ($node->args === '') {
            throw new CompileException('Missing arguments in {fileScript} macro.');
        }

        return $writer->write(''
            . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
            . 'echo $_l->tmp->createElement("fileCss", %node.array)->getHtml()->render(); '
            . '}');
    }

    /**
     * {scripts $tags}
     */
    public function macroScripts(MacroNode $node, PhpWriter $writer)
    {
        $inline = preg_match("/\|inline/", $node->modifiers);
        $node->modifiers = preg_replace("/\|inline/", "", $node->modifiers);

        if ($inline) {
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
                . '$_l->tmp = $_l->tmp->getScriptsByTags(%node.array); '
                . 'foreach($_l->tmp as $script) { '
                . 'echo $script->getHtml()->render(); '
                . 'echo "\n"; '
                . '} '
                . '}');
        } else {
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\UrlService")) { '
                . 'if($_l->tmp = $_l->tmp->getScriptElement(%node.array)) { '
                . 'echo $_l->tmp->getHtml()->render(); '
                . '} '
                . '}');
        }
    }

    /**
     * {styles $tags}
     */
    public function macroStyles(MacroNode $node, PhpWriter $writer)
    {
        $inline = preg_match("/\|inline/", $node->modifiers);
        $node->modifiers = preg_replace("/\|inline/", "", $node->modifiers);

        if ($inline) {
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\ScriptService")) { '
                . '$_l->tmp = $_l->tmp->getStylesByTags(%node.array); '
                . 'foreach($_l->tmp as $style) { '
                . 'echo $style->getHtml()->render(); '
                . 'echo "\n"; '
                . '} '
                . '}');
        } else {
            return $writer->write(''
                . 'if($_l->tmp = $presenter->context->getByType("Tulinkry\Script\Services\UrlService")) { '
                . 'if($_l->tmp = $_l->tmp->getStyleElement(%node.array)) { '
                . 'echo $_l->tmp->getHtml()->render(); '
                . '}'
                . '}');
        }
    }

}
