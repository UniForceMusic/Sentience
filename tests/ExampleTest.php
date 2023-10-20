<?php

require_once 'requires.php';

use PHPUnit\Framework\TestCase;
use src\app\Response;
use src\util\MimeTypes;

final class ExampleTest extends TestCase
{
    public function testExample(): void
    {
        $a = 2;
        $b = 4;

        $this->assertEquals(6, ($a + $b));
    }
}
