<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a debug node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class Twig_Node_Debug extends Twig_Node
{
    public function __construct(Twig_Node_Expression $expr = null, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
    }

    public function compile($compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write("if (\$this->env->isDebug()) {\n")
            ->indent()
            ->write('var_export(')
        ;

        if (null === $this->expr) {
            $compiler->raw('$context');
        } else {
            $compiler->subcompile($this->expr);
        }

        $compiler
            ->raw(");\n")
            ->outdent()
            ->write("}\n")
        ;
    }
}
