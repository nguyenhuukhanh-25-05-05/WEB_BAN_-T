@echo off
chcp 65001 >nul
title Fix Secret - NHK Mobile
echo.
echo ================================================
echo  Xoa API key khoi commit va push len GitHub
echo ================================================
echo.

cd /d D:\nhkmobile_web

echo [1/4] Untrack file _secret.php (bo theo doi git)...
git rm --cached api/_secret.php 2>nul
echo.

echo [2/4] Stage tat ca thay doi...
git add -A
echo.

echo [3/4] Amend commit cuoi (ghi de, xoa key)...
git commit --amend --no-edit
echo.

echo [4/4] Force push len GitHub...
git push origin main --force
echo.

echo ================================================
if %ERRORLEVEL% EQU 0 (
    echo  THANH CONG! Da push len GitHub.
    echo.
    echo  Nho vao Render.com set bien moi truong:
    echo    XAI_API_KEY = xai-wVGZT1jla5...
) else (
    echo  [LOI] Push that bai. Xem thong bao o tren.
)
echo ================================================
echo.
pause
