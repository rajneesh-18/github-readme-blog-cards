(function () {
  const form = document.getElementById('demo-form');
  const urlInput = document.getElementById('blog-url');
  const layoutSelect = document.getElementById('layout');
  const themeSelect = document.getElementById('theme');
  const previewImg = document.getElementById('card-preview');
  const previewSize = document.getElementById('preview-size');
  const resetBtn = document.getElementById('reset');
  const codeSection = document.querySelector('.preview-url');
  const htmlCode = document.getElementById('html-code');
  const htmlLabel = codeSection ? codeSection.querySelector('label') : null;
  const themeModeBtn = document.getElementById('theme-mode');
  const downloadBtn = document.getElementById('download-card');

  // Add new theme UI
  const addThemeBtn = document.getElementById('add-theme-btn');
  const newThemeForm = document.getElementById('new-theme-form');
  const newThemeName = document.getElementById('new-theme-name');
  const bgColor = document.getElementById('bg-color');
  const strokeColor = document.getElementById('stroke-color');
  const titleColor = document.getElementById('title-color');
  const descColor = document.getElementById('desc-color');
  const tagBgColor = document.getElementById('tag-bg-color');
  const tagTitleColor = document.getElementById('tag-title-color');
  const cancelCreateBtn = document.getElementById('cancel-create');
  const previewThemeBtn = document.getElementById('preview-theme');

  let themePreviewMode = false;

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

  // Code section helpers
  const showHtmlCode = (snippet) => {
    themePreviewMode = false;
    if (!codeSection || !htmlLabel || !htmlCode) return;
    htmlLabel.textContent = 'HTML Code';
    htmlCode.style.display = 'block';
    // remove any previous theme code UI
    const existingContainer = codeSection.querySelector('#theme-code-container');
    if (existingContainer) existingContainer.remove();
    const existingInstructions = codeSection.querySelector('.code-instructions');
    if (existingInstructions) existingInstructions.remove();
    htmlCode.value = snippet;
  };

  const attachCopyHandler = (btn, pre) => {
    if (!btn || !pre) return;
    btn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(pre.textContent || '');
        btn.textContent = 'Copied';
        setTimeout(() => (btn.textContent = 'Copy'), 1200);
      } catch (e) {
        // fallback
        const range = document.createRange();
        range.selectNodeContents(pre);
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
        document.execCommand('copy');
        sel.removeAllRanges();
        btn.textContent = 'Copied';
        setTimeout(() => (btn.textContent = 'Copy'), 1200);
      }
    });
  };

  const showThemeCode = (code) => {
    themePreviewMode = true;
    if (!codeSection || !htmlLabel) return;
    htmlLabel.textContent = 'Theme code';
    if (htmlCode) htmlCode.style.display = 'none';

    // remove previous container or instructions if any (prevent duplicates)
    const prev = codeSection.querySelector('#theme-code-container');
    if (prev) prev.remove();
    const prevInstructions = codeSection.querySelector('.code-instructions');
    if (prevInstructions) prevInstructions.remove();

    // container with copy button and pre
    const container = document.createElement('div');
    container.id = 'theme-code-container';
    container.className = 'code-wrapper';

    const copyBtn = document.createElement('button');
    copyBtn.type = 'button';
    copyBtn.id = 'copy-theme-code';
    copyBtn.className = 'copy-btn';
    copyBtn.textContent = 'Copy';

    const pre = document.createElement('pre');
    pre.id = 'theme-code-block';
    pre.className = 'code-block';
    pre.textContent = code;

    container.appendChild(copyBtn);
    container.appendChild(pre);

    // instructions
    const instructions = document.createElement('div');
    instructions.className = 'code-instructions';
    instructions.innerHTML = `
      <div class="instructions-title">Instructions</div>
      <ol>
        <li>Copy the generated array code and add it at the last of the array in <span class="file-chip">themes_list.php</span> (<span class="file-chip">src/theme/themes_list.php</span>).</li>
        <li>Download the image from the preview and add it to <span class="file-chip">images/themes</span> folder. Make sure the name matches the theme name.</li>
        <li>Add your theme in the theme table in <span class="file-chip">README</span>.</li>
      </ol>
    `;

    codeSection.appendChild(container);
    codeSection.appendChild(instructions);

    attachCopyHandler(copyBtn, pre);
  };

  const render = () => {
    const url = urlInput.value.trim();
    const layout = layoutSelect.value;
    const theme = themeSelect.value;

    if (!url) return;

    const previewSrc = paramsToUrl(url, layout, theme);
    previewImg.src = previewSrc;

    if (!themePreviewMode && htmlCode && htmlLabel) {
      showHtmlCode(buildHtmlSnippet(url, layout, theme));
    }
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
    themePreviewMode = false; // reset back to HTML mode
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

    // also reset the add-new-theme form colors and name
    resetThemeForm();

    themePreviewMode = false; // reset back to HTML mode
    render();
  });

  // Theme mode toggle
  const setThemeMode = (mode) => {
    document.documentElement.setAttribute('data-theme', mode);
    if (themeModeBtn) {
      themeModeBtn.textContent = mode === 'light' ? 'Dark mode' : 'Light mode';
    }
  };

  if (themeModeBtn) {
    themeModeBtn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      setThemeMode(next);
    });
  }

  // Default to dark mode
  setThemeMode('dark');

  // Download current SVG with the theme name
  const downloadCurrent = async () => {
    try {
      const url = previewImg.src;

      // Determine filename based on mode:
      // - If custom theme preview is active: use new theme name (lowercase, spaces -> dashes)
      // - Else: use the currently selected built-in theme
      const customKey =
        (newThemeName && newThemeName.value ? newThemeName.value : '')
          .trim()
          .toLowerCase()
          .replace(/\s+/g, '-');
      const filename =
        (themePreviewMode && customKey ? customKey : (themeSelect.value || 'card')) + '.svg';

      const resp = await fetch(url, { credentials: 'same-origin' });
      const blob = await resp.blob();

      const objectUrl = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = objectUrl;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(objectUrl);
    } catch (e) {
      console.error('Download failed', e);
    }
  };

  if (downloadBtn) {
    downloadBtn.addEventListener('click', downloadCurrent);
  }

  // Add new theme: open form (no toggle)
  if (addThemeBtn && newThemeForm) {
    addThemeBtn.addEventListener('click', () => {
      newThemeForm.classList.remove('hidden');
      newThemeForm.setAttribute('aria-hidden', 'false');
    });
  }

  // Persist custom themes locally
  const loadCustomThemes = () => {
    try {
      const raw = localStorage.getItem('customThemes');
      return raw ? JSON.parse(raw) : {};
    } catch {
      return {};
    }
  };
  const saveCustomThemes = (themes) => {
    try {
      localStorage.setItem('customThemes', JSON.stringify(themes));
    } catch {}
  };

  const addThemeOption = (name) => {
    const opt = document.createElement('option');
    opt.value = name;
    opt.textContent = name.toLowerCase();
    themeSelect.appendChild(opt);
  };

  // Initialize dropdown with any existing custom themes
  const customThemes = loadCustomThemes();
  Object.keys(customThemes).forEach(addThemeOption);

  // Reset helper for new theme form colors and name
  const resetThemeForm = () => {
    const defaults = {
      bg: '#FDFDFF',
      stroke: '#E4E2E2',
      title: '#121212',
      desc: '#555555',
      tagBg: '#F2F0EF',
      tagTitle: '#333333',
    };

    const pairs = [
      { text: 'bg-color-text', color: 'bg-color', value: defaults.bg },
      { text: 'stroke-color-text', color: 'stroke-color', value: defaults.stroke },
      { text: 'title-color-text', color: 'title-color', value: defaults.title },
      { text: 'desc-color-text', color: 'desc-color', value: defaults.desc },
      { text: 'tag-bg-color-text', color: 'tag-bg-color', value: defaults.tagBg },
      { text: 'tag-title-color-text', color: 'tag-title-color', value: defaults.tagTitle },
    ];

    pairs.forEach(({ text, color, value }) => {
      const t = document.getElementById(text);
      const c = document.getElementById(color);
      if (t) t.value = value;
      if (c) c.value = value;
    });

    // clear name + any validation errors
    if (newThemeName) newThemeName.value = '';
    clearThemeNameError();
  };

  // Cancel create
  if (cancelCreateBtn && newThemeForm) {
    cancelCreateBtn.addEventListener('click', () => {
      newThemeForm.classList.add('hidden');
      newThemeForm.setAttribute('aria-hidden', 'true');
      // empty the new theme input field
      if (newThemeName) newThemeName.value = '';
      // restore HTML code section
      themePreviewMode = false;
      // reset theme form (name + colors)
      if (typeof resetThemeForm === 'function') {
        resetThemeForm();
      }
      render();
    });
  }

  // Validation helpers for theme name
  const showThemeNameError = (msg) => {
    if (!newThemeName) return;
    newThemeName.classList.add('input-error');
    const row = newThemeName.closest('.form-row');
    if (row) {
      let hint = row.querySelector('.error-text');
      if (!hint) {
        hint = document.createElement('small');
        hint.className = 'error-text';
        row.appendChild(hint);
      }
      hint.textContent = msg;
    }
  };
  const clearThemeNameError = () => {
    if (!newThemeName) return;
    newThemeName.classList.remove('input-error');
    const row = newThemeName.closest('.form-row');
    if (row) {
      const hint = row.querySelector('.error-text');
      if (hint) hint.remove();
    }
  };
  if (newThemeName) {
    newThemeName.addEventListener('input', () => {
      if (newThemeName.value && newThemeName.classList.contains('input-error')) {
        clearThemeNameError();
      }
    });
  }

  const getTextValue = (id) => {
    const el = document.getElementById(id);
    return el ? el.value : '';
  };

  const buildThemeCodeSnippet = () => {
    const nameRaw = (newThemeName.value || '').trim();
    if (!nameRaw) return '';
    const key = nameRaw.toLowerCase().replace(/\s+/g, '-');

    return `'${key}' => [
        'background' => '${getTextValue('bg-color-text') || '#FFFFFF'}',
        'stroke' => '${getTextValue('stroke-color-text') || '#000000'}',
        'title' => '${getTextValue('title-color-text') || '#000000'}',
        'description' => '${getTextValue('desc-color-text') || '#000000'}',
        'tagBackground' => '${getTextValue('tag-bg-color-text') || '#FFFFFF'}',
        'tagTitle' => '${getTextValue('tag-title-color-text') || '#000000'}',
    ],`;
  };

  // Helper: fetch server SVG (default theme is fine, we'll recolor)
  const fetchServerSvg = async (blogUrl, layout, baseTheme = 'default') => {
    const q = new URLSearchParams({ url: blogUrl, layout, theme: baseTheme });
    const resp = await fetch('/?' + q.toString(), { credentials: 'same-origin' });
    if (!resp.ok) throw new Error('Failed to fetch SVG');
    return await resp.text();
  };

  // Helper: update colors inside the SVG's embedded CSS
  const applyColorsToSvg = (svgText, colors) => {
    // Replace .title fill
    svgText = svgText.replace(/(\.title\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.title}$3`);
    // Replace .description fill
    svgText = svgText.replace(/(\.description\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.description}$3`);
    // Replace .card-bg fill and stroke
    svgText = svgText.replace(/(\.card-bg\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.background}$3`);
    svgText = svgText.replace(/(\.card-bg\s*\{[^}]*?stroke:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.stroke}$3`);
    // Replace .tag fill (background)
    svgText = svgText.replace(/(\.tag\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.tagBackground}$3`);
    // Replace .tagTitle fill
    svgText = svgText.replace(/(\.tagTitle\s*\{[^}]*?fill:\s*)(#[0-9a-fA-F]{3,6})(\s*;)/, `$1${colors.tagTitle}$3`);

    return svgText;
  };

  // Preview custom theme code into a <pre> block (and hide textarea)
  previewThemeBtn.addEventListener('click', async () => {
    const nameRaw = (newThemeName && newThemeName.value ? newThemeName.value : '').trim();
    if (!nameRaw) {
      showThemeNameError('name is required. lower case single word, if multiple separate by -');
      return;
    }
    clearThemeNameError();

    // 1) Show theme code block and instructions
    const code = buildThemeCodeSnippet();
    showThemeCode(code);

    // 2) Recolor server SVG and show preview inline (no server render for themed colors)
    try {
      const blogUrl = urlInput.value.trim();
      const layout = layoutSelect.value;

      if (blogUrl) {
        const baseSvg = await fetchServerSvg(blogUrl, layout, 'default');

        const colors = {
          background: getTextValue('bg-color-text') || '#FDFDFF',
          stroke: getTextValue('stroke-color-text') || '#E4E2E2',
          title: getTextValue('title-color-text') || '#121212',
          description: getTextValue('desc-color-text') || '#555555',
          tagBackground: getTextValue('tag-bg-color-text') || '#F2F0EF',
          tagTitle: getTextValue('tag-title-color-text') || '#333333',
        };

        const themedSvg = applyColorsToSvg(baseSvg, colors);

        // Display via data URL
        const dataUrl = 'data:image/svg+xml;utf8,' + encodeURIComponent(themedSvg);
        previewImg.src = dataUrl;
      }
    } catch (e) {
      // If anything fails, keep the previous preview and do nothing
      console.error('Preview recolor failed', e);
    }

    themePreviewMode = true;
  });

  initFromQuery();
})();
