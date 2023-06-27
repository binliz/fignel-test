<?php

class StyleParser
{
    private array $styles;
    private array $parsedStyles;

    public function __construct($styles)
    {
        $this->styles = $styles;
        $this->parseCssStyles();
    }

    public function parseCssStyles(): void
    {
        foreach ($this->styles as $key => $style) {
            $this->parsedStyles['.class-' . $key] = $this->parseOneStyleBlock($style);
        }
    }

    private function parseOneStyleBlock(mixed $style): array|string|null
    {
        $result = [];
        foreach ($style as $key => $item) {
            $cssKey = match ($key) {
                'padding' => 'padding',
                'margin' => 'margin',
                'fontFamily' => 'font-family',
                'fontWeight' => 'font-weight',
                'fontSize' => 'font-size',
                'letterSpacing' => 'letter-spacing',
                'fills' => 'fills',
                'lineHeightPx' => 'line-height',
                'textDecoration' => 'text-decoration',
                'width' => 'width',
                'height' => 'height',
                'background' => 'background',
                default => null
            };
            if (!$cssKey) {
                continue;
            }

            $cssValue = match ($key) {
                'fills' => $this->convertFill($item),
                'background' => $this->convertColor($item),
                'textDecoration' => $this->getTextDecoration($item),
                'lineHeightPx' => $item . 'px',
                default => $item
            };

            if ($cssKey === 'fills') {
                $result = array_merge($result, $cssValue);
            } else {
                $result[$cssKey] = $cssValue;
            }
        }

        return $result;
    }

    private function convertFill(array $item): array
    {
        $result = [];

        $newItem = current($item);

        if (property_exists($newItem, 'opacity')) {
            $result['opacity'] = $newItem->opacity;
        }
        if (property_exists($newItem, 'color')) {
            $result['color'] = $this->convertColor($newItem->color);
        }

        return $result;
    }

    private function cssArrayToCss(array $rules, int $indent = 0): string
    {
        $css = '';
        $prefix = str_repeat('  ', $indent);

        foreach ($rules as $key => $value) {
            if (is_array($value)) {
                $selector = $key;
                $properties = $value;

                $css .= $prefix . "$selector {\n";
                $css .= $prefix . $this->cssArrayToCss($properties, $indent + 1);
                $css .= $prefix . "}\n";
            } else {
                $property = $key;
                $css .= $prefix . "$property: $value;\n";
            }
        }

        return $css;
    }

    private function getTextDecoration(string $item): string
    {
        if ($item === 'UNDERLINE') {
            return 'underline';
        }
        if ($item === 'STRIKETHROUGH') {
            return 'line-through';
        }

        return '';
    }

    public function addStyles($styleOverrideTable): void
    {
        foreach ($styleOverrideTable as $key => $style) {
            $this->parsedStyles['.class-' . $key] = $this->parseOneStyleBlock($style);
        }
    }

    public function addStyle(stdClass $style, string $key): void
    {
        $this->parsedStyles['.class-' . $key] = array_merge(
            $this->parsedStyles['.class-' . $key],
            $this->parseOneStyleBlock($style)
        );
    }

    public function getCssStyles(): string
    {
        return $this->cssArrayToCss($this->parsedStyles);
    }

    private function convertColor(stdClass $item): string
    {
        if (property_exists($item, 'a')) {
            return "rgba(" . intval($item->r * 255) . ',' . intval(
                    $item->g * 255
                ) . ',' . intval($item->b * 255) . ',' . intval($item->a * 255) . ')';
        } else {
            return "rgb(" . intval($item->r * 255) . ',' . intval(
                    $item->g * 255
                ) . ',' . intval($item->b * 255) . ')';
        }
    }

}
