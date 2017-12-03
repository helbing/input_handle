<?php

use PHPUnit\Framework\TestCase;
use Helbing\Handle\InputHandle;

class InputHandleTest extends TestCase
{
    private $inputHandle = null;

    public function __construct()
    {
        parent::__construct();
        $this->inputHandle = new InputHandle();
    }

    public function testDataTypeHandle()
    {
        $testData = ['foo', 'bar'];

        $result = $this->inputHandle->dateTypeHandle($testData, InputHandle::TYPE_INT);
        self::assertTrue(is_int($result));
        self::assertEquals($result, 0);

        $result = $this->inputHandle->dateTypeHandle($testData, InputHandle::TYPE_STRING);
        self::assertTrue(is_string($result));
        self::assertEquals($result, 'foo');

        $testData = 100;

        $result = $this->inputHandle->dateTypeHandle($testData, InputHandle::TYPE_STRING);
        self::assertTrue(is_string($result));
        self::assertEquals($result, '100');
    }

    public function testHandleFunc()
    {
        $testData = 'ABC';

        $result = $this->inputHandle->handleFunc($testData, 'strtolower,ucfirst');
        self::assertEquals($result, ucfirst(strtolower($testData)));
    }

    public function testInputHandle()
    {
        $testData = '<script>alert(1)</script>';

        $result = $this->inputHandle->inputHandle($testData, InputHandle::TYPE_STRING);
        self::assertEquals($result, 'alert&#40;1&#41;');

        $testData = '<img src="null" onerror="<script>alert(1)</script>"/>';

        $result = $this->inputHandle->inputHandle($testData, InputHandle::TYPE_STRING);
        self::assertEquals($result, '<img src=\"null\">alert&#40;1&#41;\"/>');
    }
}
