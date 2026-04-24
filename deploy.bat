@echo off
setlocal

:: Cấu hình GitHub URL
set REPO_URL=https://github.com/nguyenhuukhanh-25-05-05/WEB_BAN_-T.git

echo [1/4] Kiem tra Git...
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [LOI] Git chua duoc cai dat! Hay tai git tai: https://git-scm.com/
    pause
    exit /b 1
)

:: Khoi tao git neu chua co
if not exist ".git" (
    echo [2/4] Khoi tao Git repository...
    git init
    git remote add origin %REPO_URL%
) else (
    echo [2/4] Git da duoc khoi tao.
    git remote set-url origin %REPO_URL%
)

:: Dat nhanh mac dinh la main
git branch -M main

echo [3/4] Dang chuan bi tep tin...
git add .

set /p commit_msg="Nhap noi dung commit (Mac dinh: Update code): "
if "%commit_msg%"=="" set commit_msg=Update code

git commit -m "%commit_msg%"

echo [4/4] Dang day code len GitHub...
git push -f -u origin main

if %errorlevel% equ 0 (
    echo.
    echo [THANH CONG] Code da duoc day len GitHub!
) else (
    echo.
    echo [LOI] Co loi xay ra khi day code.
)

pause
