<?php

namespace Meerkat\Twig\Node;

defined('SYSPATH') or die('No direct script access.');

use Meerkat\StaticFiles\Js;

class JsOnload extends \Twig_Node {

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
            Js::instance()->add_onload($msg, $msg);
        } else {
//            var_dump($node);
            $msg = $node->getAttribute('data');
            Js::instance()->add_onload($msg, $msg);
        }
        //      $msg = $node->getAttribute('value');
    }

}
