<?php

namespace Helbing\Handle;

use Helbing\Handle\Exception\IllegalDataTypeException;

class InputHandle
{
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';

    private $filterList = [];

    public function __construct($default = true)
    {
        if ($default) {
            // 设置一些默认会进行的过滤处理
            $this->push(new XssFilter());
            $this->push(new SqlInjectionFilter());
        }
    }

    /**
     * @param Factory $handleFilter
     * @return $this
     */
    public function push(Factory $handleFilter)
    {
        $this->filterList[$handleFilter->name()] = $handleFilter;
        return $this;
    }

    /**
     * @param $filterName
     * @return $this
     */
    public function remove($filterName)
    {
        if (isset($this->filterList[$filterName])) {
            unset($this->filterList[$filterName]);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getFilterList()
    {
        return $this->filterList;
    }

    /**
     * @param $input
     * @param $dataType
     * @param null $defaultVal
     * @param null $otherHandle
     * @return array|bool|float|int|mixed|null|string
     */
    public function inputHandle($input, $dataType, $defaultVal = null, $otherHandle = null)
    {
        try {
            // 数据类型处理
            $input = $this->dateTypeHandle($input, $dataType);

            if ($dataType == self::TYPE_STRING) {
                $input = $this->dataHandle($input, $dataType, $otherHandle);
            } else if ($dataType == self::TYPE_ARRAY) {
                $input = $this->arrayDataHandle($input, $dataType, $otherHandle);
            }

            return $input;
        } catch (IllegalDataTypeException $exception) {
            return $defaultVal;
        }
    }

    /**
     * @param $input
     * @param $dataType
     * @param null $otherHandle
     * @return mixed
     */
    public function dataHandle($input, $dataType, $otherHandle = null)
    {
        foreach ($this->filterList as $filter) {
            $input = $filter->filter($input, $dataType);
        }

        return $this->handleFunc($input, $otherHandle);
    }

    /**
     * @param $input
     * @param null $otherHandle
     * @return mixed
     */
    public function handleFunc($input, $otherHandle = null)
    {
        $handleFunc = explode(',', $otherHandle);

        foreach ($handleFunc as $func) {
            $func = trim($func);

            if ($func && function_exists($func)) {
                $input = call_user_func($func, $input);
            }
        }

        return $input;
    }

    /**
     * @param $input
     * @param $dataType
     * @param null $otherHandle
     * @return array
     */
    public function arrayDataHandle($input, $dataType, $otherHandle = null)
    {
        $result = [];

        foreach ($input as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->arrayDataHandle($item, $dataType, $otherHandle);
            } else {
                $result[$key] = $this->dataHandle($item, $dataType, $otherHandle);
            }
        }

        return $result;
    }

    /**
     * @param $input
     * @param $dataType
     * @return bool|float|int|string
     * @throws IllegalDataTypeException
     */
    public function dateTypeHandle($input, $dataType)
    {
        // 数据类型为数组，但是输入的类型不是数组的处理
        if ($dataType != self::TYPE_ARRAY && is_array($input)) {
            $input = empty($input) ? '' : strval($input[0]);
        }

        if ($dataType == self::TYPE_INT) {
            $input = intval($input);
        } else if ($dataType == self::TYPE_FLOAT) {
            $input = floatval($input);
        } else if ($dataType == self::TYPE_BOOLEAN) {
            $input = boolval($input);
        } else if ($dataType == self::TYPE_STRING) {
            $input = strval($input);
        } else {
            throw new IllegalDataTypeException();
        }

        return $input;
    }
}
