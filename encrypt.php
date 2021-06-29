<?php
class KeyPair {
	private $pkey;
	private $cipher;
	private $iv;
	public function __construct($key=null, $cipher='aes-256-ctr', $iv=null){
		$this->pkey   = $key == null ? openssl_pkey_new() : openssl_get_publickey($key);
		$this->cipher = $cipher;
		$this->iv     = $iv == null ? openssl_random_pseudo_bytes(16) : $iv;
	}
	public function __destruct(){
		openssl_free_key($this->pkey);
	}
	public function get_key(){
		$key = openssl_get_privatekey($this->pkey);
		return openssl_pkey_get_details($key)['key'];
	}
	public function get_cipher(){ return $this->cipher; }
	public function get_iv(){ return $this->iv; }
	public function encrypt($unencrypted){
		return openssl_encrypt(
			$unencrypted,
			$this->cipher,
			$this->get_key(),
			OPENSSL_ZERO_PADDING,
			$this->iv
		);
	}
	public function decrypt($encrypted){
		return openssl_decrypt(
			$encrypted,
			$this->cipher,
			$this->get_key(),
			OPENSSL_ZERO_PADDING,
			$this->iv
		);
	}
	public function sign($data){
		$signature = null;
		openssl_sign(
			$data,
			$signature,
			$this->pkey,
			OPENSSL_ALGO_SHA256
		);
		return $signature;
	}
	public function verify($data, $signature){
		return openssl_verify(
			$data,
			$signature,
			$this->get_key(),
			OPENSSL_ALGO_SHA256
		);
	}
};
?>

