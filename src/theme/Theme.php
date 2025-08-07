<?php

class Theme
{
    private string $theme;

    public function __construct(string $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Get the theme mapping for the card
     *
     * @return array<string,string> theme mapping
     */
    public function getTheme(): array
    {
        /**
         * @var array<string,array<string,string>> $THEMES
         * List of theme names mapped to colors
         */
        $THEMES = include __DIR__ . '/themes_list.php';

        return $THEMES[$this->theme] ?? $THEMES['default'];
    }
}
