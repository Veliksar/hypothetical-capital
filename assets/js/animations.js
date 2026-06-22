( function() {
	if ( typeof gsap === 'undefined' ) {
		window.HCAnimations = {
			isAvailable: false,
		};
		return;
	}

	const reducedMotionQuery = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	const motion = {
		duration: 0.65,
		ease: 'power3.out',
		fadeDuration: 0.45,
		fadeEase: 'power2.inOut',
	};

	function prefersReducedMotion() {
		return reducedMotionQuery.matches;
	}

	function getDuration( duration ) {
		return prefersReducedMotion() ? 0 : duration;
	}

	function createTimeline( options ) {
		const userDefaults = options && options.defaults ? options.defaults : {};
		const timelineOptions = Object.assign( {}, options, {
			defaults: Object.assign(
				{
					duration: getDuration( motion.duration ),
					ease: motion.ease,
				},
				userDefaults
			),
		} );

		return gsap.timeline( timelineOptions );
	}

	function set( target, vars ) {
		gsap.set( target, vars );
	}

	function to( target, vars ) {
		return gsap.to( target, Object.assign( {}, vars, {
			duration: getDuration( vars.duration !== undefined ? vars.duration : motion.duration ),
		} ) );
	}

	function crossfade( fromEl, toEl, options ) {
		const duration = getDuration( options && options.duration !== undefined ? options.duration : motion.fadeDuration );
		const ease = options && options.ease ? options.ease : motion.fadeEase;
		const position = options && options.position !== undefined ? options.position : 0;
		const timeline = options && options.timeline ? options.timeline : createTimeline();

		if ( fromEl ) {
			timeline.to( fromEl, { autoAlpha: 0, duration, ease }, position );
		}

		if ( toEl ) {
			timeline.fromTo(
				toEl,
				{ autoAlpha: 0 },
				{ autoAlpha: 1, duration, ease },
				position
			);
		}

		return timeline;
	}

	function debounce( callback, wait ) {
		let timer;

		return function debounced() {
			const context = this;
			const args = arguments;

			window.clearTimeout( timer );
			timer = window.setTimeout( function() {
				callback.apply( context, args );
			}, wait );
		};
	}

	function bindSwipe( element, options ) {
		const threshold = options && options.threshold !== undefined ? options.threshold : 50;
		const maxVerticalDrift = options && options.maxVerticalDrift !== undefined ? options.maxVerticalDrift : 80;
		let startX = 0;
		let startY = 0;
		let suppressClick = false;

		element.addEventListener(
			'touchstart',
			function( event ) {
				if ( ! event.changedTouches[ 0 ] ) {
					return;
				}

				startX = event.changedTouches[ 0 ].screenX;
				startY = event.changedTouches[ 0 ].screenY;
			},
			{ passive: true }
		);

		element.addEventListener(
			'touchend',
			function( event ) {
				if ( ! event.changedTouches[ 0 ] ) {
					return;
				}

				const deltaX = event.changedTouches[ 0 ].screenX - startX;
				const deltaY = event.changedTouches[ 0 ].screenY - startY;

				if ( Math.abs( deltaX ) < threshold || Math.abs( deltaY ) > maxVerticalDrift || Math.abs( deltaY ) > Math.abs( deltaX ) ) {
					return;
				}

				suppressClick = true;
				window.setTimeout( function() {
					suppressClick = false;
				}, 400 );

				if ( deltaX < 0 && options && options.onSwipeLeft ) {
					options.onSwipeLeft();
					return;
				}

				if ( deltaX > 0 && options && options.onSwipeRight ) {
					options.onSwipeRight();
				}
			},
			{ passive: true }
		);

		return {
			shouldSuppressClick: function() {
				return suppressClick;
			},
		};
	}

	window.HCAnimations = {
		isAvailable: true,
		motion,
		prefersReducedMotion,
		getDuration,
		createTimeline,
		set,
		to,
		crossfade,
		debounce,
		bindSwipe,
	};
}() );
