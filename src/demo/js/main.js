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
        <li>Copy the generated array code and add it at the last of the array in <code>themes_list.php</code> (<code>src/theme/themes_list.php</code>).</li>
        <li>Download the image from the preview and add it to <code>images/themes</code> folder. Make sure the name matches the theme name.</li>
        <li>Add your theme in the theme table in <code>README</code>.</li>
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
    themePreviewMode = false; // reset back to HTML mode
    render();
  });

  // Theme mode toggle
  const setThemeMode = (mode) => {
    document.documentElement.setAttribute('data-theme', mode);
    themeModeBtn.textContent = mode === 'light' ? 'Dark mode' : 'Light mode';
  };

  themeModeBtn.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
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
      console.error('Download failed', e);
    }
  };

  downloadBtn.addEventListener('click', downloadCurrent);

  // Add new theme: open form (no toggle)
  addThemeBtn.addEventListener('click', () => {
    newThemeForm.classList.remove('hidden');
    newThemeForm.setAttribute('aria-hidden', 'false');
  });

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

  // Cancel create
  cancelCreateBtn.addEventListener('click', () => {
    newThemeForm.classList.add('hidden');
    newThemeForm.setAttribute('aria-hidden', 'true');
    // empty the new theme input field
    if (newThemeName) newThemeName.value = '';
    // restore HTML code section
    themePreviewMode = false;
    render();
  });

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

  // Preview custom theme code into a <pre> block (and hide textarea)
  previewThemeBtn.addEventListener('click', () => {
    const nameRaw = (newThemeName && newThemeName.value ? newThemeName.value : '').trim();
    if (!nameRaw) {
      showThemeNameError('name is required. lower case single word, if multiple separate by -');
      return;
    }
    clearThemeNameError();
    const code = buildThemeCodeSnippet();
    showThemeCode(code);
  });

  initFromQuery();
})();
