<?php

namespace app\wechat\service;

use app\wechat\model\WechatMedia;
use app\wechat\model\WechatNews;
use app\wechat\model\WechatNewsArticle;
use think\Response;
use think\admin\Service;
use think\admin\Storage;
use WeChat\Contracts\MyCurlFile;

/**
 * 微信素材管理
 * Class MediaService
 * @package app\wechat\service
 */
class MediaService extends Service
{
    /**
     * 通过图文ID读取图文信息
     * @param mixed $id 本地图文ID
     * @param array $map 额外的查询条件
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function news($id, array $map = []): array
    {
        // 文章主体数据
        $data = WechatNews::mk()->where(['id' => $id, 'is_deleted' => 0])->where($map)->findOrEmpty()->toArray();
        if (empty($data)) return [];

        // 文章内容编号
        $data['articles'] = [];
        $aids = $data['articleids'] = str2arr($data['article_id']);
        if (empty($aids)) return $data;

        // 文章内容列表
        $items = WechatNewsArticle::mk()->whereIn('id', $aids)->withoutField('create_by,create_at')->select()->toArray();
        foreach ($aids as $aid) foreach ($items as $item) if (intval($item['id']) === intval($aid)) $data['articles'][] = $item;

        // 返回文章内容
        return $data;
    }

    /**
     * 上传图片永久素材
     * @param string $url 文件地址
     * @param string $type 文件类型
     * @param array $video 视频信息
     * @return string media_id
     * @throws \WeChat\Exceptions\InvalidResponseException
     * @throws \WeChat\Exceptions\LocalCacheException
     * @throws \think\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function upload(string $url, string $type = 'image', array $video = []): string
    {
        $map = ['md5' => md5($url), 'appid' => WechatService::getAppid()];
        if (($mediaId = WechatMedia::mk()->where($map)->value('media_id'))) return $mediaId;
        $result = WechatService::WeChatMedia()->addMaterial(static::buildCurlFile($url), $type, $video);
        WechatMedia::mUpdate([
            'md5'       => $map['md5'],
            'type'      => $type,
            'appid'     => $map['appid'],
            'media_id'  => $result['media_id'],
            'media_url' => $result['url'] ?? '',
            'local_url' => $url,
        ], 'type', $map);
        return $result['media_id'];
    }

    /**
     * 创建 CURL 文件对象
     * @param string $local 文件路径或网络地址
     * @return MyCurlFile
     * @throws \WeChat\Exceptions\LocalCacheException
     */
    private static function buildCurlFile(string $local): MyCurlFile
    {
        if (file_exists($local)) {
            return new MyCurlFile($local);
        } else {
            return new MyCurlFile(Storage::down($local)['file']);
        }
    }

    /**
     * 获取二维码内容接口
     * @param string $text 二维码文本内容
     * @return string
     */
    public static function getQrcode(string $text)
    {
        $qrCode = new \Endroid\QrCode\QrCode($text);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setWriterByName('png');
        return $qrCode->writeString();//response($qrCode->writeString(), 200, ['Content-Type' => 'image/png']);
    }
}
