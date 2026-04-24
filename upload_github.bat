@echo off
echo Dang tien hanh push code len Github...

if not exist .git (
    echo Khoi tao git repository...
    git init
    git branch -M main
    git remote add origin https://github.com/nguyenhuukhanh-25-05-05/nhkmobile_web.git
) else (
    echo Cap nhat remote origin...
    git remote remove origin 2>nul
    git remote add origin https://github.com/nguyenhuukhanh-25-05-05/nhkmobile_web.git
)

echo Them file vao git...
git add .
set /p commit_msg="Nhap noi dung commit (Nhan Enter de dung mac dinh la 'Update code'): "
if "%commit_msg%"=="" set commit_msg=Update code
git commit -m "%commit_msg%"

echo Dang push len branch main (Force Push) ...
git branch -M main
git push -u origin main --force

echo.
echo Hoan thanh!
pause
