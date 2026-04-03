<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; }
        .slide { width: 100%; height: 100vh; page-break-after: always; position: relative; overflow: hidden; }
        .slide:last-child { page-break-after: auto; }
        .header { position: absolute; top: 0; left: 0; right: 0; display: flex; align-items: center; padding: 0 24px; }
        .footer { position: absolute; bottom: 0; left: 0; right: 0; display: flex; align-items: center; padding: 0 24px; justify-content: space-between; }
        .content { position: absolute; left: 0; right: 0; padding: 16px 32px; overflow: hidden; }
        .title { font-size: 28px; font-weight: bold; margin-bottom: 12px; }
        .body-text { font-size: 14px; line-height: 1.6; }
        .slot { margin-bottom: 12px; }
        .slot-label { font-size: 9px; opacity: 0.5; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    </style>
</head>
<body>
@foreach($presentation->slides as $index => $slide)
@php
    $template = $presentation->masterTemplate?->activeVersion();
    $locked = $template?->locked_zones ?? [];
    $bgColor = $template?->schema['background'] ?? '#1a1a2e';
    $headerBg = $locked['header']['background'] ?? '#111';
    $headerColor = $locked['header']['text_color'] ?? '#fff';
    $footerBg = $locked['footer']['background'] ?? '#111';
    $footerColor = $locked['footer']['text_color'] ?? '#888';
    $headerH = $locked['header']['height_px'] ?? 80;
    $footerH = $locked['footer']['height_px'] ?? 60;
@endphp
<div class="slide" style="background-color: {{ $bgColor }};">
    @if(isset($locked['header']))
    <div class="header" style="background-color: {{ $headerBg }}; height: {{ $headerH }}px; color: {{ $headerColor }};">
        <strong style="font-size: 12px;">{{ $presentation->project?->name ?? 'Project' }}</strong>
    </div>
    @endif

    <div class="content" style="top: {{ $headerH }}px; bottom: {{ $footerH }}px; color: {{ $headerColor }};">
        @foreach($slide->slots as $slot)
        <div class="slot">
            <div class="slot-label">{{ $slot->slot_key }}</div>
            @if($slot->slot_type === 'text')
                <div class="{{ $slot->slot_key === 'title' ? 'title' : 'body-text' }}">{{ $slot->content }}</div>
            @elseif($slot->slot_type === 'chart')
                <div style="border: 1px solid rgba(255,255,255,0.2); padding: 8px; border-radius: 4px;">
                    [Chart: {{ $slot->slot_key }}]
                    @php $chartData = json_decode($slot->content, true) ?? []; @endphp
                    @if(isset($chartData['labels']))
                        @foreach($chartData['labels'] as $i => $label)
                            <span style="font-size: 10px; margin-right: 8px;">{{ $label }}: {{ $chartData['values'][$i] ?? '' }}</span>
                        @endforeach
                    @endif
                </div>
            @endif
        </div>
        @endforeach
    </div>

    @if(isset($locked['footer']))
    <div class="footer" style="background-color: {{ $footerBg }}; height: {{ $footerH }}px; color: {{ $footerColor }};">
        <span style="font-size: 10px; opacity: 0.6;">{{ $presentation->title }}</span>
        <span style="font-size: 10px;">{{ $index + 1 }} / {{ $presentation->slides->count() }}</span>
    </div>
    @endif
</div>
@endforeach
</body>
</html>
