<?php 

namespace YonisSavary\DBWand\TestsCLI\CLI;

use PHPUnit\Framework\Attributes\DataProvider;
use YonisSavary\DBWand\CLI\Terminal;
use YonisSavary\DBWand\Tests\CLI\CLITestCase;

class SelectTest extends CLITestCase
{
    #[DataProvider('allDatabases')]
    public function testSelectUsers(Terminal $terminal)
    {
        $context = $this->expectCountFromScript(3, "SELECT * FROM users", $terminal);

        $this->assertEquals(
            ['id', 'email', 'username', 'password_hash', 'created_at'], 
            array_keys($context->dataset[0])
        );
    }

    #[DataProvider('allDatabases')]
    public function testBob(Terminal $terminal)
    {
        $this->expectCountFromScript(1, "SELECT * FROM users WHERE username = 'bob'", $terminal);
    }

    #[DataProvider('allDatabases')]
    public function testFromDataset(Terminal $terminal)
    {
        $this->expectCountFromScript(2, 
        "SELECT * FROM users
        SELECT * FROM dbwand WHERE username LIKE '%a%'
        ", $terminal);
    }
}