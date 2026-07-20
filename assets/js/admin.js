/**
 * Mustala Admin — upload zones (drag & drop + previews)
 */
(function () {
  'use strict';

  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function renderPreview(zone, files) {
    var preview = zone.querySelector('[data-upload-preview]');
    if (!preview) return;
    preview.innerHTML = '';
    if (!files || !files.length) return;

    Array.prototype.forEach.call(files, function (file) {
      var item = document.createElement('div');
      item.className = 'upload-preview-item';

      if (file.type && file.type.indexOf('image/') === 0) {
        var img = document.createElement('img');
        img.alt = file.name;
        img.src = URL.createObjectURL(file);
        item.appendChild(img);
      } else {
        var icon = document.createElement('div');
        icon.className = 'file-icon';
        icon.innerHTML = '<i class="fas fa-file"></i>';
        item.appendChild(icon);
      }

      var meta = document.createElement('div');
      meta.className = 'meta';
      meta.innerHTML = '<strong></strong><span></span>';
      meta.querySelector('strong').textContent = file.name;
      meta.querySelector('span').textContent = formatSize(file.size);
      item.appendChild(meta);
      preview.appendChild(item);
    });
  }

  function initZone(zone) {
    var input = zone.querySelector('[data-upload-input]');
    var browse = zone.querySelector('[data-upload-browse]');
    if (!input) return;

    if (browse) {
      browse.addEventListener('click', function (e) {
        e.preventDefault();
        input.click();
      });
    }

    zone.addEventListener('click', function (e) {
      if (e.target.closest('[data-upload-browse]') || e.target === input) return;
      if (e.target.closest('.upload-preview-item')) return;
      input.click();
    });

    input.addEventListener('change', function () {
      renderPreview(zone, input.files);
      zone.classList.toggle('has-files', input.files && input.files.length > 0);
    });

    ['dragenter', 'dragover'].forEach(function (evt) {
      zone.addEventListener(evt, function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(function (evt) {
      zone.addEventListener(evt, function (e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('is-dragover');
      });
    });

    zone.addEventListener('drop', function (e) {
      var dt = e.dataTransfer;
      if (!dt || !dt.files || !dt.files.length) return;
      var multiple = input.hasAttribute('multiple');
      try {
        var transfer = new DataTransfer();
        var list = multiple ? dt.files : [dt.files[0]];
        Array.prototype.forEach.call(list, function (f) {
          if (f) transfer.items.add(f);
        });
        input.files = transfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      } catch (err) {
        // Older browsers: user can still use Browse
      }
    });
  }

  document.querySelectorAll('[data-upload-zone]').forEach(initZone);

  /* Theme color picker sync */
  document.querySelectorAll('input[type="color"][data-sync]').forEach(function (picker) {
    var targetId = picker.getAttribute('data-sync');
    var text = targetId ? document.getElementById(targetId) : null;
    if (!text) return;

    picker.addEventListener('input', function () {
      text.value = picker.value;
      updateThemePreview();
    });

    text.addEventListener('input', function () {
      if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(text.value.trim())) {
        picker.value = text.value.trim();
        updateThemePreview();
      }
    });
  });

  function updateThemePreview() {
    var map = [
      ['theme_crimson', 0],
      ['theme_crimson_deep', 1],
      ['theme_dark', 2],
      ['theme_card_dark', 3]
    ];
    var swatches = document.querySelectorAll('.theme-preview .swatch');
    map.forEach(function (pair) {
      var input = document.getElementById(pair[0]);
      if (input && swatches[pair[1]]) {
        swatches[pair[1]].style.background = input.value;
      }
    });
  }
})();
