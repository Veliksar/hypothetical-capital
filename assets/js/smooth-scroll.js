( function() {
	const animations = window.HCAnimations;

	if (
		typeof Lenis === 'undefined' ||
		typeof gsap === 'undefined' ||
		typeof ScrollTrigger === 'undefined'
	) {
		window.HCLenis = {
			isAvailable: false,
		};
		return;
	}

	if ( ! animations || ! animations.isAvailable || animations.prefersReducedMotion() ) {
		window.HCLenis = {
			isAvailable: false,
		};
		return;
	}

	gsap.registerPlugin( ScrollTrigger );

	const lenis = new Lenis();

	lenis.on( 'scroll', ScrollTrigger.update );

	gsap.ticker.add( function( time ) {
		lenis.raf( time * 1000 );
	} );

	gsap.ticker.lagSmoothing( 0 );

	window.HCLenis = {
		isAvailable: true,
		instance: lenis,
		scrollTo: function( target, options ) {
			lenis.scrollTo( target, options || {} );
		},
	};
}() );
