<?php

class Card
{
    /*
      necessary styling variables for the blog card :-
        - blogURL : the url of the blog
        - cardWidth : the width of the blog card
        - cardHeight : the height of the blog card
        - padding : the padding around the card (esp the blog image)
        - lineHeight : the line height value for the title and description
    */
    private string $blogURL;
    private int $cardWidth = 285;
    private int $cardHeight = 320;
    private int $padding = 10;
    private int $titleFontSize = 14;
    private int $desciptionFontSize = 12;
    private int $tagFontSize = 13;
    private int $lineHeight = 20;

    /*
      receiving the blogURL from the params
    */
    public function __construct(string $blogURL)
    {
        $this->blogURL = $blogURL;
    }

    /*
      the main function responsible for returning the card SVG
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

    /*
      generates the SVG for the card
    */
    private function generateSVG($meta): string
    {
        $imageSVG = $this->renderImage($meta);
        $titleData = $this->renderTitle($meta);

        $titleSVG = $titleData['svg'];
        $titleLines = $titleData['lineCount'];

        $descriptionSVG = $this->renderDescription($meta, $titleLines);

        $blogWebsiteTag = $this->renderBlogWebsiteTag($meta);

        return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='isolation: isolate' viewBox='0 0 {$this->cardWidth} {$this->cardHeight}' width='{$this->cardWidth}px' height='{$this->cardHeight}px'>
            <style>
                .title {
                    font: bold {$this->titleFontSize}px sans-serif;
                    fill: #333;
                }

                .desc {
                    font: {$this->desciptionFontSize}px sans-serif;
                    fill: #555;
                }

                .card-bg {
                    fill: #fefefe;
                    stroke: rgba(0,0,0,0.2);
                    stroke-width: 2;
                    rx: 10;
                    ry: 10;
                }

                .tag {
                    font: bold {$this->tagFontSize}px sans-serif;
                    fill: #333;
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

    /*
      fetches the HTML content for the blog URL and enforces the encoding to UTF-8
    */
    private function fetchHTML($url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: PHP\r\n",
            ],
        ]);
        $html = @file_get_contents($url, false, $context);
        if ($html !== false) {
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $html ?: null;
    }

