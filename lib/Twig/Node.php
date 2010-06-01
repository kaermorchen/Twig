<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a node in the AST.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class Twig_Node implements Twig_NodeInterface, ArrayAccess, Countable, Iterator
{
    protected $nodes;
    protected $attributes;
    protected $lineno;
    protected $tag;

    public function __construct(array $nodes = array(), array $attributes = array(), $lineno = 0, $tag = null)
    {
        $this->nodes = array();
        foreach ($nodes as $name => $node) {
            $this->$name = $node;
        }
        $this->attributes = $attributes;
        $this->lineno = $lineno;
        $this->tag = $tag;
    }

    public function __toString()
    {
        $attributes = array();
        foreach ($this->attributes as $name => $value) {
            $attributes[] = sprintf('%s: %s', $name, str_replace("\n", '', var_export($value, true)));
        }

        $repr = array(get_class($this).'('.implode(', ', $attributes));

        if (count($this->nodes)) {
            foreach ($this->nodes as $name => $node) {
                $len = strlen($name) + 4;
                $noderepr = array();
                foreach (explode("\n", (string) $node) as $line) {
                    $noderepr[] = str_repeat(' ', $len).$line;
                }

                $repr[] = sprintf('  %s: %s', $name, ltrim(implode("\n", $noderepr)));
            }

            $repr[] = ')';
        } else {
            $repr[0] .= ')';
        }

        return implode("\n", $repr);
    }

    public function compile($compiler)
    {
        foreach ($this->nodes as $node) {
            $node->compile($compiler);
        }
    }

    public function getLine()
    {
        return $this->lineno;
    }

    public function getNodeTag()
    {
        return $this->tag;
    }

    /**
     * Returns true if the attribute is defined.
     *
     * @param  string  The attribute name
     *
     * @return Boolean true if the attribute is defined, false otherwise
     */
    public function offsetExists($name)
    {
        return $this->attributes[$name];
    }

    /**
     * Gets an attribute.
     *
     * @param  string The attribute name
     *
     * @return mixed  The attribute value
     */
    public function offsetGet($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->attributes[$name];
    }

    /**
     * Sets an attribute.
     *
     * @param string The attribute name
     * @param mixed  The attribute value
     */
    public function offsetSet($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Removes an attribute.
     *
     * @param string The attribute name
     */
    public function offsetUnset($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * Returns true if the node with the given identifier exists.
     *
     * @param  string  The node name
     *
     * @return Boolean true if the node with the given name exists, false otherwise
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->nodes);
    }

    /**
     * Gets a node by name.
     *
     * @param  string The node name
     *
     * @return Twig_Node A Twig_Node instance
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->nodes)) {
            throw new InvalidArgumentException(sprintf('Node "%s" does not exist for Node "%s".', $name, get_class($this)));
        }

        return $this->nodes[$name];
    }

    /**
     * Sets a node.
     *
     * @param string    The node name
     * @param Twig_Node A Twig_Node instance
     */
    public function __set($name, $node = null)
    {
        $this->nodes[$name] = $node;
    }

    /**
     * Removes a node by name.
     *
     * @param string The node name
     */
    public function __unset($name)
    {
        unset($this->nodes[$name]);
    }

    public function count()
    {
        return count($this->nodes);
    }

    public function rewind()
    {
        reset($this->nodes);
    }

    public function current()
    {
        return current($this->nodes);
    }

    public function key()
    {
        return key($this->nodes);
    }

    public function next()
    {
        return next($this->nodes);
    }

    public function valid()
    {
        return false !== current($this->nodes);
    }
}
