<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher\Dumper;

use Cmobi\RabbitmqBundle\Routing\MethodCollection;

class PhpMatcherDumper extends MatcherDumper
{

    public function dump(array $options = [])
    {
        $options = array_replace(
            [
                'class' => 'ProjectMethodMatcher',
                'base_class' => 'Cmobi\\RabbitmqBundle\\Routing\\Matcher\\MethodMatcher',
            ],
            $options
        );

        return <<<EOF
<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Cmobi\RabbitmqBundle\Routing\Method;

/**
 * {$options['class']}.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    /**
     * Constructor.
     */
    public function __construct(Method \$context)
    {
        \$this->context = \$context;
    }

{$this->generateMatchMethod()}
}

EOF;
    }

    private function generateMatchMethod()
    {
        $code = rtrim($this->compileRoutes($this->getMethods()), "\n");

        return <<<EOF
    public function match(\$name)
    {
        \$allow = [];
        \$context = \$this->context;

$code
        throw 0 < count(\$allow) ? new MethodNotAllowedException(array_unique(\$allow)) : new ResourceNotFoundException();
    }
EOF;
    }

    private function compileMethods(MethodCollection $methods)
    {
        $code = '';

        foreach ($methods as $collection) {
            if (null !== $name = $collection->getAttribute('name')) {
                $code .= sprintf("        if (\$method === \$name) {\n", var_export($name, true));
                $code .= rtrim($this->compileMethod($name));
            }
        }
        $code .= "        }\n\n";

        return $code;
    }

    private function compileMethod($name)
    {
        return sprintf("            return array('_method' => '%s');\n", $name);
    }
}
