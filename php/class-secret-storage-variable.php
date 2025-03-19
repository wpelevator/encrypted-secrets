<?php

namespace WPElevator\Encrypted_Secrets;

class Secret_Storage_Variable extends Secret_Storage {

	private string $secret_key;

	public function __construct( string $secret_key ) {
		$this->secret_key = $secret_key;
	}

	public function is_supported(): bool {
		return true; // Always supported.
	}

	public function set( string $secret_key ): bool {
		$this->secret_key = $secret_key;

		return true; // Can be adjusted.
	}

	public function get(): ?string {
		return $this->secret_key;
	}
}
