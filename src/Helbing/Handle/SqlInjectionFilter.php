<?php

namespace Helbing\Handle;

class SqlInjectionFilter implements Factory
{
    public function name()
    {
        return 'sqlInjection';
    }

    public function filter($input)
    {
        // SQL注入最主要还是要靠预处理和数据类型处理来解决
        return addslashes($input);
    }
}
