<?php

namespace WPTests;

use __PLUGIN__\Extensions\CoreAPI\Builders\DB;
use __PLUGIN__\Framework\DI\Container;
use PHPUnit\Framework\TestCase;

class DatabaseQueryBuilderTest extends TestCase
{
    private string $prefix;

    public function setUp(): void
    {
        global $wpdb;
        $this->prefix = $wpdb->prefix;
        Container::get()->rebind("PluginPrefix")->toConstantValue("test");
    }

    public function testSelectClause()
    {
        $sql = DB::get()->select("column")->getSQL();
        $this->assertEquals("SELECT `column` ", $sql);
    }

    public function testSelectClause2()
    {
        $sql = DB::get()->select(["column", "col"])->getSQL();
        $this->assertEquals("SELECT `column` AS `col` ", $sql);
    }

    public function testSelectClause3()
    {
        $sql = DB::get()->select(["columnA","A"], "columnB", "columnC")->getSQL();
        $this->assertEquals("SELECT `columnA` AS `A` , `columnB` , `columnC` ", $sql);
    }

    public function testFromClause()
    {
        $sql = DB::get()->from("table")->getSQL();
        $this->assertEquals("FROM `{$this->prefix}test_table` ", $sql);
    }

    public function testFromClause2()
    {
        $sql = DB::get()->from("table", "alias")->getSQL();
        $this->assertEquals("FROM `{$this->prefix}test_table` AS `alias` ", $sql);
    }

    public function testJoinClause()
    {
        $sql = DB::get()->join("table")->getSQL();
        $this->assertEquals("JOIN `{$this->prefix}test_table` ", $sql);
    }

    public function testJoinClause2()
    {
        $sql = DB::get()->join("table", "alias")->getSQL();
        $this->assertEquals("JOIN `{$this->prefix}test_table` AS `alias` ", $sql);
    }

    public function testLeftJoinClause()
    {
        $sql = DB::get()->leftJoin("table")->getSQL();
        $this->assertEquals("LEFT JOIN `{$this->prefix}test_table` ", $sql);
    }

    public function testLeftJoinClause2()
    {
        $sql = DB::get()->leftJoin("table", "alias")->getSQL();
        $this->assertEquals("LEFT JOIN `{$this->prefix}test_table` AS `alias` ", $sql);
    }

    public function testRightJoinClause()
    {
        $sql = DB::get()->rightJoin("table")->getSQL();
        $this->assertEquals("RIGHT JOIN `{$this->prefix}test_table` ", $sql);
    }

    public function testRightJoinClause2()
    {
        $sql = DB::get()->rightJoin("table", "alias")->getSQL();
        $this->assertEquals("RIGHT JOIN `{$this->prefix}test_table` AS `alias` ", $sql);
    }

    public function testOnClause()
    {
        $sql = DB::get()->on("column", ["table", "column2"])->getSQL();
        $this->assertEquals("ON `column` = `table` . `column2` ", $sql);
    }

    public function testWhereClause()
    {
        $sql = DB::get()->where("columnName", "=", true)->getSQL();
        $this->assertEquals("WHERE `columnName` = 1 ", $sql);
    }

    public function testWhereClause2()
    {
        $sql = DB::get()->where(["table", "columnName"], "<>", 10)->getSQL();
        $this->assertEquals("WHERE `table` . `columnName` <> 10 ", $sql);
    }

    public function testWhereClause3()
    {
        $sql = DB::get()->whereNull("column")->getSQL();
        $this->assertEquals("WHERE `column` IS NULL ", $sql);
    }

    public function testWhereClause4()
    {
        $sql = DB::get()->whereNotNull(["table", "column"])->getSQL();
        $this->assertEquals("WHERE `table` . `column` IS NOT NULL ", $sql);
    }
}
