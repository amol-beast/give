<?php
/**
 * Stepper Form Template
 */
ob_start(); ?>

<div class="give-form-wrapper give-display-style-stepper">
	<div class="give-stepper-wrapper give-clearfix">
		<div class="give-progress-bar">
			<span style="width:0;"></span>
		</div>
	</div>
	<form action="{{form_action}}" method="{{form_method}}" {{form_attributes}}>
		{{form_fields}}
	</form>
	<div class="give-pagination-wrapper">
		<button class="give-prev give-hidden">Previous</button>
		<button class="give-next give-hidden">Next</button>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		var children = jQuery( '> *', '.give-form' ).not('input[type="hidden"], .give-hidden'),
			progressbar = jQuery('.give-progress-bar span', '.give-stepper-wrapper');

		// Bailout.
		if( 1 > children.length ) {
			return;
		}

		var paginationWrapper = jQuery( '.give-pagination-wrapper' ),
			nextButton = jQuery( '.give-next', paginationWrapper ),
			prevButton = jQuery( '.give-prev', paginationWrapper ),
			lastSectionIndex = 0,
			currentSectionIndex = 0,
			lastSection = {},
			currentSection = {};

		// Show pages.
		nextButton.removeClass('give-hidden');

		setProcess();

		// Hide all children expect first.
		jQuery.each( children, function( index, child ){
			if( ! index ) {
				currentSection = child;
				return;
			}

			child = jQuery(child);

			child.hide();
		});

		nextButton.on( 'click', function(){
			nextPage();
		});

		prevButton.on( 'click', function(){
			prevPage();
		});

		/**
		 * Helper functions
		 */

		/**
		 * Update progress bar
		 * @param step
		 */
		function setProcess( step ){
			step = typeof step === "undefined"
				? 1 // Default value
				: parseInt(step) + 1 ;
			progressbar.css({width: ( step/children.length * 100 ) + '%'})
		}

		/**
		 * Load next page
		 */
		function nextPage(){
			lastSection = jQuery( currentSection );
			lastSectionIndex = currentSectionIndex;
			currentSectionIndex = parseInt( currentSectionIndex ) + 1;
			currentSection = jQuery( children[ currentSectionIndex ] );

			setProcess( currentSectionIndex );

			lastSection.hide();
			currentSection.show();

			// Show hide prev button.
			prevButton.removeClass('give-hidden');

			// Hide next page if we are on last section.
			if( ( currentSectionIndex + 1 )  === parseInt( children.length ) ) {
				nextButton.addClass('give-hidden');
			}
		}

		/**
		 * Load prev page
		 */
		function prevPage(){
			lastSection = jQuery( currentSection );
			lastSectionIndex = currentSectionIndex;
			currentSectionIndex = parseInt( currentSectionIndex ) - 1;

			setProcess( currentSectionIndex );

			currentSection = jQuery( children[ currentSectionIndex ] );

			lastSection.hide();
			currentSection.show();

			nextButton.removeClass('give-hidden');

			// Show hide prev button.
			if( ! currentSectionIndex ) {
				prevButton.addClass('give-hidden');
			}
		}
	});
</script>

<?php return ob_get_clean(); ?>
