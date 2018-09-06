<?php  
namespace extend;

/** 
* 3DES加解密类 
* @Author: 黎志斌 
* @version: v1.0 
* 2016年7月21日 
*/  
class Crypt3Des {
      /**
     * @param  string $crypt 需要加密的字符串
     * @param  string $key   加密使用的密钥
     * @param  string $vi    加密使用的向量
     * @return string $crypt 加密后的字符串
     * @des 3DES加密
     */
    final static public function encrypt($input, $key, $iv, $base64 = true) {
        $size = 8;
        $input = self::pkcs5_pad($input, $size);
        $encryption_descriptor = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        mcrypt_generic_init($encryption_descriptor, $key, $iv);
        $data = mcrypt_generic($encryption_descriptor, $input);
        mcrypt_generic_deinit($encryption_descriptor);
        mcrypt_module_close($encryption_descriptor);
        return base64_encode($data);
    }
    /**
     * @param  string $crypt 需要解密的字符串
     * @param  string $key   加密使用的密钥
     * @param  string $vi    加密使用的向量
     * @return string $input 解密后的字符串
     * @des 3DES解密
     */
    final static public function decrypt($crypt, $key, $iv, $base64 = true) {
        $crypt = base64_decode($crypt);
        $encryption_descriptor = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        mcrypt_generic_init($encryption_descriptor, $key, $iv);
        $decrypted_data = mdecrypt_generic($encryption_descriptor, $crypt);
        mcrypt_generic_deinit($encryption_descriptor);
        mcrypt_module_close($encryption_descriptor);
        $decrypted_data = self::pkcs5_unpad($decrypted_data);
        return rtrim($decrypted_data);
    }

    final static private function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    final static private function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)){
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}
