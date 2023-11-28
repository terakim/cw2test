/**
 * Affiliate Area Creative functions.
 *
 * @since 2.16.0
 */

'use strict';

/* eslint-disable no-console, no-undef, jsdoc/no-undefined-types */
( function() {

	const affiliateWPCreatives = {

		/**
		 * Backend data.
		 *
		 * @since 2.16.0
		 *
		 * @type {Object}
		 */
		data: {},

		/**
		 * Creatives page selectors.
		 *
		 * @since 2.16.0
		 *
		 * @type {Object}
		 */
		selectors: {
			creativesViewId: 'affwp-creatives-view',
			modalPreviewId: 'affwp-creatives-modal',
			modalContainerId: 'affwp-modal-container',
			creativeListItem: '.affwp-creatives-list-item.affwp-creatives-list-body',
			creativeClickableColumns: '.affwp-creatives-item-actions',
			copyActionButton: '.affwp-creatives-list-action[data-action="copy"]',
			viewDetailsActionButton: '.affwp-creatives-list-action[data-action="view-details"]',
			modalItem: '[data-modal]',
			copyTextarea: '.affwp-copy-textarea-content',
			creativeClone: '.affwp-creative-clone',
			copyTooltip: 'button[data-action="copy"]:not(.affwp-tooltip-initialized)'
		},

		/**
		 * Used to better control the flow of some events.
		 *
		 * Don't use directly, this if for private purposes only.
		 *
		 * @since 2.16.0
		 * @type {null|WeakMap}
		 */
		eventHandlers: null,

		/**
		 * Initiate.
		 *
		 * @since 2.16.0
		 */
		init() {

			// Get the back-end data.
			this.data = affiliatewp.extend( 'affiliatewpCreativesData' );

			// Store event handlers.
			this.eventHandlers = new WeakMap();

			// Setup all modals.
			this.setupModals();

			// Setup pagination.
			this.setupInfiniteScroll();

			// Bind other actions.
			this.initActions();
		},

		/**
		 * Get the creative ID from hash, if exists.
		 *
		 * @since 2.16.0
		 * @return {number} The Creative ID.
		 */
		getCreativeIDFromHash() {

			const matches = window.location.hash.match( /^#creative-(\d+)$/ );

			return matches ? parseInt( matches[1] ) : 0;
		},

		/**
		 * Initiate modal events.
		 *
		 * @since 2.16.0
		 */
		setupModals() {

			if ( ! affiliatewp.has( 'modal' ) ) {
				throw new Error( 'Missing modal script. Ensure affiliatewp.modal is loaded correctly.' );
			}

			if ( ! document.getElementById( this.selectors.creativesViewId ) ) {
				return; // Bail if no creatives found.
			}

			// Check if it is attempting to load a creative modal on page load.
			let creativeID = this.getCreativeIDFromHash();

			// If the creative exists, set to null, so we prevent the creation of the temporary creative.
			if ( creativeID && document.querySelector( `[data-slug="creative-${creativeID}"]` ) ) {
				creativeID = null;
			}

			// Add a temporary creative element, so the modal can be loaded even if the creative was not loaded yet.
			if ( creativeID && this.data.hasOwnProperty( 'creativeAjaxUrl' ) ) {

				// We insert a temp element so Fancybox will be able to load it using the hash plugin.
				document.getElementById( this.selectors.creativesViewId ).insertAdjacentHTML(
					'afterbegin',
					`<span
							class="${this.selectors.creativeClone.replace('.', '')}"
							data-grouped
							data-modal
							data-type="ajax"
							data-src="${this.data.creativeAjaxUrl + creativeID}"
							data-slug="creative-${creativeID}"
							style="display: none"
						  ></span>`
				)
			}

			affiliatewp.modal
				.onInit( () => {

					const clone = document.querySelector( this.selectors.creativeClone );

					if ( ! clone ) {
						return;
					}

					// Once the modal open, we get rid of the temporary creative to avoid conflicts.
					document.querySelector( '.affwp-creative-clone' ).remove();
				} )
				.onLoading( () => {
					this.hideAllTooltips();
				} )
				.bind(
					document.getElementById( this.selectors.creativesViewId ),
					this.selectors.modalItem,
					{
						groupAll: true,
						showThumbs: false,
						dragToClose: false,
						draggable: false,
						autoFocus: false,
						idle: false,
						parentEl: document.getElementById( this.selectors.modalContainerId ),

					}
				);
		},

		/**
		 * Setup infinite scroll.
		 *
		 * @since 2.16.0
		 */
		setupInfiniteScroll() {

			if ( ! affiliatewp.has( 'infiniteScroll' ) ) {
				throw new Error( 'Missing infiniteScroll script. Ensure affiliatewp.infiniteScroll is loaded correctly.' );
			}

			const elementToObserve = document.getElementById( this.selectors.creativesViewId );

			if ( ! elementToObserve ) {
				return; // Bail if the expected Element is not present in this page.
			}

			affiliatewp.infiniteScroll.setup(
				elementToObserve,
				this.data.hasOwnProperty( 'page' ) ? this.data.page : 1,
				this.data.hasOwnProperty( 'itemsPerPage' ) ? this.data.itemsPerPage : 30,
				{
					maxPages: this.data.hasOwnProperty( 'maxPages' ) ? this.data.maxPages : -1,
					triggerElementHTML: '<div class="affwp-spinner"><svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"></circle><circle cx="25" cy="25" r="20"></circle></svg></div>',
					ajax: {
						action: 'affwp_creatives_load_more',
						nonce: this.data.hasOwnProperty( 'nonce' ) ? this.data.nonce : '',
						data: this.data.hasOwnProperty( 'queryArgs' ) ? affiliatewp.parseArgs( this.data.queryArgs ) : {}
					},
					on: {
						loadMore: () => {
							this.makeClickableItems();
						}
					}
				}
			);
		},

		/**
		 * Return if the clipboard allows copying for this site.
		 *
		 * @since 2.16.0
		 *
		 * @return {boolean} Whether is enabled or not.
		 */
		isCopyEnabled() {
			return !! ( navigator && navigator.clipboard );
		},

		/**
		 * Initiate action events.
		 *
		 * @since 2.16.0
		 */
		initActions() {

			// Auto select the textarea content for copy sections when clicking on it.
			document
				.getElementById( this.selectors.modalContainerId )
				.addEventListener( 'click', ( event ) => {

					if ( event.target && ! event.target.matches( this.selectors.copyTextarea ) ) {
						return; // Bail if it is not a copy textarea field.
					}

					event.target.focus();
					event.target.select();
				} );

			// Copy action.
			document.addEventListener( 'submit', ( event ) => {

				if ( event.target && event.target.name !== 'affiliatewp_copy_form' ) {
					return; // Bail if it is not a copy textarea.
				}

				event.preventDefault();

				this.handleCopyContent( event.target );
			} );

			// Handle view details click for other columns.
			this.makeClickableItems();
		},

		/**
		 * Make columns clickable, so users can click on the row to open modals.
		 *
		 * @since 2.16.0
		 */
		makeClickableItems() {

			document.querySelectorAll( this.selectors.creativeClickableColumns ).forEach( ( col ) => {

				if ( this.eventHandlers.has( col ) ) {
					return; // Bail if the event handler was already bound.
				}

				// Bind the event handler and store the reference in the map.
				const boundEventHandler = this.handleItemClick.bind( this, col );

				this.eventHandlers.set( col, boundEventHandler );

				// Bind the event on all visible items.
				col.addEventListener( 'click', boundEventHandler );
			} );
		},

		/**
		 * Trigger the modal when clicking on any item column. Except by the actions column.
		 *
		 * @since 2.16.0
		 *
		 * @param {Element} col The column element.
		 */
		handleItemClick( col ) {
			col.parentNode.querySelector( this.selectors.viewDetailsActionButton ).click();
		},

		/**
		 * Hide all active tooltips.
		 *
		 * @since 2.16.0
		 */
		hideAllTooltips() {
			affiliatewp.tooltip.hideAll();
		},

		/**
		 * Display the copy tooltip message.
		 *
		 * @since 2.16.0
		 *
		 * @param {string} content The content to be displayed.
		 * @param {string} trigger The trigger event. Accepts: mouseenter, manual. Default: manual.
		 * @param {string} placement Tooltip position. Accepts: top, right, bottom, left or auto. Default: auto.
		 * @param {number} hideDelay Time in milliseconds before hiding the tooltip. Default: 5000.
		 */
		showCopyTooltip( content, trigger = 'manual', placement = 'auto', hideDelay = 5000 ) {

			affiliatewp.tooltip.show(
				this.selectors.copyTooltip,
				content,
				{
					trigger,
					placement,
					hideDelay
				}
			);
		},

		/**
		 * Handle the copy action from copy buttons.
		 *
		 * @since 2.16.0
		 *
		 * @param {Element} el The form element.
		 */
		handleCopyContent( el ) {

			const textarea = el.querySelector( this.selectors.copyTextarea );
			textarea.focus();
			textarea.select();

			// Copy could not be enabled if site is running on non-secure connection.
			if ( ! this.isCopyEnabled() ) {
				this.showCopyTooltip( this.data.i18n.copyDisabled );
			}

			this.copyContent(
				textarea.value,
				() => this.showCopyTooltip( this.data.i18n.copySuccess ),
				() => this.showCopyTooltip( this.data.i18n.copyError ),
			);
		},

		/**
		 * Copy contents.
		 *
		 * @since 2.16.0
		 *
		 * @param {string} content The content to copy.
		 * @param {Function} successCallback A success callback function.
		 * @param {Function} errorCallback A error callback function.
		 */
		copyContent( content, successCallback, errorCallback ) {

			if ( ! this.isCopyEnabled() ) {
				console.error( 'Copy is disabled for an unknown reason.' );
				return; // Copy is not enabled in this browser.
			}

			navigator.clipboard.writeText(content)
				.then( () => {
					if ( successCallback && typeof successCallback === 'function' ) {
						successCallback();
					}
				} )
				.catch( () => {
					if ( errorCallback && typeof errorCallback === 'function' ) {
						errorCallback();
					}
				} );
		}
	}

	affiliateWPCreatives.init();
} )();

