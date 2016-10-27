<?php

function pr()
{
    $trace = debug_backtrace()[0];
    echo '<pre xstyle="font-size:9px;font: small monospace;">';
    echo PHP_EOL.str_repeat('=', 100).PHP_EOL;
    echo 'file '.$trace['file'].' line '.$trace['line'];
    echo PHP_EOL.str_repeat('-', 100).PHP_EOL;

    if (1 === func_num_args()) {
        $args = func_get_arg(0);
    } else {
        $args = func_get_args();
    }

    echo prx($args);

    echo PHP_EOL.str_repeat('=', 100).PHP_EOL;
    echo '</pre>';
}

function prx($s)
{
    $a = [
        'Object'.PHP_EOL.' \*RECURSION\*' => '#RECURSION',
        '    ' => '  ',
        PHP_EOL.PHP_EOL => PHP_EOL,
        '  \(' => '(',
        '  \)' => ')',
        //' (=> Array|Object)'.PHP_EOL.'\s+\(' => ' $1(',
        '\('.PHP_EOL.'\s+\)' => '()',
        'Array\s+\(\)' => 'Array()'
    ];

    $s = print_r($s, true);
    foreach($a as $key => $val) {
        $s = preg_replace('#'.$key.'#', $val, $s);
    }

    return $s;
}
