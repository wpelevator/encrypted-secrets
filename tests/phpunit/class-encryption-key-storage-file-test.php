<?php

namespace WPElevator\Encrypted_Secrets_Tests;

use PHPUnit\Framework\TestCase;
use WPElevator\Encrypted_Secrets\Encryption_Key_Storage_File;

class Encryption_Key_Storage_File_Test extends TestCase {

	public function test_writable_dir_is_supported() {
		$storage = new Encryption_Key_Storage_File( __DIR__ . '/stubs/secret-sodium-generated.php' );

		$this->assertTrue( $storage->is_supported(), 'Writable directory is supported' );

		$secret = 'this is a very secret';

		$this->assertTrue( $storage->set_key( $secret ), 'Secret can be stored stored' );
		$this->assertEquals( $secret, $storage->get_key(), 'Secret is stored and retrieved' );
	}

	public function test_non_writable_dir_is_not_supported() {
		$storage = new Encryption_Key_Storage_File( '/some/directory/here/far-away' );

		$this->assertFalse( $storage->is_supported(), 'Non-existant directory is not supported' );
	}
}
