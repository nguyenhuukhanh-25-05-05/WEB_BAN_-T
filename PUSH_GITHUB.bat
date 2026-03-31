@echo off
echo NHK Mobile - Git Push Tool
echo ==========================
echo 0. Correcting remote URL...
git remote set-url origin https://github.com/nguyenhuukhanh-25-05-05/WEB_BAN_-T.git
echo 1. Staging all changes...
git add .
echo 2. Committing with premium message...
git commit -m "Security Hardening & Premium UX Upgrade: Implement XSS/CSRF protection and AJAX Cart with Toast Notifications"
echo 3. Pushing to origin...
git push origin HEAD
echo ==========================
echo Hoan thanh! Nhan nut bat ky de dong.
pause
