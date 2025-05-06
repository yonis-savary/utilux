<?php

namespace YonisSavary\DBWand\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use YonisSavary\DBWand\Database\Utils\DatabaseUtils;

class DatabaseUtilsTest extends DBWandTestCase
{
    #[DataProvider('allDatabaseUtils')]
    public function testEscapeTableName(DatabaseUtils $utils)
    {
        $this->assertIsString($utils->escapeTableName('users'));
    }

    #[DataProvider('allDatabaseUtils')]
    public function testListFields(DatabaseUtils $utils)
    {
        $this->assertCount(28, $utils->listFields());
    }

    #[DataProvider('allDatabaseUtils')]
    public function testListTables(DatabaseUtils $utils)
    {
        $this->assertCount(5, $utils->listTables());
    }
    /*

    #[DataProvider('allDatabaseUtils')]
    public function testListForeignKeyConstraints(DatabaseUtils $utils)
    {
        $utils->listForeignKeyConstraints();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testListUniqueConstraints(DatabaseUtils $utils)
    {
        $utils->listUniqueConstraints();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testTransaction(DatabaseUtils $utils)
    {
        $utils->transaction();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testAssertTableExists(DatabaseUtils $utils)
    {
        $utils->assertTableExists();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testAssertColumnExists(DatabaseUtils $utils)
    {
        $utils->assertColumnExists();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testGetFieldsPointingTo(DatabaseUtils $utils)
    {
        $utils->getFieldsPointingTo();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testDatasetToSelectValuesExpression(DatabaseUtils $utils)
    {
        $utils->datasetToSelectValuesExpression();
    }

    #[DataProvider('allDatabaseUtils')]
    public function testDatasetToValuesExpression(DatabaseUtils $utils)
    {
        $utils->datasetToValuesExpression();
    }
    */
}
