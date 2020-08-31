<?php

/*
* This file is part of the MyCMS package.
*
* (c) ZhangBing <550695@qq.com>
*
* Date: 2020/08/17
* Time: 11:14
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $token = $event->getData();
        $event->setData(["data"=>$token]);
    }
}