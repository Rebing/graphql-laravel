<?php

declare(strict_types = 1);
namespace Rebing\GraphQL\Tests\Unit\WithTypeTests;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Illuminate\Support\Collection;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WrapperType extends ObjectType
{
    /**
     * @param string $typeName The type name defined in graphql.php configuration file.
     * @param string $customTypeName The new name for wrap type
     */
    public function __construct(string $typeName, string $customTypeName)
    {
        $config = [
            'name' => $customTypeName,
            'fields' => $this->getMessagesFields($typeName),
        ];

        $underlyingType = GraphQL::type($typeName);

        if (isset($underlyingType->config['model'])) {
            $config['model'] = $underlyingType->config['model'];
        }

        parent::__construct($config);
    }

    /**
     * Resolve the wrap type.
     *
     * @param string $typeName The type name defined in graphql.php configuration file.
     */
    protected function getMessagesFields(string $typeName): array
    {
        return [
            'data' => [
                'type' => GraphQL::type($typeName),
                'resolve' => function ($data) {
                    return \array_key_exists('data', $data) ?
                        $data['data'] :
                        $data;
                },
            ],
            'messages' => [
                'type' => GraphQLType::listOf(GraphQL::type('SimpleMessageType')),
                'description' => 'List of messages',
                'resolve' => function ($data): Collection {
                    return $data['messages'];
                },
            ],
        ];
    }
}
