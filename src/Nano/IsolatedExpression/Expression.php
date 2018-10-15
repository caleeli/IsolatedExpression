<?php
namespace Nano\IsolatedExpression;

use ReflectionClass;

/**
 * Evaluates php expressions using php-cli
 *
 * @author David Callizaya <davidcallizaya@gmail.com>
 */
class Expression
{

    public static $instance = null;
    public static $PHP = 'php';
    public $code;

    public function __construct($code)
    {
        $this->isolatedExpressionCode = $code;
    }

    /**
     * Evaluate an expression provided in the first parameter;
     *
     * @return type
     */
    public function evaluate()
    {
        $this->loadCustomMethods();
        extract((array) $this);
        return eval($this->isolatedExpressionCode . ';return $this;');
    }

    /**
     * Publish the public methods of the class as global functions.
     *
     */
    private function loadCustomMethods()
    {
        /* @var $method \ReflectionMethod */
        if (static::$instance === null) {
            $reflection = new ReflectionClass(static::class);
            foreach ($reflection->getMethods() as $method) {
                if ($method->isPublic() && !$method->isStatic()) {
                    $name = $method->getName();
                    eval('function ' . $name . ' (...$args) {return ' . static::class . '::$instance->' . $name . '(...$args);};');
                }
            }
        }
        static::$instance = $this;
    }

    public function __invoke()
    {
        $class = new ReflectionClass(static::class);
        $self = new ReflectionClass(self::class);
        //Load php requires
        $runner = (self::class !== static::class ? 'require ' . var_export($self->getFileName(), true) . ';' : '')
            . 'require ' . var_export($class->getFileName(), true) . ';'
            //serialize and unserialize
            . '$expression = unserialize(' . var_export(serialize($this), true) . ');'
            //Call expression and serialize
            . 'echo serialize($expression->evaluate());'
        ;
        $cmd = 'php -r ' . escapeshellarg($runner);
        return unserialize(exec($cmd));
    }
}
