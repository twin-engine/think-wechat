<?php

namespace app\wechat\model;

use think\admin\Model;

/**
 * 微信回复关键词模型
 * Class WechatKeys
 * @package app\wechat\model
 */
class WechatKeys extends Model
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