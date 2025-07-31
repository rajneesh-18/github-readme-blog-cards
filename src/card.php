<?php

require_once __DIR__ . '/text.php';

class Card
{
    /**
     * necessary styling variables for the blog card :-
     * blogURL: url of the blog
     * cardWidth: width of the blog card
     * cardHeight: height of the blog card
     * padding: padding around the card
     * lineHeight: line height value for title and description
     */
    private string $blogURL;
    private int $cardWidth = 265;
    private int $cardHeight = 315;
    private int $padding = 10;
    private int $titleFontSize = 14;
    private int $desciptionFontSize = 12;
    private int $tagFontSize = 12;
    private int $faviconSize = 16;
    private int $lineHeight = 20;

    /**
     * @param string blog URL
     */
    public function __construct(string $blogURL)
    {
        $this->blogURL = $blogURL;
    }

    /**
     * @return string the final blog card SVG
     */
    public function render(): string
    {
        $escapedURL = htmlspecialchars($this->blogURL);

        if ($escapedURL) {
            $html = $this->fetchHTML($escapedURL);
            if ($html) {
                $meta = $this->extractMetadata($html);
                return $this->generateSVG($meta);
            } else {
                echo 'Could not fetch the URL.';
            }
        }
    }

    /**
     * generates the SVG for the card
     *
     * @param array<string,string> the metadata for the blog
     * @return string the SVG for card
     */
    private function generateSVG($meta): string
    {
        // blog image
        $imageSVG = $this->renderImage($meta);

        // blog title
        $titleData = $this->renderTitle($meta);
        $titleSVG = $titleData['svg'];
        $titleLines = $titleData['lineCount'];

        // blog description
        $descriptionSVG = $this->renderDescription($meta, $titleLines);

        // blog website tag
        $blogWebsiteTag = $this->renderBlogWebsiteTag($meta);

        // card theme
        $theme = $this->getTheme();

        return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='isolation: isolate; margin: 15px;' viewBox='0 0 {$this->cardWidth} {$this->cardHeight}' width='{$this->cardWidth}px' height='{$this->cardHeight}px'>
            <style>

                .title {
                    font: bold {$this->titleFontSize}px sans-serif;
                    fill: {$theme['title']};
                }

                .description {
                    font: {$this->desciptionFontSize}px sans-serif;
                    fill: {$theme['description']};
                }

                .card-bg {
                    fill: {$theme['background']};
                    stroke: {$theme['stroke']};
                    stroke-width: 2;
                    rx: 10;
                    ry: 10;
                }

                .tag {
                  fill: {$theme['tagBackground']};
                  rx: 6; 
                  ry: 6; 
                }

                .tagTitle {
                    font: bold {$this->tagFontSize}px sans-serif;
                    fill: {$theme['tagTitle']};
                }

            </style>

            <!-- Card background -->
            <rect class='card-bg' x='0' y='0' width='{$this->cardWidth}' height='{$this->cardHeight}' rx='10' ry='10'/>
           
            <!-- Blog image (full width, half height) -->
            {$imageSVG}

            <!-- Title -->
            {$titleSVG}

            <!-- Description -->
            {$descriptionSVG}

            <!-- Blog Site Icon and Name -->
            {$blogWebsiteTag}

        </svg>";
    }

    /**
     * Fetch the HTML content for the blog URL and enforce the encoding to UTF-8
     *
     * @param string blog URL
     * @return string HTML content of blog page
     */

