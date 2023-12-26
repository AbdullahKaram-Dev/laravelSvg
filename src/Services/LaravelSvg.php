<?php

namespace Abdullah\LaravelSvg\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @author abdullahkaramdev@gmail.com
 */
class LaravelSvg
{
    private const MINIMUM_WORDS_COUNT = 1;

    private const DEFAULT_SVG_TYPE = 'image/svg+xml';

    /**
     * @param array $settings
     * @param string $firstWord
     * @param string $lastWord
     * @param bool $withLogoText
     * @param string $svgTemplate
     * @param int $wordsCount
     */
    public function __construct(
        protected array  $settings = [],
        protected string $firstWord = '',
        protected string $lastWord = '',
        protected bool   $withLogoText = false,
        protected string $svgTemplate = '',
        protected int    $wordsCount = 0
    )
    {
        $this->settings = config('laravel-svg');
    }

    /**
     * @param $key
     * @return string
     */

    protected function getSetting($key): string
    {
        if (isset($this->settings[$key]) && !empty($this->settings[$key])) {
            return $this->settings[$key];
        }
        throw new \InvalidArgumentException("Invalid key {$key} passed to settings");
    }


    /**
     * @param string $words
     * @return $this
     */
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


    /**
     * @param string $words
     * @return void
     */
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

    /**
     * @param string $words
     * @return bool
     */
    private function isArabicWords(string $words): bool
    {
        $arabic = preg_match('/\p{Arabic}/u', $words);
        return $arabic;
    }

    /**
     * @param string|null $logoText
     * @return $this
     */
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


    /**
     * @return array
     */
    public function generate(): array
    {
        $this->buildSvg();
        return $this->saveSvg();
    }

    /**
     * @return array
     */
    protected function saveSvg(): array
    {
        $this->checkDisk();
        if ($this->getSetting('hash_svg_name')) {
            $svgName = uniqid() . '.svg';
        } else {
            $svgName = $this->firstWord . '-' . $this->lastWord . '.svg';
        }
        File::put(public_path($this->getSetting('folder') . '/' . $this->getSetting('default_svg_path')) . '/' . $svgName, $this->svgTemplate);
        return [
            'name' => $svgName,
            'path' => $this->getSetting('default_svg_path') . '/' . $svgName,
            'full_path' => asset($this->getSetting('folder') . '/' . $this->getSetting('default_svg_path')) . '/' . $svgName,
            'mime_type' => self::DEFAULT_SVG_TYPE,
            'size' => File::size(public_path($this->getSetting('folder') . '/' . $this->getSetting('default_svg_path')) . '/' . $svgName),
            'disk' => 'public'
        ];
    }


    /**
     * @return void
     */
    protected function checkDisk(): void
    {
        if (!File::exists(public_path($this->getSetting('folder') . '/' . $this->getSetting('default_svg_path')))) {
            File::makeDirectory(public_path($this->getSetting('folder') . '/' . $this->getSetting('default_svg_path')));
        }
    }

    /**
     * @return void
     */
    protected function buildSvg(): void
    {
        $this->svgTemplate .= '<svg width="100%" height="100%" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">';
        $this->buildBackground();
        $this->buildSvgText();
        $this->buildSvgLogoText();
        $this->svgTemplate .= '</svg>';
        $this->replaceSvgTemplate();
    }

    /**
     * @return void
     */
    protected function buildSvgLogoText(): void
    {
        if ($this->withLogoText) {
            $this->svgTemplate .= '<text x="25" y="190" font-size="25" fill="{logo_text_color}">{logo_text}</text>';
        }
    }

    /**
     * @return void
     */
    protected function buildSvgText(): void
    {
        $this->svgTemplate .= '<text x="50%" y="50%" word-spacing="-20" text-anchor="middle" stroke="{avtar_text_color}" stroke-width="1px" dy=".3em" font-size="90">{firstChar} {secondChar}</text>';
    }

    /**
     * @return void
     */
    protected function buildBackground(): void
    {
        $this->svgTemplate .= '<rect width="100%" height="100%" rx="50" ry="50" fill="{avatar_background_color}" /><rect x="50" y="50" width="100" height="100" rx="50" ry="50" fill="{avtar_text_color}" />';
    }

    /**
     * @return void
     */
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
