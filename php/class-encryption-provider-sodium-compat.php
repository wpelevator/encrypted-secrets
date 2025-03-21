<?php

namespace WPElevator\Encrypted_Secrets;

use ParagonIE_Sodium_Compat;

class Encryption_Provider_Sodium_Compat implements Encryption_Provider_Interface {
	public function get_id(): string {
		return 'sodium_secretbox';
	}

	public function is_supported(): bool {
		return class_exists( ParagonIE_Sodium_Compat::class, false );
	}

	public function generate_key(): string {
		try {
			return ParagonIE_Sodium_Compat::crypto_secretbox_keygen();
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to generate key: ' . $e->getMessage(), $e->getCode(), $e );
		}
	}

	public function encrypt( string $plaintext, string $encryption_key ): string {
		try {
			$nonce = random_bytes( ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );
			$encrypted = ParagonIE_Sodium_Compat::crypto_secretbox( $plaintext, $nonce, $encryption_key );
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to encrypt: ' . $e->getMessage(), $e->getCode(), $e );
		}

		return base64_encode( $nonce . $encrypted );
	}

	public function decrypt( string $ciphertext, string $encryption_key ): string {
		$encrypted = base64_decode( $ciphertext );

		try {
			$nonce = substr( $encrypted, 0, ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );
			$ciphertext = substr( $encrypted, ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );

			return ParagonIE_Sodium_Compat::crypto_secretbox_open( $ciphertext, $nonce, $encryption_key );
		} catch ( \Error $e ) {
			throw new Encryption_Provider_Exception( 'Failed to decrypt: ' . $e->getMessage(), $e->getCode(), $e );
		}
	}
}
