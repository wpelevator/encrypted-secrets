<?php

namespace WPElevator\Encrypted_Secrets;

abstract class Secret_Storage {
	abstract public function is_supported(): bool;
	abstract public function set( string $secret_key ): bool;
	abstract public function get(): ?string;
}
