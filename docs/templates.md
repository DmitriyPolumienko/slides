# Templates Guide

## Structure

A **MasterTemplate** represents a named template (e.g., "Dark Esports v1"). Each template can have multiple **TemplateVersions**.

### TemplateVersion Schema

Each version has three JSON fields:

#### `schema`
General slide configuration:
```json
{
  "id": "template_dark_esports_v1",
  "name": "Dark Esports v1",
  "background": "#0a0a0a",
  "slide_types": ["title_slide", "chart_insight", "comparison"]
}
```

#### `locked_zones`
Header and footer configuration (cannot be edited by users):
```json
{
  "header": {
    "height_px": 80,
    "elements": ["logo", "project_name", "date"],
    "background": "#111111",
    "text_color": "#ffffff"
  },
  "footer": {
    "height_px": 60,
    "elements": ["page_number", "confidentiality_label"],
    "background": "#111111",
    "text_color": "#888888"
  }
}
```

#### `editable_slots`
Defines what content slots are available and their constraints:
```json
{
  "title": {"type": "text", "max_chars": 80, "font_size": 48, "required": true},
  "chart": {"type": "chart", "allowed_types": ["pie", "bar", "line"], "required": false}
}
```

## Adding a New Template

1. Create a `MasterTemplate` record
2. Create a `TemplateVersion` with `is_active: true` and the JSON fields above
3. Or add it to `MasterTemplateSeeder` and re-run `php artisan db:seed`
