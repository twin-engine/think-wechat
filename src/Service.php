<?php

namespace app\wechat;

use app\wechat\command\Auto;
use app\wechat\command\Fans;
use app\wechat\service\AutoService;
use think\admin\Plugin;

/**
 * 组件注册服务
 * Class Service
 * @package app\wechat
 */
class Service extends Plugin
{
    /**
     * 定义当前包名
     * @var string
     */
    protected $package = 'rotoos/think-wechat';

    /**
     * 注册组件服务
     * @return void
     */
    public function register(): void
    {
        // 注册模块指令
        $this->commands([Fans::class, Auto::class]);

        // 注册粉丝关注事件
        $this->app->event->listen('WechatFansSubscribe', function ($openid) {
            AutoService::register($openid);
        });
    }

    /**
     * 增加微信配置
     * @return array[]
     */
    public static function menu(): array
    {
        // 设置插件菜单
        return [];
    }
}