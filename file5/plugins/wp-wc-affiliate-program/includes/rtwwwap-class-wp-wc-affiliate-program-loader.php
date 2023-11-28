<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Rtwwwap_Wp_Wc_Affiliate_Program
 * @subpackage Rtwwwap_Wp_Wc_Affiliate_Program/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwwap_Wp_Wc_Affiliate_Program_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwwwap_actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwwwap_actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwwwap_filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwwwap_filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->rtwwwap_actions = array();
		$this->rtwwwap_filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwwwap_hook             The name of the WordPress action that is being registered.
	 * @param    object               $rtwwwap_component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $rtwwwap_callback         The name of the function definition on the $rtwwwap_component.
	 * @param    int                  $rtwwwap_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwwwap_accepted_args    Optional. The number of arguments that should be passed to the $rtwwwap_callback. Default is 1.
	 */
	public function rtwwwap_add_action( $rtwwwap_hook, $rtwwwap_component, $rtwwwap_callback, $rtwwwap_priority = 10, $rtwwwap_accepted_args = 1 ) {
		$this->rtwwwap_actions = $this->rtwwwap_add( $this->rtwwwap_actions, $rtwwwap_hook, $rtwwwap_component, $rtwwwap_callback, $rtwwwap_priority, $rtwwwap_accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwwwap_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwwwap_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwwwap_callback         The name of the function definition on the $rtwwwap_component.
	 * @param    int                  $rtwwwap_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwwwap_accepted_args    Optional. The number of arguments that should be passed to the $rtwwwap_callback. Default is 1
	 */
	public function rtwwwap_add_filter( $rtwwwap_hook, $rtwwwap_component, $rtwwwap_callback, $rtwwwap_priority = 10, $rtwwwap_accepted_args = 1 ) {
		$this->rtwwwap_filters = $this->rtwwwap_add( $this->rtwwwap_filters, $rtwwwap_hook, $rtwwwap_component, $rtwwwap_callback, $rtwwwap_priority, $rtwwwap_accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $rtwwwap_hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $rtwwwap_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwwwap_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwwwap_callback         The name of the function definition on the $rtwwwap_component.
	 * @param    int                  $rtwwwap_priority         The priority at which the function should be fired.
	 * @param    int                  $rtwwwap_accepted_args    The number of arguments that should be passed to the $rtwwwap_callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function rtwwwap_add( $rtwwwap_hooks, $rtwwwap_hook, $rtwwwap_component, $rtwwwap_callback, $rtwwwap_priority, $rtwwwap_accepted_args ) {

		$rtwwwap_hooks[] = array(
			'rtwwwap_hook'          => $rtwwwap_hook,
			'rtwwwap_component'     => $rtwwwap_component,
			'rtwwwap_callback'      => $rtwwwap_callback,
			'rtwwwap_priority'      => $rtwwwap_priority,
			'rtwwwap_accepted_args' => $rtwwwap_accepted_args
		);

		return $rtwwwap_hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwwwap_run() {

		foreach ( $this->rtwwwap_filters as $rtwwwap_hook ) {
			add_filter( $rtwwwap_hook['rtwwwap_hook'], array( $rtwwwap_hook['rtwwwap_component'], $rtwwwap_hook['rtwwwap_callback'] ), $rtwwwap_hook['rtwwwap_priority'], $rtwwwap_hook['rtwwwap_accepted_args'] );
		}

		foreach ( $this->rtwwwap_actions as $rtwwwap_hook ) {
			add_action( $rtwwwap_hook['rtwwwap_hook'], array( $rtwwwap_hook['rtwwwap_component'], $rtwwwap_hook['rtwwwap_callback'] ), $rtwwwap_hook['rtwwwap_priority'], $rtwwwap_hook['rtwwwap_accepted_args'] );
		}

	}

}
