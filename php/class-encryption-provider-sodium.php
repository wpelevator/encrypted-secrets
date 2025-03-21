<?php

namespace WPElevator\Encrypted_Secrets;

class Encryption_Provider_Sodium implements Encryption_Provider_Interface {
	public function get_id(): string {
		return 'sodium_secretbox';
	}

	public function is_supported(): bool {
		return function_exists( 'sodium_crypto_secretbox_keygen' ) && function_exists( 'sodium_crypto_secretbox' );
	}

	public function generate_key(): string {
		try {
			return sodium_crypto_secretbox_keygen();
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to generate key: ' . $e->getMessage(), $e->getCode(), $e );
		}
	}

	public function encrypt( string $plaintext, string $encryption_key ): string {
		try {
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$encrypted = sodium_crypto_secretbox( $plaintext, $nonce, $encryption_key );
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to encrypt: ' . $e->getMessage(), $e->getCode(), $e );
		}

		return base64_encode( $nonce . $encrypted );
	}

	public function decrypt( string $ciphertext, string $encryption_key ): string {
		$encrypted = base64_decode( $ciphertext );

		try {
			$nonce = substr( $encrypted, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$ciphertext = substr( $encrypted, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			return sodium_crypto_secretbox_open( $ciphertext, $nonce, $encryption_key );
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to decrypt: ' . $e->getMessage(), $e->getCode(), $e );
		}
	}
}
