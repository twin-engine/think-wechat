<?php

use app\wechat\Service;
use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

@set_time_limit(0);
@ini_set('memory_limit', -1);

/**
 * 微信初始化
 */
class InstallWechatData extends Migrator
{
    /**
     * 初始化数据
     * @return void
     */
    public function change()
    {
        $this->insertMenu();
    }

    /**
     * 初始化菜单
     */
    private function insertMenu()
    {
        // 写入微信菜单
        PhinxExtend::write2menu([
            [
                'name' => '微信管理',
                'sort' => '200',
                'subs' => Service::menu(),
            ],
        ], [
            'url|node' => 'wechat/config/options'
        ]);
    }
}
