(function () {
  const form = document.getElementById('demo-form');
  const urlInput = document.getElementById('blog-url');
  const layoutSelect = document.getElementById('layout');
  const themeSelect = document.getElementById('theme');
  const previewImg = document.getElementById('card-preview');
  const previewSize = document.getElementById('preview-size');
  const resetBtn = document.getElementById('reset');
  const htmlCode = document.getElementById('html-code');
  const themeModeBtn = document.getElementById('theme-mode');
  const downloadBtn = document.getElementById('download-card');

  const paramsToUrl = (url, layout, theme) => {
    const base = '/';
    const q = new URLSearchParams({
      url,
      layout,
      theme,
    });
    return base + '?' + q.toString();
  };

  const buildHtmlSnippet = (hrefUrl, layout, theme) => {
    const imgSrc =
      'https://github-readme-blog-cards.onrender.com' +
      '?url=' +
      encodeURIComponent(hrefUrl) +
      '&layout=' +
      encodeURIComponent(layout) +
      '&theme=' +
      encodeURIComponent(theme);

    return `<a href="${hrefUrl}">
  <img src="${imgSrc}" alt="Blog Card"/>
</a>`;
  };

  const render = () => {
    const url = urlInput.value.trim();
    const layout = layoutSelect.value;
    const theme = themeSelect.value;

    if (!url) return;

    const previewSrc = paramsToUrl(url, layout, theme);
    previewImg.src = previewSrc;

    // Update embeddable HTML code
    htmlCode.value = buildHtmlSnippet(url, layout, theme);
  };

  // Initialize from query params if present
  const initFromQuery = () => {
    const qs = new URLSearchParams(window.location.search);
    const url = qs.get('url');
    const layout = qs.get('layout');
    const theme = qs.get('theme');

    if (url) urlInput.value = url;
    if (layout) layoutSelect.value = layout;
    if (theme) themeSelect.value = theme;

    render();
  };

  // Update preview size label based on image natural size
  const updateSizeLabel = () => {
    if (previewImg.naturalWidth && previewImg.naturalHeight) {
      previewSize.textContent = `${previewImg.naturalWidth} × ${previewImg.naturalHeight}px`;
    } else {
      previewSize.textContent = '';
    }
  };

  previewImg.addEventListener('load', updateSizeLabel);

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    render();
  });

  [urlInput, layoutSelect, themeSelect].forEach((el) => {
    el.addEventListener('change', render);
    el.addEventListener('input', render);
  });

  resetBtn.addEventListener('click', () => {
    urlInput.value =
      'https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e';
    layoutSelect.value = 'vertical';
    themeSelect.value = 'default';
    render();
  });

  // Theme mode toggle
  const setThemeMode = (mode) => {
    document.documentElement.setAttribute('data-theme', mode);
    themeModeBtn.textContent = mode === 'light' ? 'Dark mode' : 'Light mode';
  };

  themeModeBtn.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    setThemeMode(next);
  });

  // Default to dark mode
  setThemeMode('dark');

  // Download current SVG with the theme name
  const downloadCurrent = async () => {
    try {
      const url = previewImg.src;
      const theme = (themeSelect.value || 'card') + '.svg';

      const resp = await fetch(url, { credentials: 'same-origin' });
      const blob = await resp.blob();

      const objectUrl = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = objectUrl;
      a.download = theme;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(objectUrl);
    } catch (e) {
      // no-op; we keep UI minimal
      console.error('Download failed', e);
    }
  };

  downloadBtn.addEventListener('click', downloadCurrent);

  initFromQuery();
})();
