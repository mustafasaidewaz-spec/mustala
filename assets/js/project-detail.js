(function () {
  'use strict';

  var showcase = document.querySelector('.project-detail-showcase[data-project-gallery]');
  var mainImg = document.getElementById('project-main-image');
  var mainLink = document.getElementById('project-main-link');

  if (!showcase || !mainImg) {
    return;
  }

  var urls = [];
  try {
    urls = JSON.parse(showcase.getAttribute('data-project-gallery') || '[]');
  } catch (err) {
    urls = [];
  }

  if (!urls.length) {
    return;
  }

  var thumbs = Array.prototype.slice.call(document.querySelectorAll('.project-detail-thumb'));
  var lightbox = document.getElementById('lightbox');
  var lightboxImg = document.getElementById('lightbox-img');
  var lightboxPrev = document.getElementById('lightbox-prev');
  var lightboxNext = document.getElementById('lightbox-next');
  var lightboxDots = document.getElementById('lightbox-dots');
  var lightboxOpen = false;
  var index = 0;

  function setIndex(nextIndex) {
    index = ((nextIndex % urls.length) + urls.length) % urls.length;
    var src = urls[index];

    mainImg.src = src;
    if (mainLink) {
      mainLink.href = src;
    }

    thumbs.forEach(function (btn, thumbIndex) {
      var active = thumbIndex === index;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    if (lightboxOpen && lightboxImg) {
      lightboxImg.src = src;
      updateLightboxDots();
    }
  }

  function updateLightboxDots() {
    if (!lightboxDots) {
      return;
    }
    lightboxDots.querySelectorAll('.lightbox-dot').forEach(function (dot, dotIndex) {
      dot.classList.toggle('is-active', dotIndex === index);
    });
  }

  function toggleLightboxNav(show) {
    var visible = show && urls.length > 1;
    if (lightboxPrev) {
      lightboxPrev.hidden = !visible;
    }
    if (lightboxNext) {
      lightboxNext.hidden = !visible;
    }
    if (lightboxDots) {
      lightboxDots.hidden = !visible;
    }
  }

  function buildLightboxDots() {
    if (!lightboxDots || lightboxDots.children.length) {
      updateLightboxDots();
      return;
    }

    urls.forEach(function (_, dotIndex) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'lightbox-dot' + (dotIndex === index ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Image ' + (dotIndex + 1));
      dot.addEventListener('click', function (event) {
        event.stopPropagation();
        setIndex(dotIndex);
      });
      lightboxDots.appendChild(dot);
    });
  }

  function openLightbox() {
    if (!lightbox || !lightboxImg) {
      return;
    }

    lightboxOpen = true;
    lightbox.hidden = false;
    lightbox.classList.remove('show-video');
    lightboxImg.src = urls[index];
    document.body.style.overflow = 'hidden';
    buildLightboxDots();
    toggleLightboxNav(true);
    updateLightboxDots();
  }

  function closeLightbox() {
    if (!lightbox) {
      return;
    }

    lightboxOpen = false;
    lightbox.hidden = true;
    if (lightboxImg) {
      lightboxImg.removeAttribute('src');
    }
    document.body.style.overflow = '';
    toggleLightboxNav(false);
  }

  thumbs.forEach(function (btn, thumbIndex) {
    btn.addEventListener('click', function () {
      setIndex(thumbIndex);
    });
  });

  if (mainLink) {
    mainLink.addEventListener('click', function (event) {
      event.preventDefault();
      openLightbox();
    });
  }

  if (lightboxPrev) {
    lightboxPrev.addEventListener('click', function (event) {
      event.stopPropagation();
      setIndex(index - 1);
    });
  }

  if (lightboxNext) {
    lightboxNext.addEventListener('click', function (event) {
      event.stopPropagation();
      setIndex(index + 1);
    });
  }

  if (lightbox) {
    lightbox.addEventListener('click', function (event) {
      if (event.target === lightbox || event.target.classList.contains('lightbox-close')) {
        closeLightbox();
      }
    });
  }

  document.addEventListener('keydown', function (event) {
    if (lightbox && lightbox.hidden) {
      lightboxOpen = false;
    }

    if (!lightboxOpen || !lightbox || lightbox.hidden) {
      return;
    }

    if (event.key === 'Escape') {
      closeLightbox();
    }
    if (event.key === 'ArrowLeft') {
      setIndex(index - 1);
    }
    if (event.key === 'ArrowRight') {
      setIndex(index + 1);
    }
  });
})();
