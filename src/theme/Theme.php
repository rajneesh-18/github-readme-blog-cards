<?php

class Theme
{
    public function __construct() {}

    /**
     * Get the theme mapping for the card
     *
     * @return array<string,string> theme mapping
     */
    public function getTheme(): array
    {
        $THEMES = include __DIR__ . '/themes_list.php';

        return $THEMES['default'];
    }
}
