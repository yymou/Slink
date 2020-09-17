<?php
return [
    //设置存储
    'redis' => [
        'prefix' => 'slink',
        'write' => [
            'hostname' => '127.0.0.1',
            'username' => '',
            'password' => '',
            'database' => '0',
            'port' => '6379',
            'timeout' => '5'
        ],
        //支持设置多个读库
        'read' => [
            'count' => 1,
            [
                'hostname' => '127.0.0.1',
                'username' => '',
                'password' => '',
                'database' => '0',
                'port' => '6379',
                'timeout' => '5'
            ]
        ],
        'orilinkTtl' => 86400 //原始链接缓存过期时间(如设置一天则 24小时内同样的链接会返回相同的短链) 单位为秒
    ],
    'linklen' => 7, //生成短链长度 推荐是7位 一旦项目启动禁止修改该值 7位生成的数量我62的*6*次方 最小为5
];