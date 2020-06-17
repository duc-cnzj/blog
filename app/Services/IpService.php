<?php

namespace App\Services;

use DucCnzj\Ip\IpClient;

class IpService
{
    public static function make(): IpClient
    {
        return app('ip')
            ->setProviderConfig('ali', config('duc.ip.ali'))
            ->setProviderConfig('baidu', config('duc.ip.baidu'))
            ->use('baidu', 'ali', 'taobao');
    }
}
