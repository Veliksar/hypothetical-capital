( function() {
	const sliders = document.querySelectorAll( '[data-highlights-slider]' );
	const animations = window.HCAnimations;

	if ( ! sliders.length || ! animations || ! animations.isAvailable ) {
		return;
	}

	const SLIDE_DURATION = 1;
	const OVERLAY_DURATION = 0.5;

	sliders.forEach( function( slider ) {
		const viewport = slider.querySelector( '.fc-block__highlights-viewport' );
		const stage = slider.querySelector( '.fc-block__highlights-stage' );
		const slotElements = gsap.utils.toArray( slider.querySelectorAll( '.fc-block__highlights-slot' ) );
		const cards = gsap.utils.toArray( slider.querySelectorAll( '.fc-block__highlight-card' ) );
		const captions = gsap.utils.toArray( slider.querySelectorAll( '.fc-block__highlight-description' ) );
		const prevButton = slider.querySelector( '[data-slider-prev]' );
		const nextButton = slider.querySelector( '[data-slider-next]' );

		if ( ! viewport || ! stage || slotElements.length !== 3 || ! cards.length || ! prevButton || ! nextButton ) {
			return;
		}

		const total = cards.length;
		const mobileQuery = window.matchMedia( '(max-width: 768px)' );
		let currentIndex = 0;
		let isAnimating = false;
		let activeTimeline = null;
		let swipeController = null;

		function getCard( index ) {
			if ( index < 0 || index >= total ) {
				return null;
			}

			return cards.find( function( card ) {
				return parseInt( card.dataset.cardIndex, 10 ) === index;
			} ) || null;
		}

		function getCaption( index ) {
			return captions.find( function( caption ) {
				return parseInt( caption.dataset.captionIndex, 10 ) === index;
			} ) || null;
		}

		function getCardLogo( card ) {
			return card ? card.querySelector( '.fc-block__highlight-logo' ) : null;
		}

		function setLogoHidden( logo ) {
			if ( ! logo ) {
				return;
			}

			animations.set( logo, { x: 181, autoAlpha: 0 } );
		}

		function setLogoVisible( logo ) {
			if ( ! logo ) {
				return;
			}

			animations.set( logo, { x: 0, autoAlpha: 1 } );
		}

		function animateLogoIn( logo, timeline ) {
			if ( ! logo ) {
				return;
			}

			animations.set( logo, { x: 181, autoAlpha: 1 } );
			timeline.to(
				logo,
				{
					x: 0,
					duration: animations.getDuration( SLIDE_DURATION ),
					ease: animations.motion.ease,
				},
				0.1
			);
		}

		function animateLogoOut( logo, timeline ) {
			if ( ! logo ) {
				return;
			}

			timeline.to(
				logo,
				{
					x: 181,
					autoAlpha: 0,
					duration: animations.getDuration( SLIDE_DURATION * 0.6 ),
					ease: animations.motion.ease,
				},
				0
			);
		}

		function getCardOverlay( card ) {
			return card ? card.querySelector( '.fc-block__highlight-overlay' ) : null;
		}

		function setOverlayHidden( overlay ) {
			if ( ! overlay ) {
				return;
			}

			animations.set( overlay, { xPercent: 0, autoAlpha: 0 } );
		}

		function setOverlayVisible( overlay ) {
			if ( ! overlay ) {
				return;
			}

			animations.set( overlay, { xPercent: 0, autoAlpha: 1 } );
		}

		function animateOverlayOut( overlay, timeline ) {
			if ( ! overlay ) {
				return;
			}

			const dur = animations.getDuration( OVERLAY_DURATION );

			animations.set( overlay, { xPercent: 0, autoAlpha: 1 } );
			timeline.to( overlay, { xPercent: -100, autoAlpha: 0, duration: dur, ease: 'power1.in' }, 0 );
		}

		function animateOverlayIn( overlay, timeline ) {
			if ( ! overlay ) {
				return;
			}

			animations.set( overlay, { xPercent: 0, autoAlpha: 0 } );
			timeline.to( overlay, {
				autoAlpha: 1,
				duration: animations.getDuration( animations.motion.fadeDuration ),
				ease: animations.motion.fadeEase,
			}, 0 );
		}

		function isMobileLayout() {
			return mobileQuery.matches;
		}

		function updateViewportWidth() {
			const viewportLeft = viewport.getBoundingClientRect().left;
			const bleedWidth = Math.max( viewport.offsetWidth, window.innerWidth - viewportLeft );

			viewport.style.width = bleedWidth + 'px';
		}

		function getSlotMetrics() {
			const stageRect = stage.getBoundingClientRect();

			return slotElements.map( function( slot ) {
				const inner = slot.querySelector( '.fc-block__highlights-slot-inner' ) || slot;
				const rect = inner.getBoundingClientRect();

				return {
					x: rect.left - stageRect.left,
					y: rect.top - stageRect.top,
					width: rect.width,
					height: rect.height,
				};
			} );
		}

		function getHiddenLeftMetrics( slotMetrics ) {
			return {
				x: -slotMetrics[ 0 ].width - 48,
				y: slotMetrics[ 0 ].y,
				width: slotMetrics[ 0 ].width,
				height: slotMetrics[ 0 ].height,
			};
		}

		function updateSliderMetrics( slotMetrics ) {
			if ( ! slotMetrics[ 0 ] || ! slotMetrics[ 0 ].height ) {
				return;
			}

			slider.style.setProperty( '--highlights-primary-size', slotMetrics[ 0 ].height + 'px' );
			slider.style.setProperty( '--highlights-primary-width', slotMetrics[ 0 ].width + 'px' );
		}

		function getHiddenRightMetrics( slotMetrics, stageWidth ) {
			const hiddenSlot = isMobileLayout() ? slotMetrics[ 1 ] : slotMetrics[ 2 ];

			return {
				x: stageWidth + 48,
				y: hiddenSlot.y,
				width: hiddenSlot.width,
				height: hiddenSlot.height,
			};
		}

		function applyCardState( card, metrics, visible ) {
			animations.set( card, {
				x: metrics.x,
				y: metrics.y,
				width: metrics.width,
				height: metrics.height,
				autoAlpha: visible ? 1 : 0,
				visibility: visible ? 'visible' : 'hidden',
			} );
		}

		function syncCaptions( activeIndex, immediate ) {
			captions.forEach( function( caption ) {
				const captionIndex = parseInt( caption.dataset.captionIndex, 10 );
				const isActive = captionIndex === activeIndex;

				if ( immediate || animations.prefersReducedMotion() ) {
					animations.set( caption, {
						autoAlpha: isActive ? 1 : 0,
						visibility: isActive ? 'visible' : 'hidden',
					} );
					return;
				}

				animations.set( caption, {
					visibility: 'visible',
				} );
			} );
		}

		function updateCardInteractivity() {
			const hasNextPreview = currentIndex < total - 1;
			const hasSkipPreview = currentIndex < total - 2 && ! isMobileLayout();

			cards.forEach( function( card ) {
				const cardIndex = parseInt( card.dataset.cardIndex, 10 );
				const isNextPreview = hasNextPreview && cardIndex === currentIndex + 1;
				const isSkipPreview = hasSkipPreview && cardIndex === currentIndex + 2;

				card.classList.toggle( 'is-next-trigger', isNextPreview );
				card.classList.toggle( 'is-skip-trigger', isSkipPreview );

				if ( isNextPreview || isSkipPreview ) {
					card.setAttribute( 'role', 'button' );
					card.setAttribute( 'tabindex', '0' );
					card.setAttribute(
						'aria-label',
						slider.getAttribute( 'data-next-slide-label' ) || 'Next slide'
					);
					return;
				}

				card.removeAttribute( 'role' );
				card.removeAttribute( 'tabindex' );
				card.removeAttribute( 'aria-label' );
			} );
		}

		function layoutIdle() {
			const slotMetrics = getSlotMetrics();
			const stageWidth = stage.offsetWidth;
			const hiddenLeft = getHiddenLeftMetrics( slotMetrics );
			const hiddenRight = getHiddenRightMetrics( slotMetrics, stageWidth );
			const mobileLayout = isMobileLayout();

			cards.forEach( function( card ) {
				const cardIndex = parseInt( card.dataset.cardIndex, 10 );
				const offset = cardIndex - currentIndex;
				const logo = getCardLogo( card );
				const overlay = getCardOverlay( card );

				if ( offset === 0 ) {
					applyCardState( card, slotMetrics[ 0 ], true );
					setLogoVisible( logo );
					setOverlayHidden( overlay );
					return;
				}

				if ( offset === 1 ) {
					applyCardState( card, slotMetrics[ 1 ], true );
					setLogoHidden( logo );
					setOverlayVisible( overlay );
					return;
				}

				if ( offset === 2 && ! mobileLayout ) {
					applyCardState( card, slotMetrics[ 2 ], true );
					setLogoHidden( logo );
					setOverlayVisible( overlay );
					return;
				}

				if ( offset < 0 ) {
					applyCardState( card, hiddenLeft, false );
					setLogoHidden( logo );
					setOverlayVisible( overlay );
					return;
				}

				applyCardState( card, hiddenRight, false );
				setLogoHidden( logo );
				setOverlayVisible( overlay );
			} );

			syncCaptions( currentIndex, true );
			updateCardInteractivity();
			updateButtons();
			updateSliderMetrics( slotMetrics );
		}

		function updateButtons() {
			prevButton.disabled = currentIndex <= 0 || isAnimating;
			nextButton.disabled = currentIndex >= total - 1 || isAnimating;
		}

		function finishTransition( nextIndex ) {
			currentIndex = nextIndex;
			isAnimating = false;
			activeTimeline = null;
			layoutIdle();
		}

		function animateNext() {
			if ( isAnimating || currentIndex >= total - 1 ) {
				return;
			}

			isAnimating = true;
			updateButtons();

			const slotMetrics = getSlotMetrics();
			const stageWidth = stage.offsetWidth;
			const hiddenLeft = getHiddenLeftMetrics( slotMetrics );
			const hiddenRight = getHiddenRightMetrics( slotMetrics, stageWidth );
			const leaving = getCard( currentIndex );
			const toPrimary = getCard( currentIndex + 1 );
			const toSecondary = getCard( currentIndex + 2 );
			const entering = getCard( currentIndex + 3 );
			const currentCaption = getCaption( currentIndex );
			const nextCaption = getCaption( currentIndex + 1 );
			const timeline = animations.createTimeline( {
				defaults: { duration: animations.getDuration( SLIDE_DURATION ) },
				onComplete: function() {
					finishTransition( currentIndex + 1 );
				},
			} );

			activeTimeline = timeline;

			if ( leaving ) {
				animations.set( leaving, { visibility: 'visible' } );
				timeline.to( leaving, {
					x: hiddenLeft.x,
					y: hiddenLeft.y,
					width: hiddenLeft.width,
					height: hiddenLeft.height,
					autoAlpha: 0,
				}, 0 );
				animateLogoOut( getCardLogo( leaving ), timeline );
			}

			if ( toPrimary ) {
				animations.set( toPrimary, { visibility: 'visible' } );
				timeline.to( toPrimary, {
					x: slotMetrics[ 0 ].x,
					y: slotMetrics[ 0 ].y,
					width: slotMetrics[ 0 ].width,
					height: slotMetrics[ 0 ].height,
					autoAlpha: 1,
				}, 0 );
				animateLogoIn( getCardLogo( toPrimary ), timeline );
				animateOverlayOut( getCardOverlay( toPrimary ), timeline );
			}

			if ( toSecondary ) {
				animations.set( toSecondary, { visibility: 'visible' } );
				timeline.to( toSecondary, {
					x: slotMetrics[ 1 ].x,
					y: slotMetrics[ 1 ].y,
					width: slotMetrics[ 1 ].width,
					height: slotMetrics[ 1 ].height,
					autoAlpha: 1,
				}, 0 );
			}

			if ( entering ) {
				const enteringTarget = isMobileLayout() ? hiddenRight : slotMetrics[ 2 ];

				animations.set( entering, {
					x: hiddenRight.x,
					y: hiddenRight.y,
					width: hiddenRight.width,
					height: hiddenRight.height,
					autoAlpha: 0,
					visibility: 'visible',
				} );
				timeline.to( entering, {
					x: enteringTarget.x,
					y: enteringTarget.y,
					width: enteringTarget.width,
					height: enteringTarget.height,
					autoAlpha: isMobileLayout() ? 0 : 1,
				}, 0 );
			}

			if ( currentCaption || nextCaption ) {
				captions.forEach( function( caption ) {
					animations.set( caption, { visibility: 'visible' } );
				} );
				animations.crossfade( currentCaption, nextCaption, { timeline } );
			}
		}

		function animatePrev() {
			if ( isAnimating || currentIndex <= 0 ) {
				return;
			}

			isAnimating = true;
			updateButtons();

			const slotMetrics = getSlotMetrics();
			const stageWidth = stage.offsetWidth;
			const hiddenLeft = getHiddenLeftMetrics( slotMetrics );
			const hiddenRight = getHiddenRightMetrics( slotMetrics, stageWidth );
			const entering = getCard( currentIndex - 1 );
			const toSecondary = getCard( currentIndex );
			const toTertiary = getCard( currentIndex + 1 );
			const leaving = getCard( currentIndex + 2 );
			const currentCaption = getCaption( currentIndex );
			const previousCaption = getCaption( currentIndex - 1 );
			const timeline = animations.createTimeline( {
				defaults: { duration: animations.getDuration( SLIDE_DURATION ) },
				onComplete: function() {
					finishTransition( currentIndex - 1 );
				},
			} );

			activeTimeline = timeline;

			if ( entering ) {
				setOverlayHidden( getCardOverlay( entering ) );
				animations.set( entering, {
					x: hiddenLeft.x,
					y: hiddenLeft.y,
					width: hiddenLeft.width,
					height: hiddenLeft.height,
					autoAlpha: 0,
					visibility: 'visible',
				} );
				timeline.to( entering, {
					x: slotMetrics[ 0 ].x,
					y: slotMetrics[ 0 ].y,
					width: slotMetrics[ 0 ].width,
					height: slotMetrics[ 0 ].height,
					autoAlpha: 1,
				}, 0 );
				animateLogoIn( getCardLogo( entering ), timeline );
			}

			if ( toSecondary ) {
				animations.set( toSecondary, { visibility: 'visible' } );
				timeline.to( toSecondary, {
					x: slotMetrics[ 1 ].x,
					y: slotMetrics[ 1 ].y,
					width: slotMetrics[ 1 ].width,
					height: slotMetrics[ 1 ].height,
					autoAlpha: 1,
				}, 0 );
				animateLogoOut( getCardLogo( toSecondary ), timeline );
				animateOverlayIn( getCardOverlay( toSecondary ), timeline );
			}

			if ( toTertiary ) {
				const tertiaryTarget = isMobileLayout() ? hiddenRight : slotMetrics[ 2 ];

				animations.set( toTertiary, { visibility: 'visible' } );
				timeline.to( toTertiary, {
					x: tertiaryTarget.x,
					y: tertiaryTarget.y,
					width: tertiaryTarget.width,
					height: tertiaryTarget.height,
					autoAlpha: isMobileLayout() ? 0 : 1,
				}, 0 );
			}

			if ( leaving ) {
				animations.set( leaving, { visibility: 'visible' } );
				timeline.to( leaving, {
					x: hiddenRight.x,
					y: hiddenRight.y,
					width: hiddenRight.width,
					height: hiddenRight.height,
					autoAlpha: 0,
				}, 0 );
			}

			if ( currentCaption || previousCaption ) {
				captions.forEach( function( caption ) {
					animations.set( caption, { visibility: 'visible' } );
				} );
				animations.crossfade( currentCaption, previousCaption, { timeline } );
			}
		}

		function animateSkip2() {
			if ( isAnimating || currentIndex >= total - 2 ) {
				return;
			}

			isAnimating = true;
			updateButtons();

			const slotMetrics = getSlotMetrics();
			const stageWidth = stage.offsetWidth;
			const hiddenLeft = getHiddenLeftMetrics( slotMetrics );
			const hiddenRight = getHiddenRightMetrics( slotMetrics, stageWidth );
			const leaving = getCard( currentIndex );
			const leavingAux = getCard( currentIndex + 1 );
			const toPrimary = getCard( currentIndex + 2 );
			const toSecondary = getCard( currentIndex + 3 );
			const toTertiary = getCard( currentIndex + 4 );
			const currentCaption = getCaption( currentIndex );
			const nextCaption = getCaption( currentIndex + 2 );
			const timeline = animations.createTimeline( {
				defaults: { duration: animations.getDuration( SLIDE_DURATION ) },
				onComplete: function() {
					finishTransition( currentIndex + 2 );
				},
			} );

			activeTimeline = timeline;

			if ( leaving ) {
				animations.set( leaving, { visibility: 'visible' } );
				timeline.to( leaving, {
					x: hiddenLeft.x,
					y: hiddenLeft.y,
					width: hiddenLeft.width,
					height: hiddenLeft.height,
					autoAlpha: 0,
				}, 0 );
				animateLogoOut( getCardLogo( leaving ), timeline );
			}

			if ( leavingAux ) {
				animations.set( leavingAux, { visibility: 'visible' } );
				timeline.to( leavingAux, {
					x: hiddenLeft.x,
					y: hiddenLeft.y,
					width: hiddenLeft.width,
					height: hiddenLeft.height,
					autoAlpha: 0,
				}, 0 );
			}

			if ( toPrimary ) {
				animations.set( toPrimary, { visibility: 'visible' } );
				timeline.to( toPrimary, {
					x: slotMetrics[ 0 ].x,
					y: slotMetrics[ 0 ].y,
					width: slotMetrics[ 0 ].width,
					height: slotMetrics[ 0 ].height,
					autoAlpha: 1,
				}, 0 );
				animateLogoIn( getCardLogo( toPrimary ), timeline );
				animateOverlayOut( getCardOverlay( toPrimary ), timeline );
			}

			if ( toSecondary ) {
				animations.set( toSecondary, {
					x: hiddenRight.x,
					y: hiddenRight.y,
					width: hiddenRight.width,
					height: hiddenRight.height,
					autoAlpha: 0,
					visibility: 'visible',
				} );
				timeline.to( toSecondary, {
					x: slotMetrics[ 1 ].x,
					y: slotMetrics[ 1 ].y,
					width: slotMetrics[ 1 ].width,
					height: slotMetrics[ 1 ].height,
					autoAlpha: 1,
				}, 0 );
			}

			if ( toTertiary ) {
				const toTertiaryTarget = isMobileLayout() ? hiddenRight : slotMetrics[ 2 ];

				animations.set( toTertiary, {
					x: hiddenRight.x,
					y: hiddenRight.y,
					width: hiddenRight.width,
					height: hiddenRight.height,
					autoAlpha: 0,
					visibility: 'visible',
				} );
				timeline.to( toTertiary, {
					x: toTertiaryTarget.x,
					y: toTertiaryTarget.y,
					width: toTertiaryTarget.width,
					height: toTertiaryTarget.height,
					autoAlpha: isMobileLayout() ? 0 : 1,
				}, 0 );
			}

			if ( currentCaption || nextCaption ) {
				captions.forEach( function( caption ) {
					animations.set( caption, { visibility: 'visible' } );
				} );
				animations.crossfade( currentCaption, nextCaption, { timeline } );
			}
		}

		function handleNextTrigger( event ) {
			if ( swipeController && swipeController.shouldSuppressClick() ) {
				return;
			}

			const card = event.currentTarget;

			if ( card.classList.contains( 'is-skip-trigger' ) ) {
				event.preventDefault();
				animateSkip2();
				return;
			}

			if ( ! card.classList.contains( 'is-next-trigger' ) ) {
				return;
			}

			event.preventDefault();
			animateNext();
		}

		function handleNextTriggerKeydown( event ) {
			if ( event.key !== 'Enter' && event.key !== ' ' ) {
				return;
			}

			event.preventDefault();
			handleNextTrigger( event );
		}

		prevButton.addEventListener( 'click', animatePrev );
		nextButton.addEventListener( 'click', animateNext );

		cards.forEach( function( card ) {
			card.addEventListener( 'click', handleNextTrigger );
			card.addEventListener( 'keydown', handleNextTriggerKeydown );
		} );

		const measure = animations.debounce( function() {
			if ( activeTimeline ) {
				activeTimeline.kill();
				activeTimeline = null;
				isAnimating = false;
			}

			updateViewportWidth();
			layoutIdle();
		}, 150 );

		if ( animations.bindSwipe ) {
			swipeController = animations.bindSwipe( viewport, {
				onSwipeLeft: function() {
					if ( ! isMobileLayout() ) {
						return;
					}

					animateNext();
				},
				onSwipeRight: function() {
					if ( ! isMobileLayout() ) {
						return;
					}

					animatePrev();
				},
			} );
		}

		if ( typeof mobileQuery.addEventListener === 'function' ) {
			mobileQuery.addEventListener( 'change', measure );
		} else if ( typeof mobileQuery.addListener === 'function' ) {
			mobileQuery.addListener( measure );
		}

		window.addEventListener( 'resize', measure );

		if ( typeof ResizeObserver !== 'undefined' ) {
			const resizeObserver = new ResizeObserver( measure );

			resizeObserver.observe( viewport );
			resizeObserver.observe( stage );
			slotElements.forEach( function( slot ) {
				resizeObserver.observe( slot );
			} );
		}

		cards.forEach( function( card ) {
			const image = card.querySelector( 'img' );

			if ( image && ! image.complete ) {
				image.addEventListener( 'load', measure );
			}
		} );

		updateViewportWidth();
		layoutIdle();
	} );
}() );
