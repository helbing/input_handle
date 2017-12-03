<?php

namespace Helbing\Handle;

use voku\helper\AntiXSS;

class XssFilter implements Factory
{
    public function name()
    {
        return 'xss';
    }

    public function filter($input)
    {
        $xss = new AntiXSS();
        return $xss->xss_clean($input);
    }
}
