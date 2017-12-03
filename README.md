### Introduce

input_handle是一个数据过滤库，提供XSS过滤，简单SQL注入过滤和数据类型转换

### Usage

#### 简单使用

```php
$handle = new inputHandle();

$data = $handle->inputHandle($data, InputHandle::TYPE_STRING, $defaultVal);
```

#### 调用函数做额外的处理

inputHandle函数的第四个参数可以添加一些trim，htmlspecialchars等函数对数据做一些额外的处理

```php
$handle = new inputHandle();

$data = $handle->inputHandle($data, InputHandle::TYPE_STRING, $defaultVal, 'trim,htmlspecialchars');
```

#### 自定义过滤

> 创建自定义过滤类，并实现`Helbing\Handle\Factory`接口函数

```php

use Helbing\Handle\Factory;

class MyFilter implements Factory
{
    public function name()
    {
        return 'filter-name';
    }

    public function filter($input)
    {
        return doSomething($input);
    }
}
```

> 使用自定义过滤

```php
$handle = new inputHandle();

$handle->push(new MyFilter());

$data = $handle->inputHandle($data, InputHandle::TYPE_STRING);
```

### Require

- [voku/anti-xss](https://packagist.org/packages/voku/anti-xss) 一个好用的，成熟的XSS过滤包
