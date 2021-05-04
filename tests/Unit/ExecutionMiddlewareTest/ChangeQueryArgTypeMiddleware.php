<?php

declare(strict_types = 1);
namespace Rebing\GraphQL\Tests\Unit\ExecutionMiddlewareTest;

use Closure;
use GraphQL\Language\Parser;
use GraphQL\Language\Visitor;
use GraphQL\Language\AST\NodeKind;
use Rebing\GraphQL\Support\ExecutionMiddleware\ExecutionMiddleware;

class ChangeQueryArgTypeMiddleware extends ExecutionMiddleware
{
    /**
     * @param string|mixed $query
     * @param array<string,mixed> $args
     * @return Closure|array<mixed>
     */
    public function handle($query, $args, Closure $next)
    {
        $query = Parser::parse($query);

        Visitor::visit($query, [
            NodeKind::VARIABLE_DEFINITION => function ($node, $key, $parent, $path, $ancestors) {
                $node->type->name->value = 'Int';

                return $node;
            },
        ]);

        return $next($query, $args);
    }
}