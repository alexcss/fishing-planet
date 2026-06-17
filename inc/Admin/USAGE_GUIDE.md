# DLC Importer - Usage Guide

## Quick Start

### Step 1: Access the Importer
1. Log into WordPress Admin
2. Navigate to **DLC** menu in sidebar
3. Click **Import from Google Sheets**

### Step 2: Enter Google Sheets URL
```
https://docs.google.com/spreadsheets/d/1cFixi3S71uRZHJYv8P1TYLFFcsk9MCkx9nj8gN20dBY/edit?gid=0#gid=0
```

### Step 3: Click "Sync DLC from Google Sheets"
The importer will:
- Fetch data from Google Sheets
- Process each row
- Create/update DLC posts
- Download images
- Create taxonomy terms
- Show sync report

## Google Sheets Format

### Column Headers (Row 1)
```
title | content | short_description | dlc_category | dlc_includes | dlc_waterways | store_steam | store_epic_games | store_ps | store_xbox | store_windows | store_mac | store_android | store_ios | store_switch | thumbnail | gallery
```

### Example Row
| Column | Value |
|--------|-------|
| title | "Alaska Premium Pack" |
| content | "Full description of the DLC..." |
| short_description | "Explore the wilderness of Alaska" |
| dlc_category | "Premium<br>Adventure" |
| dlc_includes | "New Location<br>New Fish Species<br>Equipment" |
| dlc_waterways | "Kenai River<br>Chilkat Lake" |
| store_steam | "https://store.steampowered.com/..." |
| store_ps | "https://store.playstation.com/..." |
| thumbnail | "https://example.com/alaska-thumb.jpg" |
| gallery | "https://example.com/img1.jpg<br>https://example.com/img2.jpg" |

## Multi-line Cells

### For Taxonomies (dlc_category, dlc_includes, dlc_waterways)
In Google Sheets, press **Alt+Enter** (Windows) or **Option+Enter** (Mac) to add line breaks:

```
Premium
Adventure
Seasonal
```

### For Gallery Images
Same approach - one URL per line:

```
https://example.com/screenshot1.jpg
https://example.com/screenshot2.jpg
https://example.com/screenshot3.jpg
```

## Sync Report

After sync completes, you'll see:

### Success Message
```
✓ Sync completed successfully!
  • Added: 5 DLC
  • Updated: 3 DLC
  • Errors: 0
```

### Error Message (if any)
```
✗ Import Errors:
  • Row 3: Title is required
  • Row 7: Failed to download image
```

## Common Scenarios

### Scenario 1: First Import
- All DLC will be created as new posts
- All terms will be created
- All images will be downloaded

### Scenario 2: Update Existing DLC
- DLC matched by title will be updated
- Existing terms will be reused
- Images already downloaded won't be re-downloaded

### Scenario 3: Mixed Import
- Some rows create new DLC
- Some rows update existing DLC
- Report shows breakdown

## Best Practices

### 1. Test with Small Dataset First
- Import 2-3 DLC first
- Verify data is correct
- Then import full dataset

### 2. Keep Sheet Organized
- Use consistent naming
- Don't leave empty rows in middle
- Keep column headers in Row 1

### 3. Image URLs
- Use direct image URLs (ending in .jpg, .png, etc.)
- Ensure images are publicly accessible
- Use high-quality images (will be resized by WordPress)

### 4. Store Links
- Include full URLs with https://
- Test links before importing
- Leave empty if store not available

### 5. Taxonomy Terms
- Use consistent term names
- Capitalize properly
- Avoid special characters

## Troubleshooting

### Problem: "Failed to fetch data from Google Sheets"
**Solution:**
1. Check sheet is publicly accessible
2. Verify URL is correct
3. Try opening URL in incognito browser

### Problem: "Invalid Google Sheets URL"
**Solution:**
- URL must contain `/spreadsheets/d/`
- Use full URL from browser address bar
- Don't use shortened URLs

### Problem: Images not importing
**Solution:**
1. Check image URLs are publicly accessible
2. Verify URLs end with image extension
3. Check file size (very large images may timeout)

### Problem: Terms not created
**Solution:**
1. Verify column names match exactly
2. Check line breaks in cells
3. Ensure taxonomy is registered

### Problem: Duplicate DLC created
**Solution:**
- Titles must match exactly (case-sensitive)
- Check for extra spaces in titles
- Use same title format consistently

## Advanced Tips

### Bulk Update Existing DLC
1. Export current DLC to sheet
2. Make changes in sheet
3. Re-import (will update by title)

### Add New Fields
Edit `inc/Admin/DLC_Importer.php`:
```php
case 'your_new_field':
    $data['your_new_field'] = $value;
    break;
```

### Custom Error Handling
Errors are stored in transient for 5 minutes:
```php
get_transient( 'fp_dlc_import_errors' );
```

### Modify Column Mapping
In `map_columns()` method, adjust mapping logic:
```php
$map[ $index ] = trim( strtolower( $header ) );
```

## Performance

### Import Speed
- ~1-2 seconds per DLC (without images)
- ~3-5 seconds per DLC (with images)
- Depends on server speed and image sizes

### Recommended Limits
- **Small import**: 1-10 DLC
- **Medium import**: 10-50 DLC
- **Large import**: 50-100 DLC
- **Very large**: Split into batches

### Server Requirements
- PHP max_execution_time: 300+ seconds
- PHP memory_limit: 256M+ recommended
- WordPress media upload enabled

## Data Validation

### Required Fields
- `title` - Must not be empty

### Optional Fields
- All other fields can be empty
- Empty cells are skipped

### URL Validation
- Store links validated as URLs
- Image URLs validated before download
- Invalid URLs are skipped with warning

### Text Length
- `short_description`: Max 400 characters (ACF field limit)
- `title`: No limit
- `content`: No limit

## Security

### Data Sanitization
- All text fields sanitized with `sanitize_text_field()`
- URLs validated with `filter_var()`
- Nonce verification on form submit
- Capability check: `manage_options`

### Image Downloads
- Uses WordPress `media_sideload_image()`
- Validates file types
- Checks file size limits
- Prevents path traversal

## Maintenance

### Clear Import Errors
Errors auto-delete after 5 minutes, or manually:
```php
delete_transient( 'fp_dlc_import_errors' );
```

### Reset Sheet URL
Delete saved URL:
```php
delete_option( 'fp_dlc_importer_sheet_url' );
```

### View Import Logs
Check WordPress debug.log for detailed errors (if WP_DEBUG enabled)

## Support & Documentation

- **Main README**: `inc/Admin/README.md`
- **Setup Guide**: `DLC_IMPORTER_SETUP.md`
- **Code**: `inc/Admin/DLC_Importer.php`
