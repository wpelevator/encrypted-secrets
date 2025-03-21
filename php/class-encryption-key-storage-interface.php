<?php

namespace WPElevator\Encrypted_Secrets;

interface Encryption_Key_Storage_Interface {
	/**
	 * If the storage method is supported.
	 *
	 * @return bool
	 */
	public function is_supported(): bool;

	/**
	 * Store a secret key.
	 *
	 * @param string $secret_key Secret key to store.
	 *
	 * @return bool If the key was stored.
	 */
	public function set_key( string $secret_key ): bool;

	/**
	 * Retrieve a secret key.
	 *
	 * @return string|null Secret key or null if not present.
	 */
	public function get_key(): ?string;
}
