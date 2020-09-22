<?php
namespace zyk\tools\uploader;

use Qiniu\Auth;
use Qiniu\Http\Client;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use zyk\tools\BaseInterface;

class Qiniu implements BaseInterface {

    /**
     * 服务基本信息
     * @author lwh 2019-12-03
     * @return array
     */
    public function serviceInfo() {
        return ['service_name' => '七牛云图片处理', 'service_class' => 'Qiniu', 'service_describe' => '系统图片处理', 'author' => 'LiWenHui', 'version' => '1.0'];
    }

    /**
     * 上传凭证-获取上传token
     * @auhtor lwh 2019-12-19
     * @param $policy 参数1
     * @param $type 类型,默认0,0-表示公有, 1-表示私有
     * @return string
     */
    public function uploadToken($policy, $type) {
        if($type == 1) {
            $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
            $policy['callbackUrl'] = config('app.qiniu.callbackHost').rtrim($policy['callbackUrl']);
            return $auth->uploadToken(config('app.qiniu.bucket'), null, 108000, $policy);
        }else {
            $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
//            $policy['callbackUrl'] = config('app.qiniu.callbackHost').rtrim($policy['callbackUrl']);
            return $auth->uploadToken(config('app.qiniu.bucket'), null, 108000, $policy);
        }
    }

    /**
     * 获取验证类
     * @author lwh 2019-12-03
     * @return Auth
     */
    protected function downloadAuth() {
        return new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
    }

    /**
     * 文件下载
     * @author lwh 2019-12-03
     * @param $type 类型,默认0,0-表示公有, 1-表示私有
     * @param $fkey 图片文件key
     * @param string $ext 加后缀，一般为下载连接
     * @return string 返回
     */
    public function downloadUrl($type,$fkey, $ext = '') {
        if($type == 1) {
            $url = config('app.qiniu.file_upload_domain').$fkey;
            if(!empty($ext)) {
                $url .= "?attname=" . md5($fkey). $ext;
            }
            $auth = $this->downloadAuth();
            return $auth->privateDownloadUrl($url);
        }else {
            $url = config('app.qiniu.file_upload_domain').$fkey;
            if(!empty($ext)) {
                $url .= "?attname=". $ext;
            }
            return $url;
        }
    }

    /**
     * 预览图片获取缩率图(公有)
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $type 类型
     * @param int $width 宽度
     * @param int $height 高度
     * @return string 返回
     */
    public function pubThumbUrl($fkey, $type, $width = 0, $height = 0) {
        return config('app.qiniu.file_upload_domain') . $fkey . '?imageView2/' . $type . '/w/' . $width . '/h/' . $height;
    }

    /**
     * 预览图片获取缩率图(私有)
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $type 类型
     * @param int $width 宽度
     * @param int $height 高度
     * @return string 返回
     */
    public function thumbUrl($fkey, $type, $width = 0, $height = 0) {
        $url = config('app.qiniu.file_upload_domain') . $fkey . '?imageView2/' . $type . '/w/' . $width . '/h/' . $height;
        $auth = $this->downloadAuth();
        return $auth->privateDownloadUrl($url);
    }

    /**
     * 获取元数据
     * @author lwh 2019-12-03
     * @param $type 类型,默认0,0-表示公有, 1-表示私有
     * @param $fkey 图片文件key
     * @return mixed
     */
    public function getFileMine($type,$fkey) {
        if($type == 1) {
            $url  = '/stat/'.\Qiniu\base64_urlSafeEncode(config('app.qiniu.bucket').':'.$fkey);
            $auth = $this->downloadAuth()->authorization($url);
            $headers['Authorization'] = $auth['Authorization'];
            $res  = Client::get(config('app.qiniu.ObjHost').$url, $headers);
            return $res->json();
        }else {
            $url  = '/stat/'.\Qiniu\base64_urlSafeEncode(config('app.qiniu.bucket').':'.$fkey);
            $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
            $auth = $auth->authorization($url);
            $headers['Authorization'] = $auth['Authorization'];
            $res  = Client::get(config('app.qiniu.ObjHost').$url, $headers);
            return $res->json();
        }
    }

