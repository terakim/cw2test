<?php

//if ( ! class_exists( 'GFForms' ) ) die();

class AffWP_MLM_GF_Referrer_Field extends GF_Field {

	/**
	 * @var string $type The field type.
	 */
	public $type = 'referrer';

	/**
	 * Return the field title, for use in the form editor.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {

		$field_label = affiliate_wp()->settings->get( 'affwp_mlm_referrer_field_label', esc_attr__( 'Referrer', 'affiliatewp-multi-level-marketing' ) );

		return $field_label;
	}

	/**
	 * Assign the field button to the Advanced Fields group.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields', // 'affwp_mlm_fields', // 'affwp_fields', // 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * The settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'description_setting',
			'rules_setting',
			'placeholder_setting',
			'input_class_setting',
			'css_class_setting',
			'size_setting',
			'admin_label_setting',
			'default_value_setting',
			'visibility_setting',
			// 'conditional_logic_field_setting',
		);
	}

	/**
	 * Enable this field for use with conditional logic.
	 *
	 * @return bool

	public function is_conditional_logic_supported() {
		return true;
	}
	 */

	/**
	 * The scripts to be included in the form editor.
	 *
	 * @return string
	 */
	public function get_form_editor_inline_script_on_page_render() {

		// set the default field label for the simple type field
		$script = sprintf( "function SetDefaultValues_simple(field) {field.label = '%s';}", $this->get_form_editor_field_title() ) . PHP_EOL;

		// initialize the fields custom settings
		$script .= "jQuery(document).bind('gform_load_field_settings', function (event, field, form) {" .
		           "var inputClass = field.inputClass == undefined ? '' : field.inputClass;" .
		           "jQuery('#input_class_setting').val(inputClass);" .
		           "});" . PHP_EOL;

		// saving the simple setting
		$script .= "function SetInputClassSetting(value) {SetFieldProperty('inputClass', value);}" . PHP_EOL;

		return $script;
	}

	/**
	 * Define the fields inner markup.
	 *
	 * @param array $form The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$id              = absint( $this->id );
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		// Prepare the value of the input ID attribute.
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$value = esc_attr( $value );

		$ref_field = affiliate_wp()->settings->get( 'affwp_mlm_referrer_field' );

		// Get the currently tracked affiliate_id
		$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();
		$referrer = ! empty( $affiliate_id ) ? affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id ) : '';
		$disabled = ! empty( $referrer ) ? ' disabled="disabled"' : '';
		$referrer_val = ( ! empty( $referrer ) ) ? $referrer : $value;

		// Get the value of the inputClass property for the current field.
		$inputClass = $this->inputClass;

		// Prepare the input classes.
		$size         = $this->size;
		$class_suffix = $is_entry_detail ? '_admin' : '';
		$class        = $size . $class_suffix . ' ' . $inputClass;

		// Prepare the other input attributes.
		$tabindex              = $this->get_tabindex();
//		$logic_event           = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$required_attribute    = $ref_field == 'require' || $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$disabled_text 				 = empty( $disabled_text ) ? $disabled : "";

		// Prepare the input tag for this field.
		$input = "<input name='input_{$id}' id='{$field_id}' type='text' value='{$referrer_val}' class='{$class}' {$tabindex} {$placeholder_attribute} {$required_attribute} {$invalid_attribute} {$disabled_text}/>";

		if ( ! empty( $referrer ) ) {
			$input .= "<input type='hidden' name='input_{$id}' value='{$affiliate_id}'/>";
		}

		return sprintf( "<div class='ginput_container ginput_container_%s'>%s</div>", $this->type, $input );
	}
}

GF_Fields::register( new AffWP_MLM_GF_Referrer_Field() );
