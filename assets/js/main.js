/**
 * Mustala — professional frontend interactions
 */
(function () {
  'use strict';

  var navbar = document.getElementById('navbar');
  var menuToggle = document.getElementById('menu-toggle');
  var mainNav = document.getElementById('main-nav');
  var scrollTop = document.getElementById('scroll-top');
  var lightbox = document.getElementById('lightbox');
  var lightboxImg = document.getElementById('lightbox-img');
  var lightboxVideo = document.getElementById('lightbox-video');

  function onScroll() {
    var y = window.scrollY || document.documentElement.scrollTop;
    if (navbar) navbar.classList.toggle('sticky', y > 40);
    if (scrollTop) scrollTop.classList.toggle('show', y > 420);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (scrollTop) {
    scrollTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  function setMenuOpen(open) {
    if (!mainNav || !menuToggle) return;
    mainNav.classList.toggle('active', !!open);
    if (navbar) navbar.classList.toggle('menu-open', !!open);
    document.body.classList.toggle('nav-open', !!open);
    menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    var icon = menuToggle.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-bars', !open);
      icon.classList.toggle('fa-xmark', !!open);
    }
  }

  if (menuToggle && mainNav) {
    menuToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setMenuOpen(!mainNav.classList.contains('active'));
    });

    mainNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        setMenuOpen(false);
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setMenuOpen(false);
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 991) setMenuOpen(false);
    });
  }

  /* Language dropdown */
  (function initLangDropdown() {
    var dropdown = document.getElementById('lang-dropdown');
    var toggle = document.getElementById('lang-toggle');
    var menu = dropdown ? dropdown.querySelector('.lang-dropdown-menu') : null;
    if (!dropdown || !toggle || !menu) return;

    function setOpen(open) {
      dropdown.classList.toggle('is-open', !!open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) menu.removeAttribute('hidden');
      else menu.setAttribute('hidden', '');
    }

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setOpen(!dropdown.classList.contains('is-open'));
    });

    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target)) setOpen(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setOpen(false);
    });
  })();

  if (window.Typed) {
    var typedStrings = (window.MUSTALA_TYPED && window.MUSTALA_TYPED.hero) || ['Web Developer', 'WordPress Developer', 'Frontend Developer', 'Freelancer'];
    var typedAlt = (window.MUSTALA_TYPED && window.MUSTALA_TYPED.about) || typedStrings;

    if (document.querySelector('.typing')) {
      new Typed('.typing', {
        strings: typedStrings,
        typeSpeed: 90,
        backSpeed: 55,
        loop: true
      });
    }
    if (document.querySelector('.typing-2')) {
      new Typed('.typing-2', {
        strings: typedAlt,
        typeSpeed: 90,
        backSpeed: 55,
        loop: true
      });
    }
  }

  /* Scroll reveal */
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('visible'); });
  }

  /* Animated counters */
  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-target'), 10) || 0;
    var duration = 1400;
    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      el.textContent = Math.floor(progress * target);
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target;
    }
    requestAnimationFrame(step);
  }

  document.querySelectorAll('.counter').forEach(function (el) {
    if ('IntersectionObserver' in window) {
      var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCounter(el);
            obs.unobserve(el);
          }
        });
      }, { threshold: 0.4 });
      obs.observe(el);
    } else {
      animateCounter(el);
    }
  });

  /* Skill bars */
  document.querySelectorAll('.bar span[data-width]').forEach(function (bar) {
    if ('IntersectionObserver' in window) {
      var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            bar.style.width = bar.getAttribute('data-width');
            obs.unobserve(bar);
          }
        });
      });
      obs.observe(bar);
    } else {
      bar.style.width = bar.getAttribute('data-width');
    }
  });

  /* Accordion */
  document.querySelectorAll('.accordion-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.accordion-item');
      var panel = item.querySelector('.accordion-panel');
      var open = item.classList.contains('open');
      document.querySelectorAll('.accordion-item.open').forEach(function (other) {
        if (other !== item) {
          other.classList.remove('open');
          other.querySelector('.accordion-panel').style.maxHeight = null;
        }
      });
      if (open) {
        item.classList.remove('open');
        panel.style.maxHeight = null;
      } else {
        item.classList.add('open');
        panel.style.maxHeight = panel.scrollHeight + 'px';
      }
    });
  });

  /* Lightbox */
  function openLightbox(src, type) {
    if (!lightbox) return;
    lightbox.hidden = false;
    if (type === 'video') {
      lightbox.classList.add('show-video');
      lightboxVideo.src = src;
    } else {
      lightbox.classList.remove('show-video');
      lightboxImg.src = src;
      if (lightboxVideo) {
        lightboxVideo.pause && lightboxVideo.pause();
        lightboxVideo.removeAttribute('src');
      }
    }
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.hidden = true;
    lightbox.classList.remove('show-video');
    if (lightboxVideo) {
      lightboxVideo.pause && lightboxVideo.pause();
      lightboxVideo.removeAttribute('src');
    }
    if (lightboxImg) lightboxImg.removeAttribute('src');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('[data-lightbox]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      openLightbox(el.getAttribute('href') || el.dataset.src, el.dataset.lightbox || 'image');
    });
  });

  if (lightbox) {
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target.classList.contains('lightbox-close')) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeLightbox();
    });
  }

  /* Teams slider (mobile / tablet) */
  function initTeamsSlider(root) {
    var track = root.querySelector('[data-teams-track]');
    var dotsWrap = root.querySelector('[data-teams-dots]');
    if (!track) return;

    var cards = Array.prototype.slice.call(track.querySelectorAll('.team-card'));
    if (!cards.length) return;

    var index = 0;
    var timer = null;
    var mq = window.matchMedia('(max-width: 991px)');

    function isMobile() {
      return mq.matches;
    }

    function goTo(i, smooth) {
      if (!cards.length) return;
      index = ((i % cards.length) + cards.length) % cards.length;
      var behavior = smooth === false ? 'auto' : 'smooth';
      track.scrollTo({ left: cards[index].offsetLeft, behavior: behavior });
      updateDots();
    }

    function updateDots() {
      if (!dotsWrap) return;
      dotsWrap.querySelectorAll('button').forEach(function (dot, di) {
        dot.classList.toggle('active', di === index);
        dot.setAttribute('aria-selected', di === index ? 'true' : 'false');
      });
    }

    function syncFromScroll() {
      if (!isMobile()) return;
      var closest = 0;
      var best = Infinity;
      var left = track.scrollLeft;
      cards.forEach(function (card, i) {
        var d = Math.abs(card.offsetLeft - left);
        if (d < best) {
          best = d;
          closest = i;
        }
      });
      if (closest !== index) {
        index = closest;
        updateDots();
      }
    }

    if (dotsWrap) {
      dotsWrap.innerHTML = cards.map(function (_, i) {
        return '<button type="button" role="tab" aria-label="Slide ' + (i + 1) + '"' +
          (i === 0 ? ' class="active" aria-selected="true"' : ' aria-selected="false"') + '></button>';
      }).join('');
      dotsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('button');
        if (!btn) return;
        var i = Array.prototype.indexOf.call(dotsWrap.children, btn);
        if (i >= 0) {
          goTo(i);
          restartAuto();
        }
      });
    }

    var scrollTick = null;
    track.addEventListener('scroll', function () {
      if (scrollTick) cancelAnimationFrame(scrollTick);
      scrollTick = requestAnimationFrame(syncFromScroll);
    }, { passive: true });

    function stopAuto() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    function startAuto() {
      stopAuto();
      if (!isMobile() || cards.length < 2) return;
      timer = setInterval(function () {
        goTo(index + 1);
      }, 4000);
    }

    function restartAuto() {
      stopAuto();
      startAuto();
    }

    var touchStartX = 0;
    track.addEventListener('touchstart', function (e) {
      stopAuto();
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    track.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(dx) > 40) {
        goTo(index + (dx < 0 ? 1 : -1));
      }
      restartAuto();
    }, { passive: true });

    function onResize() {
      if (isMobile()) {
        goTo(index, false);
        startAuto();
      } else {
        stopAuto();
        track.scrollLeft = 0;
      }
    }

    if (mq.addEventListener) mq.addEventListener('change', onResize);
    else mq.addListener(onResize);
    window.addEventListener('resize', onResize);

    if (isMobile()) {
      goTo(0, false);
      startAuto();
    }
  }

  document.querySelectorAll('[data-teams-slider]').forEach(initTeamsSlider);

  document.querySelectorAll('#contact-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var btn = form.querySelector('.contact-submit');
      var label = form.querySelector('.contact-submit-text');
      if (!btn || btn.disabled) return;
      form.classList.add('is-submitting');
      btn.disabled = true;
      if (label) {
        label.textContent = btn.getAttribute('data-sending') || 'Sending…';
      }
    });
  });
})();