    /**
     * 下载文件(公有)
     * @author lwh 2019-12-03
     * @param $type 类型
     * @param $file_key 图片文件key
     * @param bool $need_mine
     * @return array|bool
     */
    public function pubDownloadFile($type, $file_key, $need_mine = false) {
        $ext = '';
        if ($need_mine) {
            // 需要获取元数据信息，主要为了补充后缀
            $mine = $this->getFileMine(0,$file_key);
            if ($mine['mimeType'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $ext = '.xlsx';
            }
        }

        $save_path = config('download_path').'file_temp/'.$type.'/';
        $file_name = md5($type.$file_key).$ext;

        $file_download_path = $this->pubDownloadUrl($file_key, $file_name);
        if (!is_dir($save_path)) {
            mkdir($save_path,0777,true);
        }

        try {
            $res = file_put_contents($save_path.$file_name, file_get_contents($file_download_path));
        } catch (\Exception $e) {
            return false;
        }
        if ($res) {
            return ['file_key' => $file_key, 'file_temp_path' => $save_path.$file_name];
        }
        return false;
    }
    /**
     * 下载文件(私有)
     * @author lwh 2019-12-03
     * @param $type 类型
     * @param $file_key 图片文件key
     * @param bool $need_mine
     * @return array|bool
     */
    public function downloadFile($type, $file_key, $need_mine = false) {
        $ext = '';
        if ($need_mine) {
            // 需要获取元数据信息，主要为了补充后缀
            $mine = $this->getFileMine(1,$file_key);
            if ($mine['mimeType'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $ext = '.xlsx';
            }
        }

        $save_path = config('download_path').'file_temp/'.$type.'/';
        $file_name = md5($type.$file_key).$ext;

        $file_download_path = $this->downloadUrl(1,$file_key, $file_name);
        if (!is_dir($save_path)) {
            mkdir($save_path,0777,true);
        }

        try {
            $res = file_put_contents($save_path.$file_name, file_get_contents($file_download_path));
        } catch (\Exception $e) {
            return false;
        }
        if ($res) {
            return ['file_key' => $file_key, 'file_temp_path' => $save_path.$file_name];
        }
        return false;
    }

    /**
     * 删除资源
     * @author lwh 2019-12-03
     * @param $key 图片文件key
     * @return mixed 返回
     */
    public function delFile($key) {
        $bucketManager = new BucketManager($this->downloadAuth());
        return $bucketManager->delete(config('app.qiniu.bucket'), $key);
    }

    /**
     * 上传网络资源图片（小图）
     * @author lwh 2019-12-03
     * @param $pic_url 地址
     * @return bool 返回
     */
    public function uploadPicUrl($pic_url) {
        $upload = new UploadManager();
        $file_name = md5(md5($pic_url).time().rand(1000, 9999));
        $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
        $upToken = $auth->uploadToken(config('app.qiniu.bucket'), null, 3600);
        $data = file_get_contents($pic_url);
        if (!$data) {
            return false;
        }
        $res = $upload->put($upToken, $file_name, $data);
        if ($res[0]) {
            return $res[0]['key'];
        }
        return false;
    }

    /**
     * 上传七牛
     * @author lwh 2019-12-03
     * @param $image_txt 内容
     * @return bool 返回
     */
    public function uploadPicBase64($image_txt) {
        $image = trim($image_txt);
        if ($image) {
            $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
            $upToken = $auth->uploadToken(config('app.qiniu.bucket'), null, 3600);
            $upUrl = config('app.qiniu.upload_url') . '/putb64/-1';
            $qiniu = $this->phpCurlImg($upUrl, $image, $upToken);
            $qiniuArr = json_decode($qiniu,true);
            if(!empty($qiniuArr['key'])) {
                return $qiniuArr['key'];
            } else {
                return false;
            }
        }
        return false;
    }
    /**
     * 七牛base64上传方法
     * @author lwh 2019-12-03
     * @param $remote_server 地址
     * @param $post_string 参数
     * @param $upToken token
     * @return bool|string 返回
     */
    public function phpCurlImg($remote_server,$post_string,$upToken) {
        $headers = array();
        $headers[] = 'Content-Type:application/octet-stream';
        $headers[] = 'Authorization:UpToken '.$upToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$remote_server);
        curl_setopt($ch, CURLOPT_HTTPHEADER ,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 公库图片裁剪(公有)
     * @author lwh 2019-12-03
     * @param $key 图片文件key
     * @param string $gravity 位置
     * @param int $witch 宽度
     * @param int $height 高度
     * @return bool|string 返回
     */
    public function cropPubImg($key, $gravity = 'NorthWest',  $witch = 0, $height = 0) {
        $url = $this->pubDownloadUrl($key);
        $url = $url."?imageMogr2/gravity/{$gravity}/crop/";
        if (!empty($witch) && !empty($height)) {
            return $url."{$witch}x{$height}";
        } elseif (empty($witch) && !empty($height)) {
            return $url."x{$height}";
        } elseif (!empty($witch) && empty($height)) {
            return $url."{$witch}x";
        } else {
            return false;
        }
    }
    /**
     * 私库图片裁剪(私有)
     * @author lwh 2019-12-03
     * @param $key 图片文件key
     * @param string $gravity 位置
     * @param int $witch 宽度
     * @param int $height 高度
     * @return bool|string 返回
     */
    public function cropPrivImg($key, $gravity = 'NorthWest',  $witch = 0, $height = 0) {
        $url = $this->downloadUrl(1,$key);
        $url = $url."?imageMogr2/gravity/{$gravity}/crop/";
        if (!empty($witch) && !empty($height)) {
            return $url."{$witch}x{$height}";
        } elseif (empty($witch) && !empty($height)) {
            return $url."x{$height}";
        } elseif (!empty($witch) && empty($height)) {
            return $url."{$witch}x";
        } else {
            return false;
        }
    }

    /**
     * 获取带文字=水印的缩略图链接
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $watermark 文字水印内容
     * @param $gravity 水印位置
     * @param $type 缩略图类型
     * @param int $width 缩略图款
     * @param int $height 缩略图高
     * @param $imgwidth 图片文件宽
     * @return string 展示链接
     */
    public function getWatermarkTTUrl($fkey, $watermark, $gravity, $type, $width = 0, $height = 0, $imgwidth) {
        $font_width = mb_strlen($watermark, 'UTF-8');
        $size       = intval($imgwidth * 10 / $font_width);
        $water      = 'watermark/2/text/' . $this->base64UrlSafeEncode($watermark) . '/gravity/' . $gravity . '/dissolve/100/font/' . $this->base64_urlSafeEncode('微软雅黑') . '/fontsize/' . $size . '/fill/' . $this->base64_urlSafeEncode('#e7e7e7');
        $image      = 'imageView2/' . $type . '/w/' . $width . '/h/' . $height;
        $url        = config('app.qiniu.file_upload_domain') . $fkey . '?' . $water . '|' . $image;
        return $this->getAuthorization($url);
    }

    /**
     * 获取带图片水印的缩略图的链接
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $watermark 水印图片key
     * @param $gravity 指定水印位置
     * @param $type 缩略图类型
     * @param int $width 缩略图宽
     * @param int $height 缩略图高
     * @param $imgwidth 图片文件的宽
     * @param $imgheight 图片文件的高
     * @return string 展示链接
     */
    public function getWatermarkImageThumbUrl($fkey, $watermark, $gravity, $type, $width = 0, $height = 0, $imgwidth, $imgheight) {

        $ws    = $imgwidth > $imgheight ? 0.7 : 0.5;
        $image = $this->downloadUrl(1,$watermark);
        $water = 'watermark/1/image/' . $this->base64UrlSafeEncode($image) . '/dissolve/100/gravity/' . $gravity . '/ws/' . $ws."/wst/1";
        $image = 'imageView2/' . $type . '/w/' . $width . '/h/' . $height;
        $url   = config('app.qiniu.file_upload_domain') . $fkey . '?' . $water . '|' . $image;
        return $this->getAuthorization($url);
    }

    /**
     * 生成带文字水印的图片私有下载地址
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param string $alias 名称
     * @param $watermark 水印
     * @param $gravity 位置
     * @param $width 宽度
     * @return string 返回
     */
    public function getTextWatermarkPrivateDownloadUrl($fkey, $alias = '', $watermark, $gravity, $width) {
        $font_width = mb_strlen($watermark, 'UTF-8');
        $size       = intval($width * 10 / $font_width);
        $water      = 'watermark/2/text/' . $this->base64UrlSafeEncode($watermark) . '/gravity/' . $gravity . '/dissolve/100/font/' . $this->base64_urlSafeEncode('微软雅黑') . '/fontsize/' . $size . '/fill/' . $this->base64_urlSafeEncode('#e7e7e7');
        $url        = config('app.qiniu.file_upload_domain') . $fkey . '?' . $water;
        if (!empty($alias)) {
            $alias = urlencode($alias);
            $url   .= "&attname=$alias";
        }
        return $this->getAuthorization($url);
    }

    /**
     * 生成带图片水印的图片私有下载地址
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param string $alias 名称
     * @param $watermark 水印
     * @param $gravity 位置
     * @param $width 宽度
     * @param $height 高度
     * @return string 返回
     */
    public function getImageWatermarkPrivateDownloadUrl($fkey, $alias = '', $watermark, $gravity, $width, $height) {
        $ws    = $width > $height ? 0.7 : 0.5;
        $image = $this->downloadUrl(1,$watermark);
        $water = 'watermark/1/image/' . $this->base64UrlSafeEncode($image) . '/dissolve/100/gravity/' . $gravity . '/ws/' . $ws."/wst/1";
        $url   = config('app.qiniu.file_upload_domain') . $fkey . '?' . $water;
        if (!empty($alias)) {
            $alias = urlencode($alias);
            $url   .= "&attname=$alias";
        }
        return $this->getAuthorization($url);
    }

    /**
     * 上传单个文件（支持分片上传）
     * @author lwh 2019-12-03
     * @param $file_path 地址
     * @param $auth_token token
     * @param array $params 参数
     * @return array 参数
     * @throws \Exception
     */
    public function uploadSingleFile($file_path, $auth_token, $params = [], $fileKey = '') {
        $uploader = new UploadManager();
        if (!$fileKey) {
            $fileKey = pathinfo($file_path, PATHINFO_EXTENSION);
        }
        return $uploader->putFile($auth_token, $fileKey, $file_path, $params);
    }


    /**
     * 多文件压缩，单文件路径处理
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $fname 文件名
     * @param $alias string 文件别名
     * @return string 返回
     */
    public function watermarkZipPackSingleFileUrl($fkey, $fname, $alias = '', $watermark, $gravity, $width) {
        $file_public_url = $this->getTextWatermarkPrivateDownloadUrl($fkey, $fname, $watermark, $gravity, $width);
        $url             = '/url/' . $this->base64UrlSafeEncode($file_public_url);
        if (!empty($alias)) {
            $url .= '/alias/' . $this->base64UrlSafeEncode($alias);
        }
        return $url;
    }

    public function getAuthorization($url) {
        $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
        return $auth->privateDownloadUrl($url);
    }

    /**
     * 文件压缩认证
     * @author lwh 2019-12-03
     * @param $url 地址
     * @param $body body
     * @return array 返回
     */
    public function getZipAuthorization($url, $body) {
        $auth          = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
        $authorization = $auth->authorization($url, $body, 'application/x-www-form-urlencoded');
        if ($authorization['Authorization']) {
            return $authorization['Authorization'];
        }
        return false;
    }

    /**
     * 多文件压缩，单文件路径处理
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $fname 文件名
     * @param $alias string 文件别名
     * @return string 返回
     */
    public function zipPackSingleFileUrl($fkey, $fname, $alias = '') {
        $file_public_url = $this->downloadUrl(1,$fkey, $fname);
        $url             = '/url/' . $this->base64UrlSafeEncode($file_public_url);
        if (!empty($alias)) {
            $url .= '/alias/' . $this->base64UrlSafeEncode($alias);
        }
        return $url;
    }

    /**
     * 获取压缩图片
     * @author lwh 2019-12-03
     * @param $fkey 图片文件key
     * @param $size 大小
     * @return string 返回
     */
    public function imageMogrFileSizeLimitUrl($fkey, $size) {
        $url = config('app.qiniu.file_upload_domain') . $fkey . '?imageMogr2/size-limit/' . $size;
        return $this->getAuthorization($url);
    }

    /**
     * 组装多文件压缩内容
     * @author lwh 2019-12-03
     * @param $fops 参数
     * @return string 返回
     */
    public function pfopZipFileParams($fops) {
        $bucket        = config('app.qiniu.bucket');
        $exit_file_key = config('app.qiniu.exit_file_key');
        $encoding      = 'utf-8';
        //处理操作系统编码，如果是win则修改成gbk
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $agent)) {
            $encoding = 'gbk';
        }

        $fops = 'mkzip/2' . '/encoding/' . $this->base64UrlSafeEncode($encoding) . $fops;
        return "bucket=$bucket&key=$exit_file_key&fops=$fops";
    }

    /**
     * URL安全的Base64编码。
     * @author lwh 2019-12-03
     * @param $data 参数
     * @return mixed 返回
     */
    public function base64UrlSafeEncode($data) {
        $find    = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    /**
     * 获取七牛文件下载地址
     * @author GJQ 2020-03-16
     * @param $fkey
     * @param string $ext
     * @param string $attrNmae
     * @return string
     */
    public function downloadFileUrl($fkey, $ext = '', $attrNmae = '') {
        $url = config('app.qiniu.file_upload_domain') . $fkey;
        if(!empty($ext)) {
            $url .= empty($attrNmae) ? "?attname=" . md5($fkey). $ext : "?attname=" . $attrNmae . $ext;
        }
        return $url;
    }


    /**
     * 获取图片信息
     * @author LYJ 2020.05.22
     * @param string $fkey 七牛key
     */
    public function getImageInfo($fkey) {
        $url = config('app.qiniu.file_upload_domain') . $fkey.'?imageInfo';
        $client = Client::get($url);
        return $client->json();
    }

    /**
     * 查询文件信息
     * @author LYJ 2020.05.29
     * @param string $fkey key值
     */
    public function getFileInfo($fkey) {
        $entry = config('app.qiniu.bucket').':'.$fkey;
        $encodedEntryURI = $this->base64UrlSafeEncode($entry);
        $url = 'https://rs.qbox.me/stat/'.$encodedEntryURI;
        $auth = new Auth(config('app.qiniu.accessKey'), config('app.qiniu.secretKey'));
        $headers = $auth->authorization($url);
        $client = Client::get($url, $headers);
        return $client->json();
    }


}
