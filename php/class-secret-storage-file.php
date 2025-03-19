<?php

namespace WPElevator\Encrypted_Secrets;

class Secret_Storage_File extends Secret_Storage {
	protected string $file;

	public function __construct( string $file_path ) {
		$this->file = $file_path;
	}

	public function is_supported(): bool {
		return is_writable( dirname( $this->file ) );
	}

	public function set( string $secret_key ): bool {
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

	public function get(): ?string {
		if ( is_readable( $this->file ) ) {
			$secret = include $this->file;

			if ( is_array( $secret ) && ! empty( $secret['key'] ) ) {
				return base64_decode( $secret['key'] ) ?? null;
			}
		}

		return null;
	}
}
