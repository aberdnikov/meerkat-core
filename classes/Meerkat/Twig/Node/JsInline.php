<?php

namespace Meerkat\Twig\Node;

use Meerkat\StaticFiles\Js;


defined('SYSPATH') or die('No direct script access.');

class JsInline extends \Twig_Node {

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler) {

        $compiler->addDebugInfo($this);
        $node = $this->getNode('body');
        if ($node instanceof \Twig_Node_Expression_Constant) {
            $msg = $node->getAttribute('value');
        } else {
            $msg = $node->getAttribute('data');
        }
        Js::instance()->add_inline($msg, $msg);
    }

}
