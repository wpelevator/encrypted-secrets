<?php

namespace WPElevator\Encrypted_Secrets;

abstract class Secret_Method {
	abstract public static function get_type(): string;
	abstract public static function is_supported(): bool;
	abstract public function encrypt( string $value, Secret_Storage $secret_storage ): string;
	abstract public function decrypt( string $value, Secret_Storage $secret_storage ): string;
}
