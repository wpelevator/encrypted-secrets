<?php

namespace WPElevator\Encrypted_Secrets;

class Encryption_Key_Storage_Variable implements Encryption_Key_Storage_Interface {

	private string $secret_key;

	public function __construct( string $secret_key ) {
		$this->secret_key = $secret_key;
	}

	public function is_supported(): bool {
		return true; // Always supported.
	}

	public function set_key( string $secret_key ): bool {
		$this->secret_key = $secret_key;

		return true; // Can be adjusted.
	}

	public function get_key(): ?string {
		return $this->secret_key;
	}
}