    /*
       extracts the necessary metadata - image, title and description from the HTML content fetched previously
       - again sets an encoding enforcement as a safe guard

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

    /* 
       returns the SVG for the blog image
       - height and width of image are calculated from the dimensions of the card 
    */
    private function renderImage($meta): string
    {
        $image = htmlspecialchars($meta['image'] ?? '');
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

    /* 
       returns the SVG and number of rendered lines for the blog title
       - title position is calculated based on the line height, padding and image dimensions
    */
    private function renderTitle($meta): array
    {
        $title = htmlspecialchars($meta['title'] ?? 'No Title');

        $titleXPosition = $this->padding;
        $titleYPosition = $this->cardHeight / 2 + $this->lineHeight + $this->padding * 1.5;
        $titleData = $this->renderText($title, 35, 10, 30, $this->lineHeight, 3);
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
        $description = htmlspecialchars($meta['description'] ?? 'No description available.');

        $descriptionXPosition = $this->padding;
        $descriptionYPosition = $this->cardHeight / 2 + ($titleLines + 1) * $this->lineHeight + $this->padding * 2;

        // Limit description lines based on title lines
        $maxDescLines = max(4 - $titleLines, 1);
        $descriptionData = $this->renderText($description, 45, 10, 30, $this->lineHeight, $maxDescLines);
        $descriptionSpans = $descriptionData['svg'];

        return "
         <text 
            x='{$descriptionXPosition}' 
            y='{$descriptionYPosition}' 
            class='desc'>{$descriptionSpans}</text> 
        ";
    }

    /*
      returns the SVG for the blog website name and favicon tag
    */
    private function renderBlogWebsiteTag($meta): string
    {
        $siteName = htmlspecialchars($meta['site_name'] ?? '');
        $favicon = htmlspecialchars($meta['favicon'] ?? '');
        $encodedImage = $this->fetchAndEncodeImage($favicon);
        if (!$encodedImage) {
            return '';
        }

        $fontSize = $this->tagFontSize;
        $padding = 7; // inner padding for the tag box
        $faviconSize = 18;
        $gap = 7; // space between favicon and text

        // Estimate text width
        $avgCharWidth = 0.6 * $fontSize;
        $textWidth = strlen($siteName) * $avgCharWidth;

        // Total tag box width = favicon + gap + text + 2*padding
        $boxWidth = $faviconSize + $gap + $textWidth + 2 * $padding;
        $boxHeight = $faviconSize + 2 * $padding;

        // Box position: bottom right
        $boxX = $this->cardWidth - $this->padding - $boxWidth;
        $boxY = $this->cardHeight - $this->padding - $boxHeight;

        // Position elements inside box
        $faviconX = $boxX + $padding;
        $faviconY = $boxY + $padding;

        $textX = $faviconX + $faviconSize + $gap;
        $textY = $faviconY + $fontSize * 1.05;

        return "
            <g>
                <rect 
                x='{$boxX}' 
                y='{$boxY}' 
                width='{$boxWidth}' 
                height='{$boxHeight}' 
                rx='8' 
                ry='8' 
                fill='rgba(0,0,0,0.05)' 
                />
                <image 
                x='{$faviconX}' 
                y='{$faviconY}' 
                width='{$faviconSize}' 
                height='{$faviconSize}' 
                href='{$encodedImage}' 
                preserveAspectRatio='xMidYMid slice'
                />
                <text 
                x='{$textX}' 
                y='{$textY}' 
                class='tag' 
                >
                {$siteName}
                </text>
            </g>
        ";
    }

    /*
      this functions returns the tspan tags and number of lines for a piece of text. It renders a text into multiple lines
      params are :-
        - text : the text content
        - maxChars : the maximum characters allowed in each line
        - x : the x position for each line
        - y : the y position for each line
        - lineHeight : the line height for the content
        - maxLines : maximum number of lines allowed for text to truncate it

    */
    private function renderText(string $text, int $maxChars, int $x, int $y, int $lineHeight, int $maxLines): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = trim($currentLine . ' ' . $word);

            // Check if adding this word would exceed character limit
            if (strlen($testLine) > $maxChars) {
                // If current line is empty, we need to force-fit the word
                if (empty($currentLine)) {
                    $lines[] = substr($word, 0, $maxChars - 3) . '...';
                    break;
                } else {
                    // Add current line and start new one with current word
                    $lines[] = $currentLine;
                    $currentLine = $word;

                    // Check if we've reached max lines
                    if (count($lines) >= $maxLines) {
                        break;
                    }
                }
            } else {
                $currentLine = $testLine;
            }
        }

        // Handle the final line
        if (!empty($currentLine) && count($lines) < $maxLines) {
            $lines[] = $currentLine;
        }

        // If we have more content than allowed lines, truncate the last line and add ellipsis
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $lastLine = $lines[$maxLines - 1];
            $lines[$maxLines - 1] = substr($lastLine, 0, $maxChars - 3) . '...';
        } elseif (count($lines) === $maxLines) {
            // Check if there are remaining words that didn't fit
            $allProcessedText = implode(' ', $lines);
            if (strlen($text) > strlen($allProcessedText)) {
                $lastLine = $lines[$maxLines - 1];
                $lines[$maxLines - 1] = substr($lastLine, 0, $maxChars - 3) . '...';
            }
        }

        // Generate SVG
        $svgText = '';
        foreach ($lines as $index => $line) {
            $dy = $index === 0 ? 0 : $lineHeight;
            $svgText .= "<tspan x=\"$x\" dy=\"$dy\">$line</tspan>";
        }

        return [
            'svg' => $svgText,
            'lineCount' => count($lines),
        ];
    }

    /*
       converts the image URL to base64
    */
    private function fetchAndEncodeImage($url)
    {
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return null;
        }

        // using mime_content_type to get accurate MIME type
        $mimeType = @mime_content_type($url);
        if ($mimeType === false) {
            $mimeType = 'image/png';
        }

        $base64 = base64_encode($imageData);
        return "data:{$mimeType};base64,{$base64}";
    }
}
