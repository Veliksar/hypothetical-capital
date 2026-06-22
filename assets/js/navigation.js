/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	if ( ! button ) {
		return;
	}

	const panel = document.getElementById( 'mobile-menu-panel' );
	const menu = siteNavigation.querySelector( '#primary-menu, .menu' );
	const mobileBreakpoint = window.hypotheticalCapitalNavigation?.mobileBreakpoint ?? 768;
	const mobileMediaQuery = window.matchMedia( `(max-width: ${ mobileBreakpoint - 1 }px)` );

	if ( ! menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	function isMobileNav() {
		return mobileMediaQuery.matches;
	}

	function setMenuOpen( isOpen ) {
		siteNavigation.classList.toggle( 'toggled', isOpen );
		document.body.classList.toggle( 'mobile-nav-open', isOpen && isMobileNav() );
		button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );

		if ( panel ) {
			panel.setAttribute( 'aria-hidden', isOpen ? 'false' : 'true' );
		}
	}

	button.addEventListener( 'click', function() {
		setMenuOpen( ! siteNavigation.classList.contains( 'toggled' ) );
	} );

	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside && siteNavigation.classList.contains( 'toggled' ) ) {
			setMenuOpen( false );
		}
	} );

	const links = menu.getElementsByTagName( 'a' );
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );

		link.addEventListener( 'click', function() {
			if ( isMobileNav() ) {
				setMenuOpen( false );
			}
		} );
	}

	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	mobileMediaQuery.addEventListener( 'change', function() {
		if ( ! isMobileNav() ) {
			setMenuOpen( false );
		}
	} );

	function toggleFocus( event ) {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;

			while ( ! self.classList.contains( 'nav-menu' ) ) {
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();

			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}

			menuItem.classList.toggle( 'focus' );
		}
	}
}() );
