/**
 * Mustala Projects gallery
 * Data source: api/projects.php (MySQL via admin)
 */
(function () {
  'use strict';

  var root = document.getElementById('projects-app');
  if (!root) return;

  var baseUrl = root.getAttribute('data-base') || '';
  var dataUrl = root.getAttribute('data-json') || (baseUrl + '/api/projects.php');
  var i18n = {
    showing: root.getAttribute('data-showing-label') || 'Showing',
    countOne: root.getAttribute('data-count-one') || 'project',
    countMany: root.getAttribute('data-count-many') || 'projects',
    loading: root.getAttribute('data-loading') || 'Loading projects…',
    empty: root.getAttribute('data-empty') || 'No projects match this filter.',
    loadError: root.getAttribute('data-load-error') || 'Could not load projects.',
    viewProject: root.getAttribute('data-view-project') || 'View Project',
    liveDemo: root.getAttribute('data-live-demo') || 'Live Demo',
    github: root.getAttribute('data-github') || 'GitHub',
    videoDemo: root.getAttribute('data-video-demo') || 'Video Demo',
    features: root.getAttribute('data-features') || 'Features',
    technologies: root.getAttribute('data-technologies') || 'Technologies',
    completed: root.getAttribute('data-completed') || 'Completed',
    playVideo: root.getAttribute('data-play-video') || 'Play video'
  };

  var state = {
    projects: [],
    categories: [],
    filter: 'all',
    query: '',
    lightboxImages: [],
    lightboxIndex: 0
  };

  var els = {
    filters: root.querySelector('[data-filters]'),
    grid: root.querySelector('[data-grid]'),
    search: root.querySelector('[data-search]'),
    count: root.querySelector('[data-count]')
  };

  if (els.search && root.getAttribute('data-search-placeholder')) {
    els.search.placeholder = root.getAttribute('data-search-placeholder');
  }

  function asset(path) {
    if (!path) return baseUrl + '/assets/img/placeholder.svg';
    if (/^https?:\/\//i.test(path)) return path;
    return baseUrl + '/' + String(path).replace(/^\//, '');
  }

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function hasLink(url) {
    return url != null && String(url).trim() !== '';
  }

  function isExternal(url) {
    return hasLink(url) && url !== '#';
  }

  function projectDetailUrl(p) {
    if (p.detailUrl) return p.detailUrl;
    if (p.slug) {
      return baseUrl + '/project.php?slug=' + encodeURIComponent(p.slug);
    }
    return '#';
  }

  function categoryLabel(id) {
    var found = state.categories.find(function (c) { return c.id === id; });
    return found ? found.label : id;
  }

  function filteredProjects() {
    return state.projects.filter(function (p) {
      var catOk = state.filter === 'all' || p.category === state.filter;
      var q = state.query.trim().toLowerCase();
      if (!q) return catOk;
      var hay = [
        p.title,
        p.shortDescription,
        p.description,
        (p.technologies || []).join(' '),
        p.category
      ].join(' ').toLowerCase();
      return catOk && hay.indexOf(q) !== -1;
    });
  }

  function renderFilters() {
    if (!els.filters) return;
    els.filters.innerHTML = state.categories.map(function (cat) {
      return '<button type="button" data-filter="' + escapeHtml(cat.id) + '"' +
        (state.filter === cat.id ? ' class="active"' : '') + '>' +
        escapeHtml(cat.label) + '</button>';
    }).join('');
  }

  function renderCards() {
    var list = filteredProjects();
    if (els.count) {
      var word = list.length === 1 ? i18n.countOne : i18n.countMany;
      els.count.textContent = list.length + ' ' + word;
    }
    if (!list.length) {
      els.grid.innerHTML = '<div class="projects-empty">' + escapeHtml(i18n.empty) + '</div>';
      return;
    }

    els.grid.innerHTML = list.map(function (p) {
      var techs = (p.technologies || []).slice(0, 5).map(function (t) {
        return '<span>' + escapeHtml(t) + '</span>';
      }).join('');
      var hasVideo = (p.videos || []).length > 0;
      var detailHref = projectDetailUrl(p);

      return (
        '<article class="project-card" data-category="' + escapeHtml(p.category) + '" data-id="' + escapeHtml(p.id) + '">' +
          '<div class="project-card-media" data-lightbox-open data-project-id="' + escapeHtml(p.id) + '">' +
            '<img src="' + escapeHtml(asset(p.thumbnail)) + '" alt="' + escapeHtml(p.title) + '" loading="lazy" decoding="async">' +
            '<span class="media-badge">' + escapeHtml(categoryLabel(p.category)) + '</span>' +
            (hasVideo
              ? '<button type="button" class="play-badge" data-video-open data-project-id="' + escapeHtml(p.id) + '" aria-label="' + escapeHtml(i18n.playVideo) + '"><i class="fas fa-play"></i></button>'
              : '') +
          '</div>' +
          '<div class="project-card-body">' +
            '<h3><a href="' + escapeHtml(detailHref) + '">' + escapeHtml(p.title) + '</a></h3>' +
            '<p>' + escapeHtml(p.shortDescription) + '</p>' +
            '<div class="tech-tags">' + techs + '</div>' +
            '<div class="project-card-actions">' +
              '<a class="btn" href="' + escapeHtml(detailHref) + '">' + escapeHtml(i18n.viewProject) + '</a>' +
            '</div>' +
          '</div>' +
        '</article>'
      );
    }).join('');

    requestAnimationFrame(function () {
      observeCards();
    });
  }

  function observeCards() {
    var cards = els.grid.querySelectorAll('.project-card');
    if (!('IntersectionObserver' in window)) {
      cards.forEach(function (c) { c.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });
    cards.forEach(function (c) { io.observe(c); });
  }

  function getProject(id) {
    return state.projects.find(function (p) { return p.id === id; });
  }

  /* ---------- Details modal ---------- */
  var detailsModal = document.getElementById('pj-details-modal');
  var detailsBody = document.getElementById('pj-details-body');
  var detailsMain = null;
  var detailsThumbs = null;
  var currentGallery = [];
  var currentGalleryIndex = 0;

  function openDetails(id) {
    var p = getProject(id);
    if (!p || !detailsModal || !detailsBody) return;

    currentGallery = (p.images && p.images.length ? p.images : [p.thumbnail]).map(asset);
    currentGalleryIndex = 0;

    var techs = (p.technologies || []).map(function (t) {
      return '<span>' + escapeHtml(t) + '</span>';
    }).join('');
    var features = (p.features || []).map(function (f) {
      return '<li>' + escapeHtml(f) + '</li>';
    }).join('');
    var thumbs = currentGallery.map(function (src, i) {
      return '<button type="button" data-thumb="' + i + '" class="' + (i === 0 ? 'active' : '') + '">' +
        '<img src="' + escapeHtml(src) + '" alt="" loading="lazy"></button>';
    }).join('');

    var videoBtn = (p.videos || []).length
      ? '<button type="button" class="btn btn-outline" data-video-open data-project-id="' + escapeHtml(p.id) + '"><i class="fas fa-play"></i> ' + escapeHtml(i18n.videoDemo) + '</button>'
      : '';
    var live = hasLink(p.liveDemo)
      ? '<a class="btn" href="' + escapeHtml(p.liveDemo) + '"' +
        (isExternal(p.liveDemo) ? ' target="_blank" rel="noopener"' : '') + '>' + escapeHtml(i18n.liveDemo) + '</a>'
      : '';
    var github = hasLink(p.github)
      ? '<a class="btn btn-outline" href="' + escapeHtml(p.github) + '"' +
        (isExternal(p.github) ? ' target="_blank" rel="noopener"' : '') + '>' + escapeHtml(i18n.github) + '</a>'
      : '';

    detailsBody.innerHTML =
      '<div class="pj-modal-grid">' +
        '<div>' +
          '<div class="pj-gallery-main" data-main-media>' +
            '<img src="' + escapeHtml(currentGallery[0]) + '" alt="' + escapeHtml(p.title) + '">' +
          '</div>' +
          '<div class="pj-thumbs" data-thumbs>' + thumbs + '</div>' +
        '</div>' +
        '<div>' +
          '<div class="pj-meta-label">' + escapeHtml(categoryLabel(p.category)) + '</div>' +
          '<h2>' + escapeHtml(p.title) + '</h2>' +
          '<p class="desc">' + escapeHtml(p.description) + '</p>' +
          (features ? '<div class="pj-meta-label">' + escapeHtml(i18n.features) + '</div><ul class="pj-features">' + features + '</ul>' : '') +
          '<div class="pj-meta-label">' + escapeHtml(i18n.technologies) + '</div><div class="tech-tags">' + techs + '</div>' +
          '<div class="pj-info-row">' +
            '<div><strong>' + escapeHtml(i18n.completed) + '</strong>' + escapeHtml(p.completedAt || '—') + '</div>' +
          '</div>' +
          '<div class="pj-modal-actions">' + live + github + videoBtn + '</div>' +
        '</div>' +
      '</div>';

    detailsMain = detailsBody.querySelector('[data-main-media]');
    detailsThumbs = detailsBody.querySelector('[data-thumbs]');
    detailsModal.classList.add('is-open');
    document.body.classList.add('pj-lock');
  }

  function setDetailsMedia(index) {
    if (!detailsMain || !currentGallery.length) return;
    currentGalleryIndex = (index + currentGallery.length) % currentGallery.length;
    detailsMain.innerHTML = '<img src="' + escapeHtml(currentGallery[currentGalleryIndex]) + '" alt="">';
    if (detailsThumbs) {
      detailsThumbs.querySelectorAll('button').forEach(function (btn, i) {
        btn.classList.toggle('active', i === currentGalleryIndex);
      });
    }
  }

  function closeDetails() {
    if (!detailsModal) return;
    detailsModal.classList.remove('is-open');
    if (!document.getElementById('pj-video-modal').classList.contains('is-open') &&
        !document.getElementById('pj-lightbox').classList.contains('is-open')) {
      document.body.classList.remove('pj-lock');
    }
  }

  /* ---------- Video modal ---------- */
  var videoModal = document.getElementById('pj-video-modal');
  var videoFrame = document.getElementById('pj-video-frame');

  function openVideo(id) {
    var p = getProject(id);
    if (!p || !(p.videos || []).length || !videoModal || !videoFrame) return;
    var v = p.videos[0];
    if (v.type === 'youtube') {
      videoFrame.innerHTML =
        '<iframe src="https://www.youtube.com/embed/' + encodeURIComponent(v.src) +
        '?autoplay=1&rel=0&playsinline=1&modestbranding=1" title="' + escapeHtml(v.title || p.title) +
        '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>';
    } else {
      videoFrame.innerHTML =
        '<video controls autoplay playsinline webkit-playsinline preload="metadata">' +
        '<source src="' + escapeHtml(asset(v.src)) + '" type="video/mp4">' +
        '</video>';
    }
    videoModal.classList.add('is-open');
    document.body.classList.add('pj-lock');
  }

  function closeVideo() {
    if (!videoModal || !videoFrame) return;
    videoModal.classList.remove('is-open');
    videoFrame.innerHTML = '';
    if (!detailsModal.classList.contains('is-open') &&
        !document.getElementById('pj-lightbox').classList.contains('is-open')) {
      document.body.classList.remove('pj-lock');
    }
  }

  /* ---------- Lightbox ---------- */
  var lightbox = document.getElementById('pj-lightbox');
  var lightboxImg = document.getElementById('pj-lightbox-img');
  var lightboxCaption = document.getElementById('pj-lightbox-caption');

  function openLightbox(projectId, startIndex) {
    var p = getProject(projectId);
    if (!p || !lightbox || !lightboxImg) return;
    state.lightboxImages = (p.images && p.images.length ? p.images : [p.thumbnail]).map(asset);
    state.lightboxIndex = startIndex || 0;
    showLightboxImage();
    lightbox.classList.add('is-open');
    document.body.classList.add('pj-lock');
  }

  function showLightboxImage() {
    if (!state.lightboxImages.length) return;
    state.lightboxIndex = (state.lightboxIndex + state.lightboxImages.length) % state.lightboxImages.length;
    lightboxImg.style.transform = 'scale(0.96)';
    lightboxImg.src = state.lightboxImages[state.lightboxIndex];
    requestAnimationFrame(function () {
      lightboxImg.style.transform = 'scale(1)';
    });
    if (lightboxCaption) {
      lightboxCaption.textContent = (state.lightboxIndex + 1) + ' / ' + state.lightboxImages.length;
    }
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove('is-open');
    if (!detailsModal.classList.contains('is-open') &&
        !videoModal.classList.contains('is-open')) {
      document.body.classList.remove('pj-lock');
    }
  }

  /* ---------- Events ---------- */
  if (els.filters) {
    els.filters.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-filter]');
      if (!btn) return;
      state.filter = btn.getAttribute('data-filter');
      renderFilters();
      renderCards();
    });
  }

  if (els.search) {
    els.search.addEventListener('input', function () {
      state.query = els.search.value || '';
      renderCards();
    });
  }

  root.addEventListener('click', function (e) {
    var detailsBtn = e.target.closest('[data-details-open]');
    if (detailsBtn) {
      openDetails(detailsBtn.getAttribute('data-project-id'));
      return;
    }
    var videoBtn = e.target.closest('[data-video-open]');
    if (videoBtn) {
      e.stopPropagation();
      openVideo(videoBtn.getAttribute('data-project-id'));
      return;
    }
    var media = e.target.closest('[data-lightbox-open]');
    if (media && !e.target.closest('[data-video-open]')) {
      openLightbox(media.getAttribute('data-project-id'), 0);
    }
  });

  if (detailsModal) {
    detailsModal.addEventListener('click', function (e) {
      if (e.target.matches('[data-close-details], .pj-modal-backdrop')) closeDetails();
      var thumb = e.target.closest('[data-thumb]');
      if (thumb) setDetailsMedia(parseInt(thumb.getAttribute('data-thumb'), 10) || 0);
      var videoBtn = e.target.closest('[data-video-open]');
      if (videoBtn) openVideo(videoBtn.getAttribute('data-project-id'));
    });
  }

  if (videoModal) {
    videoModal.addEventListener('click', function (e) {
      if (e.target.matches('[data-close-video], .pj-modal-backdrop')) closeVideo();
    });
  }

  if (lightbox) {
    lightbox.addEventListener('click', function (e) {
      if (e.target.matches('[data-close-lightbox], .pj-lightbox-backdrop')) closeLightbox();
      if (e.target.closest('[data-lightbox-prev]')) {
        state.lightboxIndex -= 1;
        showLightboxImage();
      }
      if (e.target.closest('[data-lightbox-next]')) {
        state.lightboxIndex += 1;
        showLightboxImage();
      }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeLightbox();
      closeVideo();
      closeDetails();
    }
    if (lightbox && lightbox.classList.contains('is-open')) {
      if (e.key === 'ArrowLeft') {
        state.lightboxIndex -= 1;
        showLightboxImage();
      }
      if (e.key === 'ArrowRight') {
        state.lightboxIndex += 1;
        showLightboxImage();
      }
    }
  });

  /* ---------- Init ---------- */
  fetch(dataUrl, { cache: 'no-store' })
    .then(function (res) {
      if (!res.ok) throw new Error('Failed to load projects');
      return res.json();
    })
    .then(function (data) {
      state.categories = data.categories || [{ id: 'all', label: 'All' }];
      state.projects = data.projects || [];
      renderFilters();
      renderCards();
    })
    .catch(function () {
      if (els.grid) {
        els.grid.innerHTML = '<div class="projects-empty">' + escapeHtml(i18n.loadError) + '</div>';
      }
    });
})();
