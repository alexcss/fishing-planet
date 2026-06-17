# DLC Importer from Google Sheets

## Overview

The DLC Importer allows you to sync DLC content from a Google Sheets document directly into WordPress. It automatically creates or updates DLC posts, taxonomies, and media.

## Access

Navigate to **DLC → Import from Google Sheets** in the WordPress admin dashboard.

## Google Sheets Setup

### 1. Make Your Sheet Public

Your Google Sheets document must be publicly accessible:
1. Open your Google Sheet
2. Click **Share** → **Change to anyone with the link**
3. Set permissions to **Viewer**

### 2. Required Columns

Your sheet must have these exact column headers (case-insensitive):

#### Basic Information
- `title` - DLC title (required)
- `short_description` - Short description (max 400 characters)
- `content` - Full content/description

#### Taxonomies (one term per line in cell)
- `dlc_category` - DLC categories
- `dlc_includes` - What the DLC includes
- `dlc_waterways` - Waterways

#### Store Links
- `store_steam` - Steam store URL
- `store_epic_games` - Epic Games store URL
- `store_ps` - PlayStation store URL
- `store_xbox` - Xbox store URL
- `store_windows` - Windows store URL
- `store_mac` - Mac store URL
- `store_android` - Android store URL
- `store_ios` - iOS store URL
- `store_switch` - Nintendo Switch store URL

#### Media
- `thumbnail` - Featured image URL
- `gallery` - Gallery image URLs (one per line in cell)

### 3. Multi-line Cell Format

For taxonomy and gallery columns, use line breaks within a single cell:

**Example for `dlc_category`:**
```
Premium
Adventure
Seasonal
```

**Example for `gallery`:**
```
https://example.com/image1.jpg
https://example.com/image2.jpg
https://example.com/image3.jpg
```

## How It Works

### Import Process

1. **Enter Google Sheets URL** - Paste the full URL of your Google Sheet
2. **Click Sync** - The importer will:
   - Fetch data from Google Sheets
   - Match existing DLC by title
   - Create new DLC or update existing ones
   - Create taxonomy terms if they don't exist
   - Download and attach images

### Sync Report

After sync completes, you'll see:
- **Added**: Number of new DLC created
- **Updated**: Number of existing DLC updated
- **Errors**: Number of rows that failed (with details)

### Duplicate Prevention

- **Posts**: Matched by title - existing posts are updated, not duplicated
- **Terms**: Checked by name - existing terms are reused
- **Images**: Tracked by source URL - same images won't be re-downloaded

## Example Google Sheet

[View Example Sheet](https://docs.google.com/spreadsheets/d/1cFixi3S71uRZHJYv8P1TYLFFcsk9MCkx9nj8gN20dBY/edit?gid=0#gid=0)

## Troubleshooting

### "Failed to fetch data from Google Sheets"
- Ensure the sheet is publicly accessible
- Check that the URL is correct
- Verify the sheet has data

### "Invalid Google Sheets URL"
- URL must be in format: `https://docs.google.com/spreadsheets/d/...`
- Include the full URL with sheet ID

### Images Not Importing
- Ensure image URLs are publicly accessible
- Check that URLs are valid and return images
- Large images may take time to download

### Terms Not Created
- Check that taxonomy columns have correct names
- Ensure terms are separated by line breaks in cells
- Verify taxonomy is registered in WordPress

## Technical Details

### Files
- `inc/Admin/DLC_Importer.php` - Main importer class
- `inc/Admin/views/dlc-importer.php` - Admin UI template
- `src/css/admin/dlc-importer.css` - Admin styles
- `src/js/admin/dlc-importer.js` - Admin scripts

### Hooks
- `admin_menu` - Adds submenu page
- `admin_post_fp_dlc_sync` - Handles sync action
- `admin_enqueue_scripts` - Loads assets

### Image Handling
- Images are downloaded via `media_sideload_image()`
- Source URL is stored in `_source_url` meta to prevent duplicates
- Attachments are properly linked to DLC posts

### Error Handling
- Errors are collected during import
- Stored in transient for display
- Detailed error messages per row
