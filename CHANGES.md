# NHK Mobile - Changes Summary

## Changes Made

### 1. Desktop App (Electron)
**Location:** `desktop-app/`

**Files Created:**
- `desktop-app/main.js` - Electron main process, handles:
  - Window creation (1400x900, min 1024x768)
  - Auto PHP server startup on port 8080
  - Menu bar with navigation to all admin modules
  - IPC handlers for app info, external links, dialogs
  - Graceful shutdown of PHP server

- `desktop-app/preload.js` - Secure context bridge exposing:
  - `electronAPI.getAppInfo()`
  - `electronAPI.openExternal()`
  - `electronAPI.showDialog()`
  - Platform info

- `desktop-app/package.json` - NPM config with:
  - Electron 28.0.0
  - electron-builder 24.9.1
  - Build targets for Win/Mac/Linux
  - Scripts: start, dev, build:win, build:mac, build:linux

- `desktop-app/assets/README.txt` - Asset placeholder

- Windows batch scripts:
  - `setup.bat` - Install dependencies
  - `run.bat` - Run the app
  - `build.bat` - Build for Windows

- `desktop-app/INSTALL.md` - Detailed installation guide
- `desktop-app/README.md` - Quick start guide
- `desktop-app/.gitignore` - Git ignore rules

**Features:**
- Loads admin panel at `http://127.0.0.1:8080/admin/dashboard.php`
- Menu bar with quick access to:
  - Dashboard
  - Products
  - Orders
  - Users
  - Warranties
  - News
  - Revenue
- Keyboard shortcuts: Ctrl+R (refresh), Ctrl+Q (quit), F12 (DevTools)
- Auto-detects and starts PHP server
- Opens external links in default browser
- Dev mode with `--dev` flag

### 2. System Check Page
**Location:** `check.php`

**Features:**
Checks and displays status for:
1. Database connection (PostgreSQL)
2. Users table (with count)
3. Products table (with count)
4. Orders table (with count)
5. Warranties table (with count)
6. News table (with count)
7. PHP version (min 8.0)
8. Required PHP extensions (pdo, pdo_pgsql, pgsql, session, json, mbstring)
9. File permissions (logs, uploads, assets)
10. Disk space
11. Session functionality
12. Mail function availability

**UI:**
- Summary cards showing success/warning/error counts
- Detailed table with status badges
- Quick action buttons to:
  - Admin Dashboard
  - Reset Database
  - Export Stats
  - Manage Products
- System info section (PHP version, OS, execution time, memory limit)

### 3. Navigation Updates

**Modified Files:**
- `includes/header.php`:
  - Added "Kiểm tra hệ thống" link to mobile menu

- `admin/includes/admin_header.php`:
  - Added "Kiểm tra hệ thống" link in sidebar
  - Added link to desktop app info

- `README.md`:
  - Complete project documentation
  - Desktop app setup instructions
  - System check page info
  - Project structure

### 4. Dependencies Installed
- electron@28.0.0
- electron-builder@24.9.1
- All dependencies in `desktop-app/node_modules/`

## How to Use

### Run Desktop App
```bash
cd desktop-app
npm start
```

Or use batch file:
```bash
desktop-app\run.bat
```

### Build Desktop App
```bash
cd desktop-app
npm run build:win
```

Or use batch file:
```bash
desktop-app\build.bat
```

### Access System Check
- Web: `http://127.0.0.1:8080/check.php`
- From admin sidebar: "Kiểm tra hệ thống"
- From mobile menu: "Kiểm tra hệ thống"

## Architecture

```
┌─────────────────────────────────────────────┐
│           Desktop App (Electron)            │
│  ┌─────────────────┐    ┌────────────────┐  │
│  │   Main Process  │    │  Renderer      │  │
│  │   (main.js)     │◄──►│  (Admin Panel) │  │
│  │  - PHP Server   │    │  - dashboard   │  │
│  │  - Menu         │    │  - products    │  │
│  │  - Window Mgmt  │    │  - orders      │  │
│  └─────────────────┘    └────────────────┘  │
│           ▲                    │             │
│           │ IPC                │ HTTP        │
│           ▼                    ▼             │
│  ┌─────────────────────────────────────┐    │
│  │         preload.js                  │    │
│  │  - electronAPI bridge               │    │
│  └─────────────────────────────────────┘    │
└─────────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│           PHP Server (Port 8080)            │
│  ┌──────────────┐    ┌──────────────────┐   │
│  │  Admin Panel │    │  System Check    │   │
│  │  /admin/     │    │  /check.php      │   │
│  └──────────────┘    └──────────────────┘   │
│  ┌──────────────┐    ┌──────────────────┐   │
│  │  Website     │    │  APIs            │   │
│  │  /index.php  │    │  /api/           │   │
│  └──────────────┘    └──────────────────┘   │
└─────────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────┐
│         PostgreSQL Database                 │
│  - users, products, orders, warranties, etc │
└─────────────────────────────────────────────┘
```

## Testing Recommendations

1. **Test Desktop App:**
   ```bash
   cd desktop-app
   npm run dev  # Opens with DevTools
   ```

2. **Test System Check:**
   - Navigate to `/check.php`
   - Verify all checks pass
   - Check database connectivity
   - Verify PHP extensions

3. **Test Admin Navigation:**
   - Open desktop app
   - Use menu items to navigate
   - Verify all admin pages load correctly

## Known Issues

1. Electron download may fail due to network issues - use manual installation if needed
2. PHP auto-detection may not find PHP in non-standard paths - set PHP_PATH env var
3. Port 8080 may conflict with other services - change in main.js if needed
