@echo off
echo NHK Mobile - Sieu Cap Vận Hành
echo ==================================
echo 1. Cau hinh danh tinh...
git config user.name "Nguyen Huu Khanh"
git config user.email "nguyenhuukhanh.coder.vn@gmail.com"

echo 2. Dang quet cac file moi thay doi...
git add .

echo 3. Dang dong goi code (Commit)...
git commit -m "Fix syntax errors and enable production debugging"

echo 4. Dang day code len GitHub (Main branch)...
git remote set-url origin https://github.com/nguyenhuukhanh-25-05-05/WEB_BAN_-T.git
git push origin HEAD:main --force

echo ==================================
echo XONG ROI! Bay gio file test.php va cac ban va loi da len GitHub.
echo Ban hay F5 lai trang web va thu ca link:
echo https://nhkmobile.onrender.com/test.php
pause
