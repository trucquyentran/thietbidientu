<?php

namespace WPCSN\formControl;

defined( 'ABSPATH' ) || exit;

class initialization {
	public $args;
	public $name_prefix = 'wpcsn';

	function set( $args = [] ) {
		$this->args = array_merge( [
			'name_prefix'  => $this->name_prefix,
			'class'        => '',
			'field_id'     => '',
			'field_name'   => '',
			'ajax'         => '',
			'settings'     => [],
			'instance'     => [],
			'old_instance' => [],
			'new_instance' => [],
			'number'       => '',
			'isWidget'     => false,
		], is_array( $args ) ? $args : [] );
	}

	public static function get_instance( $args ) {
		static $single;

		if ( ! isset( $single ) ) {
			$single = new self();
		}

		$single->set( $args );

		return $single;
	}

	public function formBuilding() {
		include_once 'form.php';

		return FromBuilding::initialization( $this->args );
	}

	public function sanitizeInstance() {
		include_once 'instance.php';

		return Instance::initialization( $this->args );
	}
}
