@echo off
echo NHK MOBILE V2.0 - THE RENAISSANCE
echo ==================================
echo 1. Cau hinh danh tinh...
git config user.name "Nguyen Huu Khanh"
git config user.email "nguyenhuukhanh.coder.vn@gmail.com"

echo 2. Dang quet cac file moi thay doi...
git add .

echo 3. Dang dong goi code (Commit V2.0)...
git commit -m "NHK Mobile V2.1 - The Light Renaissance & Stability Update: Fixed Fatal Errors and optimized design to be cleaner and more professional"

echo 4. Dang day code len GitHub (Main branch)...
git remote set-url origin https://github.com/nguyenhuukhanh-25-05-05/WEB_BAN_-T.git
git push origin HEAD:main --force

echo ==================================
echo CHUC MUNG! NHK MOBILE V2.0 DA DUOC DAY LEN HE THONG.
echo He thong Render se tu dong cap nhat trong 1-2 phut.
echo Hay kien nhan cho doi dien mao moi nhe!
pause
