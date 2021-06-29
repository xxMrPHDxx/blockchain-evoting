<?php
    function sshEncodePublicKey($private) {
        $keyInfo = openssl_pkey_get_details($private);
        $buffer  = pack("N", 7) . "ssh-rsa" .
        sshEncodeBuffer($keyInfo['rsa']['e']) . 
        sshEncodeBuffer($keyInfo['rsa']['n']);
        return "ssh-rsa " . base64_encode($buffer);
    }
    function sshEncodeBuffer($buffer) {
        $len = strlen($buffer);
        if (ord($buffer[0]) & 0x80) {
            $len++;
            $buffer = "\x00" . $buffer;
        }
        return pack("Na*", $len, $buffer);
    }
    if(false){
        $keypair = openssl_pkey_new(array('private_key_bits' => 512, 'private_key_type' => OPENSSL_KEYTYPE_RSA));
        $private = openssl_pkey_get_private($keypair);
        openssl_pkey_export($private, $pem);
        $public  = sshEncodePublicKey($private);
    
        $crypted = null; $decrypted = null;
        openssl_private_encrypt ("Test 123!" , $crypted , $private);
        // echo "Encrypted: $crypted <br>";
        openssl_private_decrypt($crypted, $decrypted , $private);
        // echo "Decrypted: $decrypted <br>";
    }
?>