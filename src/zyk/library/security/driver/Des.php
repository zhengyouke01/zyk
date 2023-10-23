<?php

namespace zyk\library\security\driver;

class Des
{
    /**
     * 加解密方法，可通过 openssl_get_cipher_methods()
     * @var   $method
     *      ECB DES-ECB、DES-EDE3 （为 ECB 模式时，$iv 为空即可）
     *      CBC DES-CBC、DES-EDE3-CBC、DESX-CBC
     *      CFB DES-CFB8、DES-EDE3-CFB8
     *      CTR
     *      OFB
     */
    protected $method = 'DES-CBC';
    protected $desKey;//密钥
    protected $output = 'HEX';//转码方式
    protected $options = OPENSSL_RAW_DATA;//填充模式pkcs7

    public function __construct($conf = [])
    {
        $this->desKey = $conf['key'];
        if (!empty($conf['output'])) $this->output = $conf['output'];
        if (!empty($conf['options'])) $this->options = $conf['options'];
        if (!empty($conf['method'])) $this->method = $conf['method'];
    }

    /**
     * 获取客户端所需DES动态秘钥
     * @param $data
     * @param $iv
     * @return false|string
     */
    //
    public function getKey($kid = 0)
    {
        $key = md5($this->desKey . '_' . date('YW'));
        if ($kid) {
            $key = md5($this->desKey . '_' . date('YW') . '_' . $kid);
        }
        return $key;
    }

    /**
     * 解密
     * @param $data
     * @param $desKey
     * @return false|string
     */
    public function decrypt($data, $desKey)
    {
        if ($this->output == 'HEX') {
            $data = hex2bin($data);
        } else if ($this->output == 'BASE64') {
            $data = base64_decode($data);
        }

        if (empty($this->desKey)) {
            $key = self::getKey();
        } else {
            $key = $desKey;
        }
        $iv = substr(md5($key), 0, 8);//取前8位
        $decrypted = openssl_decrypt($data, $this->method, $key, $this->options, $iv);
        return $decrypted;
    }

    /**
     * 加密
     * @param string $str
     * @param $desKey
     * @return string
     */
    public function encrypt($str, $desKey)
    {
        if (!empty($str)){
            if (empty($this->desKey)) {
                $key = self::getKey();
            } else {
                $key = $desKey;
            }
            $iv = substr(md5($key), 0, 8);//取前8位
            $data = openssl_encrypt($str, $this->method, $key, $this->options, $iv);
            if ($this->output == 'HEX') {
                $data = bin2hex($data);
            } else if ($this->output == 'BASE64') {
                $data = base64_encode($data);
            }
        }else{
            $data = $str;
        }
        return $data;
    }

}

