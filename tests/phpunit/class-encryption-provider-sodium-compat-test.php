<?php

namespace WPElevator\Encrypted_Secrets_Tests;

use PHPUnit\Framework\TestCase;
use SodiumException;
use WPElevator\Encrypted_Secrets\Encryption_Provider_Sodium_Compat;
use WPElevator\Encrypted_Secrets\Encryption_Key_Storage_PHP_File;
use WPElevator\Encrypted_Secrets\Encryption_Key_Storage_Variable;

class Encryption_Provider_Sodium_Compat_Test extends TestCase {

	public function setUp(): void {
		if ( ! class_exists( 'ParagonIE_Sodium_Compat', false ) ) {
			$this->markTestSkipped( 'Sodium is not available' );
		}
	}

	public function test_get_type() {
		$provider = new Encryption_Provider_Sodium_Compat();
		$this->assertEquals( 'sodium_secretbox', $provider->get_id() );
	}

	public function test_is_supported() {
		$provider = new Encryption_Provider_Sodium_Compat();

		$this->assertTrue( $provider->is_supported(), 'Sodium method is supported' );
	}

	public function test_can_generate_valid_key() {
		$provider = new Encryption_Provider_Sodium_Compat();
		$generated_key = $provider->generate_key();
		$message = 'Encrypt this message';

		$encrypted = $provider->encrypt( $message, $generated_key );

		$this->assertEquals( $message, $provider->decrypt( $encrypted, $generated_key ), 'The value was encrypted decrypted with a generated key' );
	}

	public function test_encrypt_decrypt_with_missing_key() {
		$provider = new Encryption_Provider_Sodium_Compat();
		$storage = new Encryption_Key_Storage_Variable( '' );
		$message = 'Hello, World!';

		$this->expectException( SodiumException::class );
		$encrypted = $provider->encrypt( $message, $storage->get_key() );
	}

	public function test_encrypt_decrypt_with_key() {
		$message = 'Hello, World!';
		$private_key_base64 = 'CgqSFJ3VJnZwj8UHNg3pwGUV4XeIVGSBqNzyxBUAZhI=';
		$encrypted_valid = 's2OwZcNtCGthuqz3FlUqQ2yprqOBNlV1jUAaleacXVA1kM8ce0SvxamSldJ2BdFRSpDaWK0=';

		$provider = new Encryption_Provider_Sodium_Compat();
		$storage = new Encryption_Key_Storage_Variable( base64_decode( $private_key_base64 ) );

		$this->assertEquals( $message, $provider->decrypt( $encrypted_valid, $storage->get_key() ), 'The known encrypted message can be decrypted' );

		$another_encrypted = $provider->encrypt( $message, $storage->get_key() );
		$this->assertNotEmpty( $another_encrypted, 'Can encrypt using known key' );
		$this->assertNotEquals( $encrypted_valid, $another_encrypted, 'The encrypted value is different each time' );
	}

	public function test_encrypt_with_valid_secret_from_file() {
		$secret_storage = new Encryption_Key_Storage_PHP_File( __DIR__ . '/stubs/secret-sodium-valid-key.php' );
		$provider = new Encryption_Provider_Sodium_Compat();

		$this->assertNotEmpty( $secret_storage->get_key(), 'Valid secret was resolved from the file' );

		$encrypted = $provider->encrypt( 'encrypt this value', $secret_storage->get_key() );
		$this->assertEquals( 'encrypt this value', $provider->decrypt( $encrypted, $secret_storage->get_key() ), 'The value was encrypted and decrypted' );
	}

	public function test_encrypt_with_invalid_secret_from_file() {
		$secret_storage = new Encryption_Key_Storage_PHP_File( __DIR__ . '/stubs/secret-sodium-invalid-key.php' );
		$provider = new Encryption_Provider_Sodium_Compat();

		$this->assertNotEmpty( $secret_storage->get_key(), 'Invalid secret was resolved from the file' );

		$this->expectException( SodiumException::class );
		$provider->encrypt( 'encrypt this value', $secret_storage->get_key() );
	}
}
