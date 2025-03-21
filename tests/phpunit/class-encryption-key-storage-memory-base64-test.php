<?php

namespace WPElevator\Encrypted_Secrets_Tests;

use PHPUnit\Framework\TestCase;
use WPElevator\Encrypted_Secrets\Encryption_Key_Storage_Memory_Base64;

class Encryption_Key_Storage_Memory_Base64_Test extends TestCase {
	public function test_is_supported() {
		$storage = new Encryption_Key_Storage_Memory_Base64( 'MEMORY_TEST' );

		$this->assertTrue( $storage->is_supported(), 'Memory storage is always supported' );
	}

	public function test_set() {
		$storage = new Encryption_Key_Storage_Memory_Base64( 'MEMORY_TEST' );

		$this->assertFalse( $storage->set_key( 'any-value' ), 'Memory values are immutable' );
	}

	public function test_default_get() {
		$storage = new Encryption_Key_Storage_Memory_Base64( 'MEMORY_TEST' );

		$this->assertNull( $storage->get_key(), 'No enviromment variable or constant is present' );
	}

	public function test_get_from_env() {
		putenv( sprintf( 'MEMORY_TEST_SECRET_ENV=%s', base64_encode( 'secret-env' ) ) );

		$storage = new Encryption_Key_Storage_Memory_Base64( 'MEMORY_TEST_SECRET_ENV' );

		$this->assertEquals( 'secret-env', $storage->get_key(), 'Secret is read from environment variable' );
	}

	public function test_get_from_constant() {
		define( 'MEMORY_TEST_SECRET_CONSTANT', base64_encode( 'secret-constant' ) );

		$storage = new Encryption_Key_Storage_Memory_Base64( 'MEMORY_TEST_SECRET_CONSTANT' );

		$this->assertEquals( 'secret-constant', $storage->get_key(), 'Secret is read from constant and decoded' );
	}
}
