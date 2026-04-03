<?php

namespace App\Services\Export;

use App\Models\Presentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Color;

class PptxExportService
{
    public function export(Presentation $presentation): string
    {
        $presentation->load(['slides.slots', 'theme', 'project', 'masterTemplate.versions']);

        $phpPres = new PhpPresentation();
        $phpPres->removeSlideByIndex(0);

        $template = $presentation->masterTemplate?->activeVersion();
        $lockedZones = $template?->locked_zones ?? [];

        foreach ($presentation->slides as $slide) {
            $phpSlide = $phpPres->createSlide();

            if (isset($lockedZones['header'])) {
                $this->addHeader($phpSlide, $lockedZones['header'], $presentation);
            }

            foreach ($slide->slots as $slot) {
                if ($slot->slot_type === 'text') {
                    $this->addTextShape($phpSlide, $slot);
                }
            }

            if (isset($lockedZones['footer'])) {
                $this->addFooter($phpSlide, $lockedZones['footer'], $slide->order);
            }
        }

        $filename = 'presentation_' . $presentation->id . '_' . time() . '.pptx';
        $path = storage_path('app/exports/' . $filename);

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $writer = IOFactory::createWriter($phpPres, 'PowerPoint2007');
        $writer->save($path);

        return $path;
    }

    private function addHeader($slide, array $config, Presentation $presentation): void
    {
        $shape = $slide->createRichTextShape()
            ->setHeight(60)->setWidth(720)->setOffsetX(0)->setOffsetY(0);
        $shape->createTextRun($presentation->project?->name ?? 'Presentation')
            ->getFont()->setSize(14)->setBold(true)
            ->setColor(new Color('FF' . ltrim($config['text_color'] ?? '#ffffff', '#')));
    }

    private function addFooter($slide, array $config, int $pageNum): void
    {
        $shape = $slide->createRichTextShape()
            ->setHeight(40)->setWidth(720)->setOffsetX(0)->setOffsetY(500);
        $shape->createTextRun("Page {$pageNum}")
            ->getFont()->setSize(10)
            ->setColor(new Color('FF' . ltrim($config['text_color'] ?? '#888888', '#')));
    }

    private function addTextShape($slide, $slot): void
    {
        $shape = $slide->createRichTextShape()
            ->setHeight(100)->setWidth(600)->setOffsetX(60)->setOffsetY(100);
        $content = is_string($slot->content) ? $slot->content : json_encode($slot->content);
        $shape->createTextRun($content)->getFont()->setSize(16);
    }
}
