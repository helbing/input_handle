<?php

namespace Helbing\Handle;

interface Factory
{
    // 名称
    public function name();

    // 过滤函数
    public function filter($input);
}
