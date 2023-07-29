<?php

namespace Abdullah\LaravelSvg\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @author abdullahkaramdev@gmail.com
 */
class LaravelSvg
{
    private const MINIMUM_WORDS_COUNT = 2;

    private const DEFAULT_SVG_TYPE = 'image/svg+xml';

    public function __construct(
        protected array  $settings = [],
        protected string $firstWord = '',
        protected string $lastWord = '',
        protected bool   $withLogoText = false,
        protected string $svgTemplate = '',
        protected int $wordsCount = 0
    )
    {
        $this->settings = config('laravel-svg');
    }

    protected function getSetting($key): string
    {
        if (isset($this->settings[$key]) && !empty($this->settings[$key])) {
            return $this->settings[$key];
        }
        throw new \InvalidArgumentException("Invalid key {$key} passed to settings");
    }


    public function svgFor(string $words): self
    {
        $this->setCountWords($words);
        if ($this->wordsCount < self::MINIMUM_WORDS_COUNT) {
            throw new \InvalidArgumentException('Invalid words count passed to svgFor method');
        }

        $this->firstWord = strtoupper(Str::before($words, ' '));
        $this->lastWord = strtoupper(Str::after($words, ' '));

        return $this;
    }


    private function setCountWords(string $words): void
    {
        if ($this->isArabicWords($words)) {
            $ArabicWords = explode(' ', $words);
            foreach ($ArabicWords as $word) {
                $this->wordsCount++;
            }
        } else {
            $this->wordsCount = Str::wordCount($words);
        }
    }

    private function isArabicWords(string $words): bool
    {
        $arabic = preg_match('/\p{Arabic}/u', $words);
        return $arabic;
    }

    public function logoText(string $logoText = null): self
    {
        $this->withLogoText = true;
        if (!is_null($logoText)) {
            $this->logoText = $logoText;
        } else {
            $this->logoText = $this->getSetting('default_logo_text');
        }
        return $this;
    }


    public function generate(): array
    {
        $this->buildSvg();
        return $this->saveSvg();
    }

    protected function saveSvg(): array
    {
        $this->checkDisk();
        if ($this->getSetting('hash_svg_name')) {
            $svgName = uniqid() . '.svg';
        } else {
            $svgName = $this->firstWord . '-' . $this->lastWord . '.svg';
        }
        Storage::disk($this->getSetting('disk'))->put($this->getSetting('default_svg_path') . '/' . $svgName, $this->svgTemplate);
        return [
            'name' => $svgName,
            'path' => $this->getSetting('default_svg_path') . '/' . $svgName,
            'full_path' => Storage::disk($this->getSetting('disk'))->url($this->getSetting('default_svg_path') . '/' . $svgName),
            'mime_type' => self::DEFAULT_SVG_TYPE,
            'size' => Storage::disk($this->getSetting('disk'))->size($this->getSetting('default_svg_path') . '/' . $svgName),
            'disk' => $this->getSetting('disk')
        ];
    }

    protected function checkDisk(): void
    {
        if (!Storage::disk($this->getSetting('disk'))->exists($this->getSetting('default_svg_path'))) {
            Storage::disk($this->getSetting('disk'))->makeDirectory($this->getSetting('default_svg_path'));
        }
    }

    protected function buildSvg(): void
    {
        $this->svgTemplate .= '<svg width="100%" height="100%" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">';
        $this->buildBackground();
        $this->buildSvgText();
        $this->buildSvgLogoText();
        $this->svgTemplate .= '</svg>';
        $this->replaceSvgTemplate();
    }

    protected function buildSvgLogoText(): void
    {
        if ($this->withLogoText) {
            $this->svgTemplate .= '<text x="25" y="190" font-size="25" fill="{logo_text_color}">{logo_text}</text>';
        }
    }

    protected function buildSvgText(): void
    {
        $this->svgTemplate .= '<text x="50%" y="50%" word-spacing="-2.5" text-anchor="middle" stroke="{avtar_text_color}" stroke-width="1px" dy=".3em" font-size="90">{firstChar} {secondChar}</text>';
    }

    protected function buildBackground(): void
    {
        $this->svgTemplate .= '<rect width="100%" height="100%" rx="50" ry="50" fill="{avatar_background_color}" /><rect x="50" y="50" width="100" height="100" rx="50" ry="50" fill="{avtar_text_color}" />';
    }

    private function replaceSvgTemplate(): void
    {
        $this->svgTemplate = str_replace([
            '{avtar_text_color}',
            '{avatar_background_color}',
            '{firstChar}',
            '{secondChar}',
            '{logo_text}',
            '{logo_text_color}',
        ],
            [
                $this->getSetting('avatar_text_color'),
                $this->getSetting('avatar_background_color'),
                Str::substr($this->firstWord, 0, 1),
                Str::substr($this->lastWord, 0, 1),
                ($this->withLogoText ? $this->logoText : ''),
                ($this->withLogoText ? $this->getSetting('logo_text_color') : ''),
            ],
            $this->svgTemplate
        );
    }

}
