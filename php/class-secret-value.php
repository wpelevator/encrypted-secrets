<?php

namespace WPElevator\Encrypted_Secrets;

class Secret_Value {
	const PREFIX = 'wp_secret';

	const DELIMITER = ':';

	protected string $value;

	protected array $parts;

	protected ?string $encryption_type;

	protected string $prefix;

	protected string $delimiter;

	public function __construct( string $value, ?string $encryption_type = null, string $prefix = self::PREFIX, string $delimiter = self::DELIMITER ) {
		$this->value = $value;
		$this->encryption_type = $encryption_type;
		$this->prefix = $prefix;
		$this->delimiter = $delimiter;
	}

	public function is_encrypted(): bool {
		$parts = $this->get_parts();

		return count( $parts ) > 2 && self::PREFIX === $parts[0];
	}

	private function get_parts(): array {
		if ( ! isset( $this->parts ) ) {
			$this->parts = explode( self::DELIMITER, $this->value );
		}

		return $this->parts;
	}

	public function is_encryption_type( string $type ) {
		// Matches type and also has a valid value.
		return ( $type === $this->get_encryption_type() && $this->get_encrypted_value() );
	}

	public function get_encryption_type(): ?string {
		if ( ! isset( $this->encryption_type ) ) {
			$this->encryption_type = $this->get_parts()[1] ?? null;
		}

		return $this->encryption_type;
	}

	public function get_encrypted_value(): ?string {
		$parts = $this->get_parts();

		// Account for values that contain the delimiter.
		if ( count( $parts ) > 3 ) {
			return implode( self::DELIMITER, array_slice( $parts, 3 ) );
		}

		return $parts[2] ?? null;
	}

	public function get_value(): string {
		return implode( self::DELIMITER, $this->get_parts() );
	}
}
