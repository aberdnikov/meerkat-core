<?php

namespace Meerkat\Twig\Node;

use Meerkat\StaticFiles\Css as Css;

defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a trans node.
 *
 * Based off of the core twig code for the trans tag, but modified
 * to use Kohana's __() function.
 * 
 * @package kohana-twig
 */
class Twig_Css_Node extends \Twig_Node {

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
        $msg = trim($msg);
        Css::instance()->add_inline($msg, $msg);
    }

}
