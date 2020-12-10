<?php

declare(strict_types=1);

namespace Rebing\GraphQL;

use GraphQL\Language\AST\DirectiveNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Type\Definition\ResolveInfo;

class Helpers
{
    public static function isLumen(): bool
    {
        return class_exists('Laravel\Lumen\Application');
    }

    /**
     * @param ResolveInfo $info
     * @param string $name
     * @return DirectiveNode|null
     */
    public static function getDirectiveByName(ResolveInfo $info, string $name)
    {
        $fieldNode = $info->fieldNodes[0];
        $directives = $fieldNode->directives;
        if ((null !== $directives)) {
            foreach ($directives as $directive) {
                if ($directive->name->value === $name) {
                    return $directive;
                }
            }
        }

        return null;
    }

    /**
     * @param DirectiveNode $directive
     * @return ValueNode[]
     */
    public static function getDirectiveArguments(DirectiveNode $directive)
    {
        $args = [];
        foreach ($directive->arguments as $arg) {
            $args[$arg->name->value] = $arg->value;
        }

        return $args;
    }

    /**
     * @param mixed $objectValue
     * @param mixed[] $args
     * @param mixed|null $context
     * @param ResolveInfo $info
     * @return mixed|null
     */
    public static function defaultFieldResolverWithDirectives($objectValue, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $property = null;

        if (is_array($objectValue) || $objectValue instanceof \ArrayAccess) {
            if (isset($objectValue[$fieldName])) {
                $property = $objectValue[$fieldName];
            }
        } elseif (is_object($objectValue)) {
            if (isset($objectValue->{$fieldName})) {
                $property = $objectValue->{$fieldName};
            }
        }

        if ($property instanceof \Closure) {
            $property = $property($objectValue, $args, $context, $info);
        }

        $fieldNode = $info->fieldNodes[0];
        $fieldNodeDirectives = $fieldNode->directives ?? [];
        if (count($fieldNodeDirectives ?? [])) {
            foreach ($fieldNodeDirectives as $directive) {
                /** @var \Rebing\GraphQL\Support\Directive $d */
                foreach ($info->schema->getDirectives() as $d) {
                    if ($d->name == $directive->name->value) {
                        $property = $d->handle($property, static::getDirectiveArguments($directive));
                    }
                }
            }
        }

        return $property;
    }
}