    private function fetchHTML($url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: PHP\r\n",
            ],
        ]);
        $html = @file_get_contents($url, false, $context);
        if ($html) {
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $html ?: null;
    }

    /**
     * extracts the necessary metadata - image, title and description from the HTML content fetched previously
     *
     * @param string HTML content of blog page
     * @return array<string,string> blog metadata mapping
     */
    private function extractMetadata($html): array
    {
        // Force UTF-8 for encoding
        if (stripos($html, '<meta charset=') === false) {
            $html = preg_replace('/<head[^>]*>/i', '$0<meta charset="UTF-8">', $html, 1);
        }

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $tags = [];
        foreach ($doc->getElementsByTagName('meta') as $meta) {
            $property = $meta->getAttribute('property') ?: $meta->getAttribute('name');
            $content = $meta->getAttribute('content');
            if ($property && $content) {
                $tags[$property] = $content;
            }
        }

        $title = $doc->getElementsByTagName('title')->item(0)?->nodeValue;

        // Extract site name from og:site_name if available
        $siteName = $tags['og:site_name'] ?? parse_url($url, PHP_URL_HOST);

        // Extract favicon
        $favicon = null;
        foreach ($doc->getElementsByTagName('link') as $link) {
            $rel = strtolower($link->getAttribute('rel'));
            if (in_array($rel, ['icon', 'shortcut icon', 'apple-touch-icon'])) {
                $href = $link->getAttribute('href');
                if ($href) {
                    // Resolve relative URLs
                    $favicon =
                        str_starts_with($href, 'http') || str_starts_with($href, '//')
                            ? $href
                            : rtrim($url, '/') . '/' . ltrim($href, '/');
                    break;
                }
            }
        }

        // Fallback to root domain favicon
        if (!$favicon) {
            $parsed = parse_url($url);
            $favicon = $parsed['scheme'] . '://' . $parsed['host'] . '/favicon.ico';
        }

        return [
            'title' => $tags['og:title'] ?? ($title ?? 'No title found'),
            'description' => $tags['og:description'] ?? ($tags['description'] ?? 'No description'),
            'image' => $tags['og:image'] ?? null,
            'site_name' => $siteName,
            'favicon' => $favicon,
        ];
    }

    /**
     * Generate SVG for the blog image
     *
     * @param array<string,string> blog metadata
     */
    private function renderImage($meta): string
    {
        $image = $meta['image'] ?? '';
        $encodedImage = $this->fetchAndEncodeImage($image);
        if (!$encodedImage) {
            return '';
        }

        $imageWidth = $this->cardWidth - $this->padding * 2;
        $imageHeight = $this->cardHeight / 2;

        return "
            <defs>
                <clipPath id='rounded-image-clip'>
                    <rect x='{$this->padding}' y='{$this->padding}' width='{$imageWidth}' height='{$imageHeight}' rx='10' ry='10' />
                </clipPath>
            </defs>

            <image 
                x='{$this->padding}' 
                y='{$this->padding}' 
                width='{$imageWidth}' 
                height='{$imageHeight}' 
                href='{$encodedImage}' 
                preserveAspectRatio='xMidYMid slice'
                clip-path='url(#rounded-image-clip)'
            />
        ";
    }

    /**
     * Generate SVG for blog title
     *
     * @param array<string,string> blog metadata
     * @return array<string,string> title svg and lines count
     */
    private function renderTitle($meta): array
    {
        $title = $meta['title'] ?? 'No Title';

        $titleXPosition = $this->padding;
        $titleYPosition = $this->cardHeight / 2 + $this->lineHeight + $this->padding * 1.5;

        $titleData = $this->renderText($title, $this->titleFontSize, 3, true);
        $titleSpans = $titleData['svg'];
        $titleLines = $titleData['lineCount'];

        $titleSVG = "
            <text 
                x='{$titleXPosition}' 
                y='{$titleYPosition}' 
                class='title'>{$titleSpans}</text>
        ";

        return [
            'svg' => $titleSVG,
            'lineCount' => $titleLines,
        ];
    }

    /* 
       returns the SVG for the blog description
       - description position is calculated based on the line height, padding, image dimensions and number of rendered lines for the blog title
    */
    private function renderDescription($meta, $titleLines): string
    {
        $description = $meta['description'] ?? 'No description available.';

        $descriptionXPosition = $this->padding;
        $descriptionYPosition = $this->cardHeight / 2 + ($titleLines + 1) * $this->lineHeight + $this->padding * 2;

        // Limit description lines based on title lines
        $maxDescLines = max(4 - $titleLines, 1);
        $descriptionData = $this->renderText($description, $this->desciptionFontSize, $maxDescLines, false);
        $descriptionSpans = $descriptionData['svg'];

        return "
         <text 
            x='{$descriptionXPosition}' 
            y='{$descriptionYPosition}' 
            class='description'>{$descriptionSpans}</text> 
        ";
    }

    /*
      returns the SVG for the blog website name and favicon tag
    */
    private function renderBlogWebsiteTag($meta): string
    {
        $siteName = htmlspecialchars($meta['site_name'] ?? '');
        $favicon = $meta['favicon'] ?? '';
        $encodedImage = $this->fetchAndEncodeImage($favicon);
        if (!$encodedImage) {
            return '';
        }

        $fontSize = $this->tagFontSize;
        $padding = 8; // inner padding for the tag box
        $gap = 7; // space between favicon and text

        // Estimate text width
        $avgCharWidth = 0.6 * $fontSize;
        $textWidth = strlen($siteName) * $avgCharWidth;

        // Total tag box width = favicon + gap + text + 2*padding
        $boxWidth = $this->faviconSize + $gap + $textWidth + 2 * $padding;
        $boxHeight = $this->faviconSize + 2 * $padding;

        // Box position: bottom right
        $boxX = $this->cardWidth - $this->padding - $boxWidth;
        $boxY = $this->cardHeight - $this->padding - $boxHeight;

        // Position elements inside box
        $faviconX = $boxX + $padding;
        $faviconY = $boxY + $padding;

        $textX = $faviconX + $this->faviconSize + $gap;
        $textY = $faviconY + $fontSize * 1.05;

        return "
            <g>
                <rect 
                    x='{$boxX}' 
                    y='{$boxY}' 
                    width='{$boxWidth}' 
                    height='{$boxHeight}' 
                    class='tag' 
                />
                <image 
                    x='{$faviconX}' 
                    y='{$faviconY}' 
                    width='{$this->faviconSize}' 
                    height='{$this->faviconSize}' 
                    href='{$encodedImage}' 
                    preserveAspectRatio='xMidYMid slice'
                />
                <text 
                    x='{$textX}' 
                    y='{$textY}' 
                    class='tagTitle'
                >
                 {$siteName}
                </text>
            </g>
        ";
    }

    /**
     * Generate the tspans and number of lines for the wrapped text
     *
     * @param string text
     * @param int font size
     * @param int maximum number of lines
     * @return array<string,string> SVG and lines count
     */
    private function renderText(string $text, int $fontSize, int $maxLines, bool $isBold): array
    {
        $textObj = new Text($this->cardWidth, $this->cardHeight, $this->padding, $fontSize, $this->lineHeight, $isBold);
        $lines = $textObj->wrapTextByWidth($text, $maxLines);

        // generate final SVG with tspans for each line
        $svgText = '';
        foreach ($lines as $index => $line) {
            $dy = $index === 0 ? 0 : $this->lineHeight;
            $formattedLine = htmlspecialchars($line);
            $svgText .= "<tspan x=\"$this->padding\" dy=\"$dy\">$formattedLine</tspan>";
        }

        return [
            'svg' => $svgText,
            'lineCount' => count($lines),
        ];
    }

    /**
     * Convert the image URL to base64
     *
     * @param string image URL
     * @return string base64 version of image
     */
    private function fetchAndEncodeImage($url): string
    {
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return null;
        }

        // fetch accurate MIME type
        $mimeType = @mime_content_type($url);
        if ($mimeType === false) {
            $mimeType = 'image/png';
        }

        $base64 = base64_encode($imageData);
        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Get the theme mapping for the card
     *
     * @return array<string,string> theme mapping
     */
    private function getTheme(): array
    {
        $THEMES = include __DIR__ . '/themes.php';

        return $THEMES['default'];
    }
}
