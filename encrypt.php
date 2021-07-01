<?php
class KeyPair {
	private $pri_key;
	private $pub_key;
	private $cipher;
	private $iv;
	public function __construct($pri=null, $pub=null, $cipher='aes-256-ctr', $iv=null){
		$pair = openssl_pkey_new();
		if($pri == null) openssl_pkey_export($pair, $this->pri_key);
		else $this->pri_key = openssl_get_privatekey($pri);
		$this->pub_key = $pub == null ? openssl_pkey_get_details($pair)['key'] : openssl_get_publickey($pub);
		$this->cipher  = $cipher;
		$this->iv      = $iv == null ? openssl_random_pseudo_bytes(16) : $iv;
	}
	public function get_private(){ return $this->pri_key; }
	public function get_public(){ return $this->pub_key; }
	public function get_cipher(){ return $this->cipher; }
	public function get_iv(){ return $this->iv; }
	public function encrypt($unencrypted){
		$encrypted = openssl_encrypt(
			$unencrypted,
			$this->cipher,
			$this->get_private(),
			OPENSSL_ZERO_PADDING,
			$this->iv
		);
		return openssl_encrypt(
			$encrypted,
			$this->cipher,
			$this->get_public(),
			OPENSSL_ZERO_PADDING,
			$this->iv
		);
	}
	public function decrypt($encrypted){
		$decrypted = openssl_decrypt(
			$encrypted,
			$this->cipher,
			$this->get_public(),
			OPENSSL_ZERO_PADDING,
			$this->iv
		);
		return openssl_decrypt(
			$decrypted,
			$this->cipher,
			$this->get_private(),
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

