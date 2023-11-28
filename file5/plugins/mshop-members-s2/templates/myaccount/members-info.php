<?php
if ( 'yes' === get_option( 'mshop_members_use_role_application_rule', 'no' ) ) {
	wp_enqueue_style( 'msm_my_account', MSM()->plugin_url() . '/assets/css/frontend.css' );

	?>
    <table class="shop_table shop_table_responsive my_account_orders mshop-members-info">
        <tbody>
		<?php
		$rules = get_option( 'mshop_members_role_application_rules', array () );
		foreach ( $rules as $rule ) {
			if ( 'yes' == $rule['rule_enabled'] ) {
				if ( empty( $rule['role'] ) || in_array( $user_role, explode( ',', $rule['role'] ) ) ) {
					$conditions = msm_get( $rule, 'mms_conditions', array () );

					if ( empty( $conditions ) || MSM_Myaccount::check_additional_condition( $conditions ) ) {
						MSM_Myaccount::output_row( $rule );
					}
				}
			}
		}
		?>
        </tbody>
    </table>
	<?php
}