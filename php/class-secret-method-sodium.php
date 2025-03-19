<?php

namespace WPElevator\Encrypted_Secrets;

use ParagonIE_Sodium_Compat;

class Secret_Method_Sodium extends Secret_Method {
	public static function get_type(): string {
		return 'sodium_crypto_secretbox';
	}

	public static function is_supported(): bool {
		return self::has_sodium_compat() || self::has_native_sodium();
	}

	private static function has_native_sodium(): bool {
		return function_exists( 'sodium_crypto_secretbox_keygen' ) && function_exists( 'sodium_crypto_secretbox' );
	}

	private static function has_sodium_compat(): bool {
		return class_exists( ParagonIE_Sodium_Compat::class, false );
	}

	private function generate_secret(): ?string {
		if ( self::has_native_sodium() ) {
			return sodium_crypto_secretbox_keygen();
		} elseif ( self::has_sodium_compat() ) {
			return ParagonIE_Sodium_Compat::crypto_secretbox_keygen();
		}

		return null;
	}

	public function encrypt( string $value, Secret_Storage $storage ): string {
		if ( ! self::is_supported() || ! $storage->is_supported() ) {
			return $value;
		}

		$secret_key = $storage->get();

		if ( empty( $secret_key ) ) {
			$secret_key = $this->generate_secret();

			// Skip encryption if we can't persist the secret.
			if ( $secret_key && ! $storage->set( $secret_key ) ) {
				return $value;
			}
		}

		if ( ! empty( $secret_key ) ) {
			if ( self::has_native_sodium() ) {
				$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
				$encrypted = sodium_crypto_secretbox( $value, $nonce, $secret_key );
			} elseif ( self::has_sodium_compat() ) {
				$nonce = random_bytes( ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );
				$encrypted = ParagonIE_Sodium_Compat::crypto_secretbox( $value, $nonce, $secret_key );
			}

			if ( $encrypted ) {
				return base64_encode( $nonce . $encrypted );
			}
		}

		return $value;
	}

	public function decrypt( string $value, Secret_Storage $storage ): string {
		$encrypted = base64_decode( $value );
		$secret_key = $storage->get();

		if ( ! $encrypted || empty( $secret_key ) ) {
			return $value;
		}

		if ( self::has_native_sodium() ) {
			$nonce = substr( $encrypted, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$ciphertext = substr( $encrypted, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			if ( ! empty( $nonce ) && ! empty( $ciphertext ) ) {
				return sodium_crypto_secretbox_open( $ciphertext, $nonce, $secret_key );
			}
		} elseif ( self::has_sodium_compat() ) {
			$nonce = substr( $encrypted, 0, ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );
			$ciphertext = substr( $encrypted, ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES );

			if ( ! empty( $nonce ) && ! empty( $ciphertext ) ) {
				return ParagonIE_Sodium_Compat::crypto_secretbox_open( $ciphertext, $nonce, $secret_key );
			}
		}

		return $value;
	}
}
