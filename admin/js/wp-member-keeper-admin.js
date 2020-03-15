(function( $ ) {
	'use strict';

	if ( document.querySelector( '.tab-item' ) ) {
		var tabs = document.querySelectorAll( '.tab-item' );

		tabs.forEach( function( tab, i ) {
			tab.addEventListener( 'click', function( e ) {
				var target = this;
				var data_target = target.getAttribute( 'data-target' );
				console.log( data_target );
				var current_active_tab = document.querySelector( '.tab-item.active' );
				var current_active_content = document.querySelector( '.tab-content.active' );
				if ( current_active_tab != target ) {
					current_active_tab.classList.remove( 'active' );
					target.classList.add( 'active' );
					current_active_content.classList.remove( 'active' );
					document.querySelector( ".tab-content[data-content=" + data_target + "]" ).classList.add( 'active' );
				}
			});
		});
	}

})( jQuery );
