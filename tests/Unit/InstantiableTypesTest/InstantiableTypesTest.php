<?php

declare(strict_types = 1);
namespace Rebing\GraphQL\Tests\Unit\InstantiableTypesTest;

use Carbon\Carbon;
use Rebing\GraphQL\Tests\TestCase;

class InstantiableTypesTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('graphql.schemas.default', [
            'query' => [
                'user' => UserQuery::class,
            ],
        ]);
        $app['config']->set('graphql.types', [
            'UserType' => UserType::class,
        ]);
    }

    public function testDateFunctions(): void
    {
        Carbon::setTestNow('2020-06-05 12:34:56');

        $query = <<<'GRAQPHQL'
{
    user {
        default: dateOfBirth,
        formattedDifferent: dateOfBirth(format: "Y-m-d"),
        relative: dateOfBirth(relative: true),
        alias: createdAt
    }
}
GRAQPHQL;

        $result = $this->httpGraphql($query);

        $dateOfBirth = Carbon::today()->addMonth();
        $createdAt = Carbon::today();

        $expectedResult = [
            'data' => [
                'user' => [
                    'default' => $dateOfBirth->format('Y-m-d H:i'),
                    'formattedDifferent' => $dateOfBirth->format('Y-m-d'),
                    'relative' => '4 weeks from now',
                    'alias' => $createdAt->format('Y-m-d H:i'),
                ],
            ],
        ];
        self::assertSame($expectedResult, $result);
    }
}
