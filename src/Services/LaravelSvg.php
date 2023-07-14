<?php

namespace Abdullah\LaravelSvg\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @author abdullahkaramdev@gmail.com
 */
class LaravelSvg
{

    protected readonly array $settings;

    protected readonly string $firstName;

    protected readonly string $lastName;

    protected bool $withLogoText = false;

    protected string $svgTemplate = '';


    protected readonly string $logoText;


    public function __construct()
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


    public function svgFor(string $userFullName): self
    {
        if (Str::wordCount($userFullName) < 2) {
            throw new \InvalidArgumentException('User full name must be at least 2 words');
        }
        $this->firstName = Str::before($userFullName, ' ');

        $this->lastName = Str::after($userFullName, ' ');

        return $this;
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
            $svgName = $this->firstName . '-' . $this->lastName . '.svg';
        }

        $svgPath = $this->getSetting('default_svg_path');
        $svg = $this->svgTemplate;
        $disk = $this->getSetting('disk');
        Storage::disk($disk)->put($svgPath . '/' . $svgName, $svg);
        return [
            'name' => $svgName,
            'path' => $svgPath.'/'.$svgName,
            'full_path' => Storage::disk($disk)->url($svgPath . '/' . $svgName),
            'mime_type' => 'image/svg+xml',
            'size' => Storage::disk($disk)->size($svgPath . '/' . $svgName),
            'disk' => $disk
        ];
    }

    protected function checkDisk(): void
    {
        $disk = $this->getSetting('disk');
        if (!Storage::disk($disk)->exists($this->getSetting('default_svg_path'))) {
            Storage::disk($disk)->makeDirectory($this->getSetting('default_svg_path'));
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
        $this->svgTemplate .= '<text x="50%" y="50%" text-anchor="middle" stroke="{avtar_text_color}" stroke-width="1px" dy=".3em" font-size="90">{firstChar}{secondChar}</text>';
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
                Str::upper($this->firstName[0]),
                Str::upper($this->lastName[0]),
                ($this->withLogoText ? $this->logoText : ''),
                ($this->withLogoText ? $this->getSetting('logo_text_color') : ''),
            ],
            $this->svgTemplate
        );
    }

}
