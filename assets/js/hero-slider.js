/**
 * assets/js/hero-slider.js
 * Homepage hero slider — vanilla JS, no dependencies.
 */

( function () {
  'use strict';

  const slider    = document.getElementById( 'hero-slider' );
  if ( ! slider ) return; // not the homepage

  const track     = slider.querySelector( '.hero-slides-track' );
  const slides    = track ? Array.from( track.children ) : [];
  const dots      = Array.from( slider.querySelectorAll( '.hero-dot' ) );
  const prevBtn   = slider.querySelector( '.hero-arrow-prev' );
  const nextBtn   = slider.querySelector( '.hero-arrow-next' );
  const announcer = document.getElementById( 'hero-slide-announcer' );

  if ( ! track || slides.length <= 1 ) return; // single slide — no controls needed

  const AUTOPLAY_MS = 6000;
  let current  = 0;
  let timer    = null;

  function goTo( index ) {
    current = ( index + slides.length ) % slides.length;

    track.style.transform = `translateX(-${ current * 100 }%)`;

    slides.forEach( ( s, i ) => s.classList.toggle( 'active', i === current ) );
    dots.forEach( ( d, i ) => {
      d.classList.toggle( 'active', i === current );
      if ( i === current ) d.setAttribute( 'aria-current', 'true' );
      else d.removeAttribute( 'aria-current' );
    } );

    if ( announcer ) {
      const title = slides[ current ].querySelector( '.hero-title' )?.textContent.trim() || '';
      announcer.textContent = `Slide ${ current + 1 } of ${ slides.length }: ${ title }`;
    }
  }

  function next() { goTo( current + 1 ); }
  function prev() { goTo( current - 1 ); }

  function startAutoplay() {
    stopAutoplay();
    timer = setInterval( next, AUTOPLAY_MS );
  }
  function stopAutoplay() {
    if ( timer ) clearInterval( timer );
    timer = null;
  }

  prevBtn?.addEventListener( 'click', () => { prev(); startAutoplay(); } );
  nextBtn?.addEventListener( 'click', () => { next(); startAutoplay(); } );
  dots.forEach( dot => {
    dot.addEventListener( 'click', () => {
      goTo( parseInt( dot.dataset.index, 10 ) || 0 );
      startAutoplay();
    } );
  } );

  // Pause on hover/focus — required for auto-advancing carousels (WCAG 2.2.2)
  slider.addEventListener( 'mouseenter', stopAutoplay );
  slider.addEventListener( 'mouseleave', startAutoplay );
  slider.addEventListener( 'focusin',  stopAutoplay );
  slider.addEventListener( 'focusout', startAutoplay );

  // Keyboard arrows when the slider/controls have focus
  slider.addEventListener( 'keydown', e => {
    if ( e.key === 'ArrowLeft' )  { prev(); startAutoplay(); }
    if ( e.key === 'ArrowRight' ) { next(); startAutoplay(); }
  } );

  // Touch swipe
  let touchStartX = null;
  track.addEventListener( 'touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true } );
  track.addEventListener( 'touchend', e => {
    if ( touchStartX === null ) return;
    const delta = e.changedTouches[0].clientX - touchStartX;
    if ( Math.abs( delta ) > 50 ) {
      delta < 0 ? next() : prev();
      startAutoplay();
    }
    touchStartX = null;
  }, { passive: true } );

  goTo( 0 );
  startAutoplay();

} )();
