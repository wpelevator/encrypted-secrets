<?php

namespace WPElevator\Encrypted_Secrets;

class Encryption_Key_Storage_Memory_Base64 implements Encryption_Key_Storage_Interface {

	private string $constant_or_env_name;

	public function __construct( string $constant_or_env_name ) {
		$this->constant_or_env_name = $constant_or_env_name;
	}

	public function is_supported(): bool {
		return true; // Always supported.
	}

	public function set_key( string $secret_key ): bool {
		return false; // This is read-only.
	}

	public function get_key(): ?string {
		$secret = getenv( $this->constant_or_env_name );

		if ( empty( $secret ) && defined( $this->constant_or_env_name ) ) {
			$secret = constant( $this->constant_or_env_name );
		}

		if ( is_string( $secret ) ) {
			return base64_decode( $secret );
		}

		return null;
	}
}
