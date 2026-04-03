<?php

namespace App\Services\Export;

use App\Models\Presentation;

class FigmaExportService
{
    public function export(Presentation $presentation): array
    {
        $presentation->load(['slides.slots', 'theme', 'masterTemplate.versions']);

        $template = $presentation->masterTemplate?->activeVersion();
        $lockedZones = $template?->locked_zones ?? [];
        $theme = $presentation->theme;

        $figmaDocument = [
            'type' => 'DOCUMENT',
            'name' => $presentation->title,
            'meta' => [
                'presentation_id' => $presentation->id,
                'template' => $presentation->masterTemplate?->slug,
                'exported_at' => now()->toISOString(),
                'figma_plugin_version' => '1.0',
            ],
            'pages' => [],
        ];

        foreach ($presentation->slides as $index => $slide) {
            $page = [
                'type' => 'PAGE',
                'name' => 'Slide ' . ($index + 1),
                'slide_id' => $slide->id,
                'frames' => [],
            ];

            $slideFrame = [
                'type' => 'FRAME',
                'name' => 'slide_' . ($index + 1),
                'width' => 1920,
                'height' => 1080,
                'fill' => $theme ? ['type' => 'SOLID', 'color' => $this->hexToFigmaColor($theme->color_primary)] : null,
                'children' => [],
            ];

            if (isset($lockedZones['header'])) {
                $slideFrame['children'][] = $this->buildLockedZoneFrame('header', $lockedZones['header'], $presentation);
            }

            $contentFrame = [
                'type' => 'FRAME',
                'name' => 'content_area',
                'is_editable' => true,
                'locked' => false,
                'x' => 0,
                'y' => $lockedZones['header']['height_px'] ?? 80,
                'width' => 1920,
                'height' => 1080 - ($lockedZones['header']['height_px'] ?? 80) - ($lockedZones['footer']['height_px'] ?? 60),
                'children' => [],
            ];

            foreach ($slide->slots as $slot) {
                $contentFrame['children'][] = $this->buildSlotNode($slot);
            }

            $slideFrame['children'][] = $contentFrame;

            if (isset($lockedZones['footer'])) {
                $slideFrame['children'][] = $this->buildLockedZoneFrame('footer', $lockedZones['footer'], $presentation, $index + 1);
            }

            $page['frames'][] = $slideFrame;
            $figmaDocument['pages'][] = $page;
        }

        return $figmaDocument;
    }

    private function buildLockedZoneFrame(string $zone, array $config, Presentation $presentation, int $pageNum = 1): array
    {
        $y = $zone === 'footer' ? (1080 - ($config['height_px'] ?? 60)) : 0;

        $frame = [
            'type' => 'FRAME',
            'name' => $zone . '_locked',
            'locked' => true,
            'is_editable' => false,
            'x' => 0,
            'y' => $y,
            'width' => 1920,
            'height' => $config['height_px'] ?? 80,
            'fills' => [['type' => 'SOLID', 'color' => $this->hexToFigmaColor($config['background'] ?? '#000000')]],
            'children' => [],
        ];

        foreach ($config['elements'] ?? [] as $element) {
            $frame['children'][] = [
                'type' => 'TEXT',
                'name' => $element,
                'locked' => true,
                'content' => $this->getHeaderFooterElementContent($element, $presentation, $pageNum),
                'fills' => [['type' => 'SOLID', 'color' => $this->hexToFigmaColor($config['text_color'] ?? '#ffffff')]],
            ];
        }

        return $frame;
    }

    private function buildSlotNode(mixed $slot): array
    {
        $slotType = $slot->slot_type ?? 'text';

        if ($slotType === 'text') {
            return [
                'type' => 'TEXT',
                'name' => 'slot_' . $slot->slot_key,
                'slot_key' => $slot->slot_key,
                'locked' => $slot->is_locked,
                'is_editable' => !$slot->is_locked,
                'content' => is_string($slot->content) ? $slot->content : json_encode($slot->content),
                'style' => ['fontSize' => 16],
            ];
        }

        if ($slotType === 'chart') {
            return [
                'type' => 'RECTANGLE',
                'name' => 'chart_placeholder_' . $slot->slot_key,
                'slot_key' => $slot->slot_key,
                'locked' => $slot->is_locked,
                'is_editable' => !$slot->is_locked,
                'chart_data' => is_string($slot->content) ? json_decode($slot->content, true) : $slot->content,
                'fills' => [['type' => 'SOLID', 'color' => ['r' => 0.9, 'g' => 0.9, 'b' => 0.9, 'a' => 1]]],
                'cornerRadius' => 8,
            ];
        }

        return [
            'type' => 'RECTANGLE',
            'name' => 'slot_' . $slot->slot_key,
            'slot_key' => $slot->slot_key,
            'is_editable' => !$slot->is_locked,
        ];
    }

    private function hexToFigmaColor(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        return ['r' => round($r / 255, 4), 'g' => round($g / 255, 4), 'b' => round($b / 255, 4), 'a' => 1];
    }

    private function getHeaderFooterElementContent(string $element, Presentation $presentation, int $pageNum): string
    {
        return match ($element) {
            'logo' => '[LOGO]',
            'project_name' => $presentation->project?->name ?? 'Project',
            'date' => now()->format('Y-m-d'),
            'page_number' => (string) $pageNum,
            'confidentiality_label' => 'CONFIDENTIAL',
            default => $element,
        };
    }
}
