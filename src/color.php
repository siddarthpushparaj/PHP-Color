<?php


namespace projectcleverweb\color;

class color implements \Serializable {
	
	public $hex;
	public $rgb;
	public $hsl;
	
	public function __construct($color, string $type = '') {
		if (!empty($type)) {
			$type = static::get_color_type($color);
		}
		call_user_func([__CLASS__, 'import_'.$type], $color);
	}
	
	public function serialize() :string {
		return json_encode($this->rgb);
	}
	
	public function unserialize($serialized) {
		$unserialized = (array) json_decode((string) $serialized);
		regulate::rgb_array($unserialized);
		$this->import_rgb($unserialized);
	}
	
	public static function get_color_type($color) :string {
		if (is_array($color)) {
			return static::_get_array_type($color);
		} elseif (is_string($color) && regulate::_validate_hex_str($color)) {
			return 'hex';
		} elseif (is_int($color)) {
			return 'int';
		}
		return 'error';
	}
	
	protected static function _get_array_type(array $color) :string {
		if (empty(array_diff(['r', 'g', 'b'], array_keys($color)))) {
			return 'rgb';
		} elseif (empty(array_diff(['h', 's', 'l'], array_keys($color)))) {
			return 'hsl';
		} elseif (empty(array_diff(['c', 'm', 'y', 'k'], array_keys($color)))) {
			return 'cmyk';
		}
		return 'error';
	}
	
	protected static function import_error($color) {
		echo 'The value was not a color';
		exit;
	}
	
	public function import_rgb(array $color) {
		regulate::rgb_array($color);
		$this->rgb = $color;
		$this->hex = generate::rgb_to_hex($this->rgb);
		$this->hsl = new hsl($this->rgb);
	}
	
	public function import_hsl(array $color) {
		regulate::hsl_array($color);
		$this->hsl = new hsl($color);
		$this->rgb = generate::hsl_to_rgb($color['h'], $color['s'], $color['l']);
		$this->hex = generate::rgb_to_hex($this->rgb['r'], $this->rgb['g'], $this->rgb['b']);
	}
	
	public function import_hex(string $color) {
		regulate::hex($color);
		static::import_rgb(generate::hex_to_rgb($color));
	}
	
	public function import_int(int $color) {
		regulate::hex_int($color);
		static::import_hex(str_pad(base_convert($color, 10, 16), 6, '0', STR_PAD_LEFT));
	}
	
	public function import_cmyk(array $color) {
		regulate::cmyk_array($color);
		static::import_rgb(generate::cmyk_to_rgb($color['c'], $color['m'], $color['y'], $color['k']));
	}
}

