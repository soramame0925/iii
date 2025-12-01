(() => {
  const init = () => {
    if (!document.querySelector('.mno-gallery')) {
      return;
    }

    let lastTouchEnd = 0;

    const handleTouchStart = (event) => {
      if (event.touches.length > 1) {
        lastTouchEnd = 0;
      }
    };

    const handleTouchEnd = (event) => {
      if (event.touches.length > 0) {
        return;
      }

      const now = Date.now();

      if (now - lastTouchEnd <= 300) {
        event.preventDefault();
      }

      lastTouchEnd = now;
    };

    document.addEventListener('touchstart', handleTouchStart, { passive: true });
    document.addEventListener('touchend', handleTouchEnd, { passive: false });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();