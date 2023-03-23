<?php

namespace app\wechat\model;

use think\admin\Model;

/**
 * 微信自动回复模型
 * Class WechatAuto
 * @package app\wechat\model
 */
class WechatAuto extends Model
{
    /**
     * 格式化创建时间
     * @param string $value
     * @return string
     */
    public function getCreateAtAttr(string $value): string
    {
        return format_datetime($value);
    }
}