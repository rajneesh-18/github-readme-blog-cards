<?php
// Load themes for the select dropdown
$themes = include __DIR__ . '/../theme/themes_list.php';

// Determine defaults
$defaultLayout = 'vertical';
$defaultTheme = 'default';
$defaultUrl = 'https://medium.com/@RitikaAgrawal08/exploring-css-flexbox-getting-started-with-the-basics-1174eea3ad4e';

// safe default blog URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Card Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Demo CSS served via public/demo-css.php (assets live in src/demo/css) -->
    <link rel="stylesheet" href="/demo-css">
</head>
<body>
    <header class="demo-header">
        <div>
            <h1>GitHub Readme Blog Cards</h1>
            <p>Preview SVG blog cards with different layouts and themes.</p>
        </div>
        <button class="theme-toggle" id="theme-mode" aria-label="Toggle theme">Light mode</button>
    </header>

    <main class="demo-container">
        <section class="controls">
            <form id="demo-form">
                <div class="form-group">
                    <label for="blog-url">Blog URL</label>
                    <input type="url" id="blog-url" name="url" placeholder="https://example.com/blog/post" value="<?php echo htmlspecialchars(
                        $defaultUrl,
                    ); ?>" required>
                </div>

                <div class="form-group">
                    <label for="layout">Layout</label>
                    <select id="layout" name="layout">
                        <option value="horizontal">horizontal</option>
                        <option value="vertical" selected>vertical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="theme">Theme</label>
                    <select id="theme" name="theme">
                        <?php foreach ($themes as $name => $_): ?>
                            <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $name === $defaultTheme ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(strtolower(str_replace('-', ' ', $name))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="actions">
                    <button type="submit" id="apply">Apply</button>
                    <button type="button" id="reset">Reset</button>
                </div>
            </form>
        </section>

        <section class="preview">
            <div class="preview-header">
                <span>Preview</span>
                <small id="preview-size"></small>
            </div>
            <div class="preview-stage">
                <!-- Rendered via public/index.php which outputs SVG -->
                <?php
                  $initialSrc = '/?url=' . urlencode($defaultUrl) . '&layout=' . urlencode($defaultLayout) . '&theme=' . urlencode($defaultTheme);
                ?>
                <img id="card-preview" alt="Blog card preview" src="<?php echo $initialSrc; ?>" />
            </div>
            <div class="preview-url">
                <label>HTML Code</label>
                <textarea id="html-code" readonly></textarea>
            </div>
        </section>
    </main>
    <!-- Demo JS served via public/demo-js.php (assets live in src/demo/js) -->
    <script src="/demo-js"></script>
</body>
</html>