<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Rtwalwm_Wp_Wc_Affiliate_Program
 * @subpackage Rtwalwm_Wp_Wc_Affiliate_Program/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwalwm_Wp_Wc_Affiliate_Program_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwalwm_actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwalwm_actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $rtwalwm_filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $rtwalwm_filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->rtwalwm_actions = array();
		$this->rtwalwm_filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwalwm_hook             The name of the WordPress action that is being registered.
	 * @param    object               $rtwalwm_component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $rtwalwm_callback         The name of the function definition on the $rtwalwm_component.
	 * @param    int                  $rtwalwm_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwalwm_accepted_args    Optional. The number of arguments that should be passed to the $rtwalwm_callback. Default is 1.
	 */
	public function rtwalwm_add_action( $rtwalwm_hook, $rtwalwm_component, $rtwalwm_callback, $rtwalwm_priority = 10, $rtwalwm_accepted_args = 1 ) {
		$this->rtwalwm_actions = $this->rtwalwm_add( $this->rtwalwm_actions, $rtwalwm_hook, $rtwalwm_component, $rtwalwm_callback, $rtwalwm_priority, $rtwalwm_accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $rtwalwm_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwalwm_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwalwm_callback         The name of the function definition on the $rtwalwm_component.
	 * @param    int                  $rtwalwm_priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $rtwalwm_accepted_args    Optional. The number of arguments that should be passed to the $rtwalwm_callback. Default is 1
	 */
	public function rtwalwm_add_filter( $rtwalwm_hook, $rtwalwm_component, $rtwalwm_callback, $rtwalwm_priority = 10, $rtwalwm_accepted_args = 1 ) {
		$this->rtwalwm_filters = $this->rtwalwm_add( $this->rtwalwm_filters, $rtwalwm_hook, $rtwalwm_component, $rtwalwm_callback, $rtwalwm_priority, $rtwalwm_accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $rtwalwm_hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $rtwalwm_hook             The name of the WordPress filter that is being registered.
	 * @param    object               $rtwalwm_component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $rtwalwm_callback         The name of the function definition on the $rtwalwm_component.
	 * @param    int                  $rtwalwm_priority         The priority at which the function should be fired.
	 * @param    int                  $rtwalwm_accepted_args    The number of arguments that should be passed to the $rtwalwm_callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function rtwalwm_add( $rtwalwm_hooks, $rtwalwm_hook, $rtwalwm_component, $rtwalwm_callback, $rtwalwm_priority, $rtwalwm_accepted_args ) {

		$rtwalwm_hooks[] = array(
			'rtwalwm_hook'          => $rtwalwm_hook,
			'rtwalwm_component'     => $rtwalwm_component,
			'rtwalwm_callback'      => $rtwalwm_callback,
			'rtwalwm_priority'      => $rtwalwm_priority,
			'rtwalwm_accepted_args' => $rtwalwm_accepted_args
		);

		return $rtwalwm_hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function rtwalwm_run() {

		foreach ( $this->rtwalwm_filters as $rtwalwm_hook ) {
			add_filter( $rtwalwm_hook['rtwalwm_hook'], array( $rtwalwm_hook['rtwalwm_component'], $rtwalwm_hook['rtwalwm_callback'] ), $rtwalwm_hook['rtwalwm_priority'], $rtwalwm_hook['rtwalwm_accepted_args'] );
		}

		foreach ( $this->rtwalwm_actions as $rtwalwm_hook ) {
			add_action( $rtwalwm_hook['rtwalwm_hook'], array( $rtwalwm_hook['rtwalwm_component'], $rtwalwm_hook['rtwalwm_callback'] ), $rtwalwm_hook['rtwalwm_priority'], $rtwalwm_hook['rtwalwm_accepted_args'] );
		}

	}

}
