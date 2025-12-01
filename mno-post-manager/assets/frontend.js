(function () {
  function initSlider(root) {
    var track = root.querySelector('.mno-pm-slider__track');
    if (!track) {
      return;
    }

    var slides = Array.prototype.filter.call(track.children, function (node) {
      return node && node.nodeType === 1;
    });
    if (!slides.length) {
      return;
    }

    var dotsContainer = root.querySelector('.mno-pm-slider__dots');
    var prevButton = root.querySelector('.mno-pm-slider__nav--prev');
    var nextButton = root.querySelector('.mno-pm-slider__nav--next');
    var index = 0;
    var dots = [];
    var resizeRaf = null;
    var scrollRaf = null;
    var programmaticTimeout = null;
    var isProgrammaticScroll = false;
    var swipeStartX = null;
    var swipeActive = false;

    slides.forEach(function (slide) {
      slide.style.flex = '0 0 100%';
    });

    if (slides.length <= 1) {
      if (dotsContainer) {
        dotsContainer.innerHTML = '';
        dotsContainer.style.display = 'none';
      }
      if (prevButton) {
        prevButton.style.display = 'none';
      }
      if (nextButton) {
        nextButton.style.display = 'none';
      }
      track.scrollLeft = 0;
      return;
    }

    function getDotLabel(i) {
      if (typeof mnoPmSlider !== 'undefined' && mnoPmSlider.i18n && mnoPmSlider.i18n.slide) {
        return mnoPmSlider.i18n.slide.replace('%d', i + 1);
      }
      return 'Slide ' + (i + 1);
    }

    function wrapIndex(targetIndex) {
      var lastIndex = slides.length - 1;
      if (targetIndex < 0) {
        return lastIndex;
      }
      if (targetIndex > lastIndex) {
        return 0;
      }
      return targetIndex;
    }

    function setActiveDot() {
      if (!dots.length) {
        return;
      }
      dots.forEach(function (dot, i) {
        if (!dot) {
          return;
        }
        if (i === index) {
          dot.classList.add('is-active');
        } else {
          dot.classList.remove('is-active');
        }
      });
    }

    function updateNavState() {
      if (prevButton) {
        prevButton.disabled = false;
      }
      if (nextButton) {
        nextButton.disabled = false;
      }
    }

    function scrollToIndex(targetIndex, smooth) {
      var target = slides[targetIndex];
      if (!target) {
        return;
      }

      var behavior = smooth ? 'smooth' : 'auto';
      if (typeof track.scrollTo === 'function') {
        try {
          track.scrollTo({ left: target.offsetLeft, behavior: behavior });
        } catch (error) {
          track.scrollLeft = target.offsetLeft;
        }
      } else {
        track.scrollLeft = target.offsetLeft;
      }
    }

    function goTo(targetIndex, smooth) {
      var normalized = wrapIndex(targetIndex);
      var didWrap = normalized !== targetIndex;
      var useSmoothScroll = smooth !== false && !didWrap;
      index = normalized;
      setActiveDot();
      updateNavState();
      isProgrammaticScroll = true;
      scrollToIndex(normalized, useSmoothScroll);
      window.clearTimeout(programmaticTimeout);
      programmaticTimeout = window.setTimeout(function () {
        isProgrammaticScroll = false;
      }, useSmoothScroll ? 500 : 100);
    }

    function updateIndexFromScroll() {
      scrollRaf = null;
      if (isProgrammaticScroll) {
        return;
      }

      var scrollLeft = track.scrollLeft;
      var closestIndex = index;
      var minDistance = Infinity;

      slides.forEach(function (slide, i) {
        var distance = Math.abs(slide.offsetLeft - scrollLeft);
        if (distance < minDistance) {
          minDistance = distance;
          closestIndex = i;
        }
      });

      if (closestIndex !== index) {
        index = closestIndex;
        setActiveDot();
        updateNavState();
      }
    }

    function handleScroll() {
      if (scrollRaf !== null) {
        window.cancelAnimationFrame(scrollRaf);
      }
      scrollRaf = window.requestAnimationFrame(updateIndexFromScroll);
    }

    function handleResize() {
      if (resizeRaf !== null) {
        window.cancelAnimationFrame(resizeRaf);
      }
      resizeRaf = window.requestAnimationFrame(function () {
        goTo(index, false);
      });
    }

    function beginSwipe(positionX) {
      swipeStartX = positionX;
      swipeActive = true;
    }

    function endSwipe(positionX) {
      if (!swipeActive || swipeStartX === null) {
        return;
      }

      var deltaX = positionX - swipeStartX;
      swipeStartX = null;
      swipeActive = false;

      if (Math.abs(deltaX) < 30) {
        return;
      }

      if (deltaX < 0) {
        goTo(index + 1, true);
      } else {
        goTo(index - 1, true);
      }
    }

    function cancelSwipe() {
      swipeStartX = null;
      swipeActive = false;
    }

    if (dotsContainer) {
      dotsContainer.innerHTML = '';
      dots = slides.map(function (_, i) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'mno-pm-slider__dot';
        dot.setAttribute('aria-label', getDotLabel(i));
        dot.addEventListener('click', function () {
          goTo(i, true);
        });
        dotsContainer.appendChild(dot);
        return dot;
      });
    }

    if (prevButton) {
      prevButton.addEventListener('click', function () {
        goTo(index - 1, true);
      });
    }

    if (nextButton) {
      nextButton.addEventListener('click', function () {
        goTo(index + 1, true);
      });
    }

    track.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);

    if (typeof window !== 'undefined' && typeof window.PointerEvent === 'function') {
      track.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse') {
          return;
        }
        beginSwipe(event.clientX);
      });
      track.addEventListener('pointerup', function (event) {
        if (event.pointerType === 'mouse') {
          return;
        }
        endSwipe(event.clientX);
      });
      track.addEventListener('pointercancel', cancelSwipe);
      track.addEventListener('pointerleave', cancelSwipe);
    } else {
      track.addEventListener('touchstart', function (event) {
        if (!event.touches || !event.touches.length) {
          return;
        }
        beginSwipe(event.touches[0].clientX);
      }, { passive: true });
      track.addEventListener('touchend', function (event) {
        if (!event.changedTouches || !event.changedTouches.length) {
          return;
        }
        endSwipe(event.changedTouches[0].clientX);
      });
      track.addEventListener('touchcancel', cancelSwipe);
    }

    setActiveDot();
    updateNavState();
    goTo(0, false);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var sliders = document.querySelectorAll('[data-mno-pm-slider]');
    Array.prototype.forEach.call(sliders, function (slider) {
      initSlider(slider);
    });
  });
})();