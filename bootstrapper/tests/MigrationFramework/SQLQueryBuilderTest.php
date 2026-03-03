<?php

namespace Tests\MigrationFramework;

use PHPUnit\Framework\TestCase;
use __PLUGIN__\Extensions\MigrationFramework\Common\SQLQueryBuilder;

class SQLQueryBuilderTest extends TestCase
{
    public function testQueryBuilderIsEmpty(): void
    {
        $builder = new SQLQueryBuilder();
        $this->assertTrue($builder->getSQL() === "");
    }

    public function testQueryIsFinalized(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->finalize();
        $this->assertTrue($builder->getSQL() === "; ");
    }

    public function testIfKeywordIsValid(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword("NOT NULL");
        $this->assertTrue($builder->getSQL() === "NOT NULL ");
    }

    public function testIfIdentifierIsEscaped(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->identifier("ABCD");
        $this->assertTrue($builder->getSQL() === "`ABCD` ");
    }

    public function testIfStringIsEscaped(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->value("Some value");
        $this->assertTrue($builder->getSQL() === "'Some value' ");
    }

    public function testIfNumberIsRecordedCorrectly(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->value(10);
        $this->assertTrue($builder->getSQL() === "10 ");
    }

    public function testIfBooleanIsRecordedCorrectly(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->value(true);
        $this->assertTrue($builder->getSQL() === "1 ");
    }

    public function testIfExceptionIsThrownInCaseOfInvalidValueType(): void
    {
        $this->expectException(\Exception::class);
        $builder = new SQLQueryBuilder();
        $builder->value([1,2,3]);
    }

    public function testIfListIsValid(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->keyword('(')->list([1, 2, 3], ",", function ($builder, $item) {
            $builder->value($item);
        })->keyword(")");
        $this->assertTrue($builder->getSQL() === "( 1 , 2 , 3 ) ");
    }

    public function testIfIdentifierListIsValid(): void
    {
        $builder = new SQLQueryBuilder();
        $builder->identifierList(['a', 'b', 'c']);
        $this->assertTrue($builder->getSQL() === "`a` , `b` , `c` ");
    }
}
