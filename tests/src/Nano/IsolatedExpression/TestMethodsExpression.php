<?php
namespace Nano\IsolatedExpression;

/**
 * Implements methods that could be used within the Expression
 *
 * @author David Callizaya <davidcallizaya@gmail.com>
 */
class TestMethodsExpression extends Expression
{

    /**
     * Uppercase a string.
     *
     * @param string $string
     *
     * @return string
     */
    public function upper($string)
    {
        return strtoupper($string);
    }
}
