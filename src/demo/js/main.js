(function () {
  const form = document.getElementById("demo-form");
  const urlInput = document.getElementById("blog-url");
  const layoutSelect = document.getElementById("layout");
  const themeSelect = document.getElementById("theme");
  const previewImg = document.getElementById("card-preview");
  const shareUrl = document.getElementById("share-url");
  const previewSize = document.getElementById("preview-size");
  const resetBtn = document.getElementById("reset");

  const paramsToUrl = (url, layout, theme) => {
    const base = "/";
    const q = new URLSearchParams({
      url,
      layout,
      theme,
    });
    return base + "?" + q.toString();
  };

  const render = () => {
    const url = urlInput.value.trim();
    const layout = layoutSelect.value;
    const theme = themeSelect.value;

    if (!url) return;

    const previewSrc = paramsToUrl(url, layout, theme);
    previewImg.src = previewSrc;
    shareUrl.value = window.location.origin + "/demo?url=" + encodeURIComponent(url) + "&layout=" + layout + "&theme=" + theme;
  };

  // Initialize from query params if present
  const initFromQuery = () => {
    const qs = new URLSearchParams(window.location.search);
    const url = qs.get("url");
    const layout = qs.get("layout");
    const theme = qs.get("theme");

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
      previewSize.textContent = "";
    }
  };

  previewImg.addEventListener("load", updateSizeLabel);

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    render();
  });

  [urlInput, layoutSelect, themeSelect].forEach((el) => {
    el.addEventListener("change", render);
    el.addEventListener("input", render);
  });

  resetBtn.addEventListener("click", () => {
    urlInput.value = "https://github.blog/changelog/";
    layoutSelect.value = "vertical";
    themeSelect.value = "default";
    render();
  });

  initFromQuery();
})();