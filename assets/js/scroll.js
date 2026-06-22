( function() {
	const animations = window.HCAnimations;
	const lenisScroll = window.HCLenis;
	const scrollTarget = document.scrollingElement || document.documentElement;
	let activeScrollTween = null;

	function prefersReducedMotion() {
		return animations && animations.prefersReducedMotion();
	}

	function getScrollDuration() {
		if ( ! animations || ! animations.isAvailable ) {
			return null;
		}

		return animations.getDuration( animations.motion.duration * 2 );
	}

	function getScrollEase() {
		return animations && animations.isAvailable ? animations.motion.fadeEase : null;
	}

	function scrollToY( targetY ) {
		const maxScroll = scrollTarget.scrollHeight - window.innerHeight;
		const y = Math.max( 0, Math.min( targetY, maxScroll ) );

		if ( activeScrollTween ) {
			activeScrollTween.kill();
			activeScrollTween = null;
		}

		if ( lenisScroll && lenisScroll.isAvailable ) {
			lenisScroll.scrollTo( y, {
				duration: getScrollDuration(),
			} );
			return;
		}

		if (
			typeof gsap !== 'undefined' &&
			animations &&
			animations.isAvailable &&
			! prefersReducedMotion()
		) {
			activeScrollTween = gsap.to( scrollTarget, {
				scrollTop: y,
				duration: getScrollDuration(),
				ease: getScrollEase(),
				onComplete: function() {
					activeScrollTween = null;
				},
			} );
			return;
		}

		window.scrollTo( {
			top: y,
			behavior: prefersReducedMotion() ? 'auto' : 'smooth',
		} );
	}

	function scrollToTop() {
		scrollToY( 0 );
	}

	function scrollToElement( element ) {
		if ( lenisScroll && lenisScroll.isAvailable ) {
			lenisScroll.scrollTo( element, {
				duration: getScrollDuration(),
			} );
			return;
		}

		scrollToY( element.getBoundingClientRect().top + window.pageYOffset );
	}

	function isSamePageAnchor( link ) {
		if ( ! link.hash || link.hash === '#' ) {
			return false;
		}

		const linkUrl = new URL( link.href, window.location.href );
		const currentUrl = new URL( window.location.href );

		return (
			linkUrl.origin === currentUrl.origin &&
			linkUrl.pathname === currentUrl.pathname &&
			linkUrl.search === currentUrl.search
		);
	}

	document.querySelectorAll( '[data-scroll-to-top]' ).forEach( function( button ) {
		button.addEventListener( 'click', function( event ) {
			event.preventDefault();
			scrollToTop();
		} );
	} );

	document.addEventListener( 'click', function( event ) {
		const link = event.target.closest( 'a[href]' );

		if ( ! link || ! isSamePageAnchor( link ) ) {
			return;
		}

		const target = document.querySelector( link.hash );

		if ( ! target ) {
			return;
		}

		event.preventDefault();
		scrollToElement( target );

		if ( window.history && window.history.pushState ) {
			window.history.pushState( null, '', link.hash );
		}
	} );
}() );
