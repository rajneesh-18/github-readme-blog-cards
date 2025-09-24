<?php
// Load themes for the select dropdown
$themes = include __DIR__ . '/../theme/themes_list.php';

// Determine defaults
$defaultLayout = 'vertical';
$defaultTheme = 'default';
$defaultUrl = 'https://github.blog/changelog/'; // safe default blog URL
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
        <h1>Blog Cards Demo</h1>
        <p>Preview SVG blog cards with different layouts and themes.</p>
    </header>

    <main class="demo-container">
        <section class="controls">
            <form id="demo-form">
                <div class="form-group">
                    <label for="blog-url">Blog URL</label>
                    <input type="url" id="blog-url" name="url" placeholder="https://example.com/blog/post" value="<?php echo htmlspecialchars($defaultUrl); ?>" required>
                </div>

                <div class="form-group">
                    <label for="layout">Layout</label>
                    <select id="layout" name="layout">
                        <option value="horizontal">Horizontal</option>
                        <option value="vertical" selected>Vertical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="theme">Theme</label>
                    <select id="theme" name="theme">
                        <?php foreach ($themes as $name => $_): ?>
                            <option value="<?php echo htmlspecialchars($name); ?>" <?php echo $name === $defaultTheme ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $name))); ?>
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
                <img id="card-preview" alt="Blog card preview" src="/" />
            </div>
            <div class="preview-url">
                <label>Shareable URL</label>
                <input type="text" id="share-url" readonly>
            </div>
        </section>
    </main>

    <footer class="demo-footer">
        <p>Route: <code>/demo</code>. The preview requests <code>/</code> with query params and returns an SVG.</p>
    </footer>

    <!-- Demo JS served via public/demo-js.php (assets live in src/demo/js) -->
    <script src="/demo-js"></script>
</body>
</html>