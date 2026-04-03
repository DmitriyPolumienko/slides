# Figma Export

## Overview

The Figma export generates a JSON document that mirrors Figma's node tree structure. It can be imported by a Figma plugin to recreate the presentation.

## Document Structure

```json
{
  "type": "DOCUMENT",
  "name": "Presentation Title",
  "meta": {
    "presentation_id": 1,
    "template": "dark-esports-v1",
    "exported_at": "2024-01-01T00:00:00Z",
    "figma_plugin_version": "1.0"
  },
  "pages": [
    {
      "type": "PAGE",
      "name": "Slide 1",
      "frames": [
        {
          "type": "FRAME",
          "name": "slide_1",
          "width": 1920,
          "height": 1080,
          "children": [
            { "type": "FRAME", "name": "header_locked", "locked": true },
            { "type": "FRAME", "name": "content_area", "is_editable": true },
            { "type": "FRAME", "name": "footer_locked", "locked": true }
          ]
        }
      ]
    }
  ]
}
```

## Usage

1. Open a presentation in the builder
2. Click **Figma JSON** export button
3. Save the `.json` file
4. In Figma, use the plugin to import (plugin not included — this is the data contract)

## Locked vs Editable

- `"locked": true` — header/footer zones, should not be modified in Figma
- `"is_editable": true` — content area and individual slots, can be edited
