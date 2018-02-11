<?php
//私钥解密
function rsaDecryptData($ciphertext)
{
    $rsa_private_key = config('rsa_private_key');
    $cleartext = '';
    //判断密文长度，按照128字节分割，解密
    foreach (str_split(base64_decode($ciphertext), 128) as $chunk) {

        openssl_private_decrypt($chunk, $decryptData, $rsa_private_key);

        $cleartext .= $decryptData;
    }
    //openssl_private_decrypt(base64_decode($ciphertext), $cleartext, $rsa_private_key);
    if (!empty($cleartext)) {
        return $cleartext;
    } else {
        return false;
    }
}

/*function rsaEncryptionData($data)
{
    $client_rsa_public_key = config('client_rsa_public_key');
    $ciphertext = '';
    openssl_public_encrypt($data,$ciphertext,$client_rsa_public_key);
    if(!empty($ciphertext)){
        return base64_encode($ciphertext);
    }else{
        return false;
    }
    openssl_public_decrypt();
}*/