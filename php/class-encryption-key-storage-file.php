<?php

namespace WPElevator\Encrypted_Secrets;

class Encryption_Key_Storage_File implements Encryption_Key_Storage_Interface {
	protected string $file;

	public function __construct( string $file_path ) {
		$this->file = $file_path;
	}

	public function is_supported(): bool {
		return is_writable( dirname( $this->file ) );
	}

	public function set_key( string $secret_key ): bool {
		$secret = [
			'key' => base64_encode( $secret_key ),
			'created' => time(),
		];

		$secret_stored = sprintf( '<?php return %s;', var_export( $secret, true ) );

		if ( $this->is_supported() ) {
			return (bool) file_put_contents( $this->file, $secret_stored );
		}

		return false;
	}

	public function get_key(): ?string {
		if ( is_readable( $this->file ) ) {
			$secret = include $this->file;

			if ( is_array( $secret ) && ! empty( $secret['key'] ) ) {
				return base64_decode( $secret['key'] ) ?? null;
			}
		}

		return null;
	}
}
