( function () {
	var hero = document.querySelector( '.fc-block--hero' );
	if ( ! hero ) {
		return;
	}

	if ( ! window.HCAnimations || ! HCAnimations.isAvailable || HCAnimations.prefersReducedMotion() ) {
		hero.classList.add( 'is-hero-intro-ready' );
		return;
	}

	var media       = hero.querySelector( '.fc-block__media' );
	var header      = document.querySelector( '.site-header' );
	var lines       = hero.querySelectorAll( '.fc-block__grid-line' );
	var heading     = hero.querySelector( '.fc-block__content h1, .fc-block__content h2, .fc-block__content h3' );
	var description = hero.querySelector( '.fc-block__content p' );
	var cta         = hero.querySelector( '.fc-block__cta' );

	var revealEase          = 'power2.out';
	var mediaDuration       = 0.85;
	var headerDuration      = 0.55;
	var lineDuration        = 0.7;
	var lineStagger         = 0.24;
	var headingDuration     = 0.9;
	var descriptionDuration = 0.85;
	var ctaDuration         = 0.85;
	var revealTo            = {
		y: 0,
		ease: revealEase,
		clearProps: 'transform',
	};

	function wrapReveal( el ) {
		if ( ! el ) {
			return null;
		}

		var wrapper = document.createElement( 'div' );
		wrapper.className = 'fc-block__reveal';
		el.parentNode.insertBefore( wrapper, el );
		wrapper.appendChild( el );

		return el;
	}

	function hideForReveal( el ) {
		if ( ! el || ! el.parentElement ) {
			return;
		}

		var clipHeight = el.parentElement.offsetHeight;
		var buffer     = Math.max( 4, Math.round( clipHeight * 0.02 ) );

		gsap.set( el, { y: clipHeight + buffer } );
	}

	function runIntro() {
		hero.classList.add( 'is-hero-intro-ready' );

		gsap.set( lines, {
			scaleY: 0,
			transformOrigin: 'top center',
		} );

		if ( media ) {
			gsap.set( media, { autoAlpha: 0 } );
		}

		if ( header ) {
			gsap.set( header, { autoAlpha: 0 } );
		}

		wrapReveal( heading );
		wrapReveal( description );
		wrapReveal( cta );

		hideForReveal( heading );
		hideForReveal( description );
		hideForReveal( cta );

		var tl   = gsap.timeline();
		var step = 0;

		if ( media ) {
			tl.to( media, {
				autoAlpha: 1,
				duration: mediaDuration,
				ease: revealEase,
			}, step );
			step += mediaDuration;
		}

		if ( header ) {
			tl.to( header, {
				autoAlpha: 1,
				duration: headerDuration,
				ease: revealEase,
			}, step );
			step += headerDuration;
		}

		var linesStart    = step;
		var lastLineStart = linesStart + Math.max( 0, lines.length - 1 ) * lineStagger;

		tl.to( lines, {
			scaleY: 1,
			duration: lineDuration,
			ease: revealEase,
			stagger: lineStagger,
			clearProps: 'transform',
		}, linesStart );

		if ( heading ) {
			tl.to( heading, Object.assign( { duration: headingDuration }, revealTo ), lastLineStart );
		}

		if ( description ) {
			tl.to( description, Object.assign( { duration: descriptionDuration }, revealTo ), '+=0' );
		}

		if ( cta ) {
			tl.to( cta, Object.assign( { duration: ctaDuration }, revealTo ), '+=0' );
		}
	}

	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( runIntro );
	} else {
		runIntro();
	}
}() );
