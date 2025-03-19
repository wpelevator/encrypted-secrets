<?php

namespace WPElevator\Encrypted_Secrets;

class Secret_Storage_Memory_Base64 extends Secret_Storage {

	private string $constant_or_env_name;

	public function __construct( string $constant_or_env_name ) {
		$this->constant_or_env_name = $constant_or_env_name;
	}

	public function is_supported(): bool {
		return true; // Always supported.
	}

	public function set( string $secret_key ): bool {
		return false; // This is read-only.
	}

	public function get(): ?string {
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
