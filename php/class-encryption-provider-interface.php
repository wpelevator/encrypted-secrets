<?php

namespace WPElevator\Encrypted_Secrets;

interface Encryption_Provider_Interface {
	/**
	 * Get the provider identifier.
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Is the provider supported by the current environment?
	 *
	 * @return bool
	 */
	public function is_supported(): bool;

	/**
	 * Generate a new encryption key. Be sure to check if the
	 * provider is supported before calling this method.
	 *
	 * @throws Encryption_Provider_Exception If failed to generate a key.
	 *
	 * @return string
	 */
	public function generate_key(): string;

	/**
	 * Encrypt plain text using an encryption key.
	 *
	 * @param string $plaintext Plain text to encrypt.
	 * @param string $key Encryption key to use.
	 *
	 * @throws Encryption_Provider_Exception If failed to encrypt.
	 *
	 * @return string The encrypted text.
	 */
	public function encrypt( string $plaintext, string $key ): string;

	/**
	 * Decrypt ciphertext using an encryption key.
	 *
	 * @param string $ciphertext Encrypted text to decrypt.
	 * @param string $key Encryption key to use.
	 *
	 * @throws Encryption_Provider_Exception If failed to decrypt.
	 *
	 * @return string The decrypted text.
	 */
	public function decrypt( string $ciphertext, string $key ): string;
}
