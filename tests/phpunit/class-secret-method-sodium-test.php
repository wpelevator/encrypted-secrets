<?php

namespace WPElevator\Encrypted_Secrets_Tests;

use PHPUnit\Framework\TestCase;
use SodiumException;
use WPElevator\Encrypted_Secrets\Secret_Method_Sodium;
use WPElevator\Encrypted_Secrets\Secret_Storage_File;
use WPElevator\Encrypted_Secrets\Secret_Storage_Variable;

class Secret_Method_Sodium_Test extends TestCase {
	public function test_get_type() {
		$this->assertEquals( 'sodium_crypto_secretbox', Secret_Method_Sodium::get_type() );
	}

	public function test_is_supported() {
		$this->assertTrue( Secret_Method_Sodium::is_supported(), 'Sodium method is supported' );
	}

	public function test_encrypt_decrypt_with_missing_key() {
		$method = new Secret_Method_Sodium();
		$storage = new Secret_Storage_Variable( '' );

		$message = 'Hello, World!';
		$encrypted = $method->encrypt( $message, $storage );

		$this->assertIsString( $storage->get(), 'A secret was generated and stored on-demand' );
		$this->assertNotEmpty( $encrypted, 'The encrypted value is present' );
		$this->assertNotEquals( $message, $encrypted, 'The encrypted value does not match the original' );
		$this->assertEquals( $message, $method->decrypt( $encrypted, $storage ), 'The value was decrypted' );
	}

	public function test_encrypt_decrypt_with_key() {
		$message = 'Hello, World!';
		$private_key_base64 = 'CgqSFJ3VJnZwj8UHNg3pwGUV4XeIVGSBqNzyxBUAZhI=';
		$encrypted_valid = 's2OwZcNtCGthuqz3FlUqQ2yprqOBNlV1jUAaleacXVA1kM8ce0SvxamSldJ2BdFRSpDaWK0=';

		$method = new Secret_Method_Sodium();
		$storage = new Secret_Storage_Variable( base64_decode( $private_key_base64 ) );

		$this->assertEquals( $message, $method->decrypt( $encrypted_valid, $storage ), 'The known encrypted message can be decrypted' );

		$another_encrypted = $method->encrypt( $message, $storage );
		$this->assertNotEmpty( $another_encrypted, 'Can encrypt using known key' );
		$this->assertNotEquals( $encrypted_valid, $another_encrypted, 'The encrypted value is different each time' );
	}

	public function test_encrypt_with_valid_secret_from_file() {
		$secret_storage = new Secret_Storage_File( __DIR__ . '/stubs/secret-sodium-valid-key.php' );
		$method = new Secret_Method_Sodium();

		$this->assertNotEmpty( $secret_storage->get(), 'Valid secret was resolved from the file' );

		$encrypted = $method->encrypt( 'encrypt this value', $secret_storage );
		$this->assertEquals( 'encrypt this value', $method->decrypt( $encrypted, $secret_storage ), 'The value was encrypted and decrypted' );
	}

	public function test_encrypt_with_invalid_secret_from_file() {
		$secret_storage = new Secret_Storage_File( __DIR__ . '/stubs/secret-sodium-invalid-key.php' );
		$method = new Secret_Method_Sodium();

		$this->assertNotEmpty( $secret_storage->get(), 'Invalid secret was resolved from the file' );

		$this->expectException( SodiumException::class );
		$method->encrypt( 'encrypt this value', $secret_storage );
	}
}
