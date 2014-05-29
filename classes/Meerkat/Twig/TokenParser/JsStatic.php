<?php

namespace Meerkat\Twig\TokenParser;

/**
 * Token parser for the trans tag.
 * 
 * Both block styles are allowed:
 * 
 *     {% js %}http://site.ru/src.js{% endjs %}
 * 
 * The body of the tag will be trim()ed before being passed to __().
 * 
 * @package kohana-twig
 */
class JsStatic extends \Twig_TokenParser {

    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token) {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        // Allow passing only an expression without an endblock
        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            $body = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse(array($this, 'decideForEnd'), true);
        }
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        // Pass it off to the compiler
        return new \Meerkat\Twig\Node\JsStatic(array('body' => $body), array(), $lineno, $this->getTag());
    }

    /**
     * Tests for the endtrans block
     *
     * @return  boolean
     */
    public function decideForEnd($token) {
        return $token->test('endjs');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag() {
        return 'js';
    }

}
