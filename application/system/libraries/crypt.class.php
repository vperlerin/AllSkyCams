<?php

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Crypt {
    function encrypt($plaintext) {
        $td = mcrypt_module_open(CRYPT_ALGO, null, CRYPT_MODE, null);
        mcrypt_generic_init($td, ENC_KEY, CRYPT_IV);
        $blocksize = mcrypt_enc_get_block_size($td);

        $len = strlen($plaintext);
        $padsize = $len % $blocksize;

        if ($padsize > 0) {
            $padsize = $blocksize - $padsize;
            $totalsize = $len + $padsize;
        } else {
            $padsize = 8;
            $totalsize = $len + $blocksize;
        }

        $padtext = str_pad($plaintext, $totalsize, chr($padsize));

        $crypttext = mcrypt_generic($td, $padtext);
        mcrypt_generic_deinit($td);

        return array_shift(unpack('H*', $crypttext));
    }

    function decrypt($cipher) {
        $td = mcrypt_module_open(CRYPT_ALGO, null, CRYPT_MODE, null);
        mcrypt_generic_init($td, ENC_KEY, CRYPT_IV);
        $plaintext = rtrim(mdecrypt_generic($td, pack('H*', $cipher)), "\0\1\2\3\4\5\6\7\8\9");

        return $plaintext;
    }
}
