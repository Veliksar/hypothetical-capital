( function() {
	const sections = document.querySelectorAll( '.fc-block--stats' );

	if ( ! sections.length ) {
		return;
	}

	const MOBILE_MAX_WIDTH = 767;
	const STATS_MOBILE_ROW_LINE_INSET = 22;

	function isStatsMobileLayout() {
		return window.matchMedia( `(max-width: ${ MOBILE_MAX_WIDTH }px)` ).matches;
	}

	function getStatsMobileRowBreakTop( cards, sectionRect ) {
		const row1Bottom = Math.min(
			cards[0].getBoundingClientRect().bottom,
			cards[1].getBoundingClientRect().bottom
		);
		const row2Top = cards[2].getBoundingClientRect().top;
		const rowBreak = Math.abs( row1Bottom - row2Top ) < 1
			? row1Bottom - STATS_MOBILE_ROW_LINE_INSET
			: row1Bottom;

		return rowBreak - sectionRect.top;
	}

	function updateStatsHorizontalLines( section ) {
		const figure = section.querySelector( '.fc-block__figure' );
		const lines = section.querySelector( '.fc-block__grid-lines--horizontal' );

		if ( ! lines ) {
			return;
		}

		const sectionRect = section.getBoundingClientRect();

		if ( isStatsMobileLayout() ) {
			const statsGrid = section.querySelector( '.fc-block__stats' );
			const cards = statsGrid ? statsGrid.querySelectorAll( '.fc-block__stat-card' ) : [];

			if ( cards.length < 3 ) {
				lines.style.display = 'none';
				return;
			}

			lines.style.display = '';
			const rowBreakTop = getStatsMobileRowBreakTop( cards, sectionRect );

			lines.style.setProperty( '--stats-h-line-mid', `${ rowBreakTop }px` );
			return;
		}

		if ( ! figure ) {
			return;
		}

		lines.style.display = '';
		const figureRect = figure.getBoundingClientRect();
		const lineTop = figureRect.top - sectionRect.top;
		const lineMid = lineTop + figureRect.height * 0.66;

		lines.style.setProperty( '--stats-h-line-top', `${ lineTop }px` );
		lines.style.setProperty( '--stats-h-line-mid', `${ lineMid }px` );
	}

	function bindStatsHorizontalLines( section ) {
		updateStatsHorizontalLines( section );

		const image = section.querySelector( '.fc-block__figure img' );

		if ( image && ! image.complete ) {
			image.addEventListener( 'load', function() {
				updateStatsHorizontalLines( section );
			} );
		}
	}

	sections.forEach( bindStatsHorizontalLines );

	window.addEventListener( 'resize', function() {
		sections.forEach( updateStatsHorizontalLines );
	} );

	const animations = window.HCAnimations;

	if ( typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined' ) {
		return;
	}

	gsap.registerPlugin( ScrollTrigger );

	const COUNTER_DURATION = 2;
	const COUNTER_STAGGER = 0.15;
	const statKsesSpan = '<span class="fc-block__stat-title-unit">%</span>';

	function formatValue( value, decimals ) {
		if ( decimals > 0 ) {
			return value.toFixed( decimals );
		}

		return String( Math.round( value ) );
	}

	function renderCounter( element, parts, value ) {
		const formatted = formatValue( value, parts.decimals );
		const output = parts.prefix + formatted;

		if ( parts.suffix === '%' ) {
			element.innerHTML = output + statKsesSpan;
			return;
		}

		element.textContent = output + parts.suffix;
	}

	function getCounterParts( element ) {
		const value = parseFloat( element.dataset.statValue );

		if ( Number.isNaN( value ) ) {
			return null;
		}

		return {
			value,
			prefix: element.dataset.statPrefix || '',
			suffix: element.dataset.statSuffix || '',
			decimals: parseInt( element.dataset.statDecimals, 10 ) || 0,
		};
	}

	sections.forEach( function( section ) {
		const counters = gsap.utils.toArray( section.querySelectorAll( '[data-stat-counter]' ) );

		if ( ! counters.length ) {
			return;
		}

		const counterData = counters
			.map( function( element ) {
				const parts = getCounterParts( element );

				if ( ! parts ) {
					return null;
				}

				return {
					element,
					parts,
					state: { value: 0 },
				};
			} )
			.filter( Boolean );

		if ( ! counterData.length ) {
			return;
		}

		if ( ! animations || ! animations.isAvailable || animations.prefersReducedMotion() ) {
			counterData.forEach( function( item ) {
				renderCounter( item.element, item.parts, item.parts.value );
			} );
			return;
		}

		const timeline = animations.createTimeline( {
			scrollTrigger: {
				trigger: section,
				start: 'top 80%',
				once: true,
			},
		} );

		counterData.forEach( function( item, index ) {
			timeline.to(
				item.state,
				{
					value: item.parts.value,
					duration: animations.getDuration( COUNTER_DURATION ),
					ease: 'power2.out',
					onUpdate: function() {
						renderCounter( item.element, item.parts, item.state.value );
					},
				},
				index * COUNTER_STAGGER
			);
		} );
	} );
}() );
