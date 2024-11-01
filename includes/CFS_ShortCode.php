<?php


class CFS_ShortCode {

	public function __construct() {
		include_once dirname( __FILE__ ) . '/tables/city.php';
		include_once dirname( __FILE__ ) . '/tables/district.php';
		include_once dirname( __FILE__ ) . '/tables/wards.php';
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		$this->shortcode_city();
		$this->shortcode_district();
		$this->shortcode_wards();
		$this->load_ajax();

	}

	public function load_scripts() {
		wp_enqueue_style( 'cfs-custom', CFS_URL . 'assets/css/custom.css' );
		wp_enqueue_script( 'cfs-custom', CFS_URL . 'assets/js/custom.js' );
		wp_localize_script( 'cfs-custom', 'cfs_data', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'cfs_security' )
		) );
	}

	public function load_ajax() {
		add_action( 'wp_ajax_cfs_load_district', array( $this, 'ajax_load_data_district' ) );
		add_action( 'wp_ajax_nopriv_cfs_load_district', array( $this, 'ajax_load_data_district' ) );
		add_action( 'wp_ajax_cfs_load_wards', array( $this, 'ajax_load_data_wards' ) );
		add_action( 'wp_ajax_nopriv_cfs_load_wards', array( $this, 'ajax_load_data_wards' ) );
	}

	/** Create shortcode select city
	 * @return void
	 * */
	public function shortcode_city() {
		wpcf7_add_shortcode( 'city', array( $this, 'shortcode_city_handle' ), true );
	}

	/** Generate shortcode select city HTML
	 *
	 * @param object $tag
	 *
	 * @return string
	 * */
	public function shortcode_city_handle( $tag ) {

		if ( empty( $tag->name ) ) {
			return '';
		}
		$cities = cfs_get_cities();

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['class']    = $tag->get_class_option( $class ) . ' cfs-country';
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$multiple      = $tag->has_option( 'multiple' );
		$include_blank = $tag->has_option( 'include_blank' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );

			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}

		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}

		$default_choice = $tag->get_default_option( null, array(
			'multiple' => $multiple,
			'shifted'  => $include_blank,
		) );

		$placeholder = (string) reset( $tag->values );
		$html        = "<option value='' data-id='0'>$placeholder</option>";
		$hangover    = wpcf7_get_hangover( $tag->name );

		foreach ( $cities as $key => $city ) {
			if ( $hangover ) {
				$selected = in_array( $city[1], (array) $hangover, true );
			} else {
				$selected = in_array( $city[1], (array) $default_choice, true );
			}

			$item_atts = array(
				'value'    => $city[1],
				'selected' => $selected ? 'selected' : '',
				'data-id'  => $city[0]
			);

			$item_atts = wpcf7_format_atts( $item_atts );

			$label = $city[1];

			$html .= sprintf( '<option %1$s>%2$s</option>',
				$item_atts, esc_html( $label ) );
		}

		if ( $multiple ) {
			$atts['multiple'] = 'multiple';
		}

		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
			sanitize_html_class( $tag->name ), $atts, $html, $validation_error );


		return $html;
	}

	/** Create shortcode select district
	 * @return void
	 * */
	public function shortcode_district() {
		wpcf7_add_shortcode( 'district', array( $this, 'shortcode_district_handle' ), true );
	}

	/** Generate shortcode select district
	 *
	 * @param object $tag
	 *
	 * @return string
	 * */
	public function shortcode_district_handle( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['class']    = $tag->get_class_option( $class ) . ' cfs-district';
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$multiple = $tag->has_option( 'multiple' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );

			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}

		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}
		$placeholder = (string) reset( $tag->values );

		$html = "<option value='' data-id='0'>$placeholder</option>";

		if ( $multiple ) {
			$atts['multiple'] = 'multiple';
		}

		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
			sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

		return $html;
	}

	/** Create shortcode select wards
	 * @return void
	 * */
	public function shortcode_wards() {
		wpcf7_add_shortcode( 'wards', array( $this, 'shortcode_wards_handle' ), true );
	}

	/** Generate shortcode select wards
	 *
	 * @param object $tag
	 *
	 * @return string
	 * */
	public function shortcode_wards_handle( $tag ) {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$validation_error = wpcf7_get_validation_error( $tag->name );

		$class = wpcf7_form_controls_class( $tag->type );

		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		$atts = array();

		$atts['class']    = $tag->get_class_option( $class ) . ' cfs-wards';
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$multiple = $tag->has_option( 'multiple' );

		if ( $tag->has_option( 'size' ) ) {
			$size = $tag->get_option( 'size', 'int', true );

			if ( $size ) {
				$atts['size'] = $size;
			} elseif ( $multiple ) {
				$atts['size'] = 4;
			} else {
				$atts['size'] = 1;
			}
		}

		if ( $data = (array) $tag->get_data_option() ) {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}
		$placeholder = (string) reset( $tag->values );

		$html = "<option value='' data-id='0'>$placeholder</option>";

		if ( $multiple ) {
			$atts['multiple'] = 'multiple';
		}

		$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
			sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

		return $html;
	}

	/** Load ajax when onchange select city
	 * @return void
	 * */
	public function ajax_load_data_district() {
		// Check sercurity
		check_ajax_referer( 'cfs_security', '_wp_nonce' );
		if ( isset( $_POST['cfs_city_id'] ) ) {
			$city_id = sanitize_text_field( $_POST['cfs_city_id'] );
			echo json_encode( [
				'data'   => $this->get_data_district_by_city( $city_id ),
				'status' => 200
			] );
		} else {
			echo json_encode( [
				'data'   => 'Có lỗi xảy ra. Vui lòng thử lại !',
				'status' => 401
			] );
		}

		die();
	}

	/** Get data district by city id
	 *
	 * @param integer $city_id
	 *
	 * @return string
	 * */
	public function get_data_district_by_city( $city_id = 1 ) {
		$districts = cfs_get_districts();
		$districts = array_filter( $districts, function ( $district ) use ( $city_id ) {
			if ( $district[3] == $city_id ) {
				return $district;
			}
		} );

		$html = "<option value='' data-id='0'> --- </option>";
		foreach ( $districts as $district ) {
			$district_id   = $district[0];
			$district_name = $district[1];

			$html .= "<option value='$district_name' data-id='$district_id'>$district_name</option>";
		}

		return $html;
	}

	/** Load ajax when onchange select city
	 * @return void
	 * */
	public function ajax_load_data_wards() {
		// Check sercurity
		check_ajax_referer( 'cfs_security', '_wp_nonce' );
		if ( isset( $_POST['cfs_district_id'] ) ) {
			$district_id = sanitize_text_field( $_POST['cfs_district_id'] );
			echo json_encode( [
				'data'   => $this->get_data_wards_by_district( $district_id ),
				'status' => 200
			] );
		} else {
			echo json_encode( [
				'data'   => 'Có lỗi xảy ra. Vui lòng thử lại !',
				'status' => 401
			] );
		}

		die();
	}

	/** Get data district by district id
	 *
	 * @param integer $district_id
	 *
	 * @return string
	 * */
	public function get_data_wards_by_district( $district_id = 1 ) {
		$wards = cfs_get_wards();
		$wards = array_filter( $wards, function ( $ward ) use ( $district_id ) {
			if ( $ward[3] == $district_id ) {
				return $ward;
			}
		} );

		$html = "<option value='' data-id='0'> --- </option>";
		foreach ( $wards as $ward ) {
			$ward_id   = $ward[0];
			$ward_name = $ward[1];

			$html .= "<option value='$ward_name' data-id='$ward_id'>$ward_name</option>";
		}

		return $html;
	}
}