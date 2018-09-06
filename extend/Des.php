<?php
namespace extend;

class Des 
{
    public static function desEncode($str, $key) {
		/* Open module, and create IV */
		$size = 8;
        $str = self::pkcs5_pad($str, $size);
		$td = mcrypt_module_open('des', '', 'ecb', '');
		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
		$iv_size = mcrypt_enc_get_iv_size($td);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		/* Initialize encryption handle */
		if (mcrypt_generic_init($td, $key, $iv) === -1)
		{
		    return FALSE;
		}
		/* Encrypt data */
		$c_t = mcrypt_generic($td, $str);
		/* Clean up */
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return base64_encode( $c_t );
	}

	public static function desDecode($str, $key) {
		/* Open module, and create IV */
	    $td = mcrypt_module_open('des', '', 'ecb', '');
	    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
	    $iv_size = mcrypt_enc_get_iv_size($td);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    /* Initialize encryption handle */
	    if (mcrypt_generic_init($td, $key, $iv) === -1)
	    {
	    	return FALSE;
	    }
	    /* Reinitialize buffers for decryption */
	    $p_t = mdecrypt_generic($td, $str);
	    /* Clean up */
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return trim($p_t);
	}

	public  static function do_mencrypt($input, $key)
    {
        $input = str_replace("n", "", $input);
        $input = str_replace("t", "", $input);
        $input = str_replace("r", "", $input);
        $key = substr(md5($key), 0, 24);
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim(chop(base64_encode($encrypted_data)));
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