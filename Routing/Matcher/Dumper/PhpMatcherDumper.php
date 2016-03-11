<?php

namespace Cmobi\RabbitmqBundle\Routing\Matcher\Dumper;

use Cmobi\RabbitmqBundle\Routing\Method;
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

{$this->generateMatchMethod()}
}

EOF;
    }

    private function generateMatchMethod()
    {
        $code = rtrim($this->compileMethods($this->getMethods()), "\n");

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
                $code .= sprintf("        if (\$context->getMethod() === %s) {\n", var_export($name, true));
                $code .= rtrim($this->compileMethod($collection));
                $code .= "\n\n        }\n\n";
            }
        }

        return $code;
    }

    private function compileMethod(Method $method)
    {
        $code = '';

        if ($method->getDefaults()) {
            $code .= sprintf("            return %s;\n", str_replace("\n", '', var_export(array_replace($method->getDefaults(), ['_method' => $method->getName()]), true)));
        } else {
            $code .= sprintf("            return ['_method' => '%s'];\n", $method->getName());
        }

        return $code;
    }
}