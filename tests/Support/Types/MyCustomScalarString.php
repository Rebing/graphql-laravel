<?php

declare(strict_types=1);

namespace Rebing\GraphQL\Tests\Support\Types;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Contracts\TypeConvertible;

class MyCustomScalarString extends ScalarType implements TypeConvertible
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @throws Error
     *
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * In the case of an invalid value this method must throw an Exception
     *
     * @param mixed $value
     *
     * @throws Error
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node         $valueNode
     * @param mixed[]|null $variables
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new InvariantViolation('Expected node of type '.StringValueNode::class.' , got '.get_class($valueNode));
        }

        return $valueNode->value;
    }

    public function toType(): Type
    {
        return new static();
    }
}
