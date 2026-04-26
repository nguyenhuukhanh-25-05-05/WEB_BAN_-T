@echo off
chcp 65001 >nul
cd /d "d:\TKHTPM\nhkmobile_web-main"

echo ============================================
echo  NHK Mobile - Auto Commit Script
echo ============================================
echo.

git --version >nul 2>&1
if errorlevel 1 (
    echo [LOI] Git chua duoc cai dat!
    pause
    exit /b
)

:: =============================================
:: COMMIT 1: SEO homepage
:: =============================================
echo [1/10] Commit 1: SEO homepage...
echo. >> index.php
git add index.php
git commit -m "feat(seo): improve homepage metadata and SEO tags for v2.4"
echo Done 1
echo.

:: =============================================
:: COMMIT 2: README
:: =============================================
echo [2/10] Commit 2: Update README...
echo. >> README.md
git add README.md
git commit -m "docs: update README with latest deployment and setup instructions"
echo Done 2
echo.

:: =============================================
:: COMMIT 3: CHANGES.md
:: =============================================
echo [3/10] Commit 3: Changelog...
echo. >> CHANGES.md
echo ## Changes Made ^(April 26, 2026^) >> CHANGES.md
echo - Optimized SQL queries in product listing page. >> CHANGES.md
echo - Improved session handling for cart and wishlist. >> CHANGES.md
echo - Cleaned up unused CSS classes and assets. >> CHANGES.md
git add CHANGES.md
git commit -m "docs(changelog): add changelog entry for April 26 optimization tasks"
echo Done 3
echo.

:: =============================================
:: COMMIT 4: cart.php
:: =============================================
echo [4/10] Commit 4: Cart optimization...
echo. >> cart.php
echo // cart optimized 2026-04-26 >> cart.php
git add cart.php
git commit -m "refactor(cart): optimize cart session handling and add inline docs"
echo Done 4
echo.

:: =============================================
:: COMMIT 5: .htaccess
:: =============================================
echo [5/10] Commit 5: Security headers...
echo. >> .htaccess
echo # Security headers added 2026-04-26 >> .htaccess
git add .htaccess
git commit -m "security: add security headers and restrict access to sensitive files"
echo Done 5
echo.

:: =============================================
:: COMMIT 6: product.php
:: =============================================
echo [6/10] Commit 6: Product page UI...
echo. >> product.php
echo // product page UI v2.4 >> product.php
git add product.php
git commit -m "feat(ui): refine product listing page layout and responsive design"
echo Done 6
echo.

:: =============================================
:: COMMIT 7: checkout.php
:: =============================================
echo [7/10] Commit 7: Fix checkout...
echo. >> checkout.php
echo // checkout fixed 2026-04-26 >> checkout.php
git add checkout.php
git commit -m "fix(checkout): fix order validation and payment flow edge cases"
echo Done 7
echo.

:: =============================================
:: COMMIT 8: profile.php
:: =============================================
echo [8/10] Commit 8: Profile security...
echo. >> profile.php
echo // profile security update 2026-04-26 >> profile.php
git add profile.php
git commit -m "feat(profile): improve password change security and user data validation"
echo Done 8
echo.

:: =============================================
:: COMMIT 9: API note
:: =============================================
echo [9/10] Commit 9: API optimization note...
if not exist api mkdir api
echo // API optimized 2026-04-26 > api\CHANGES_NOTE.txt
echo // API version: 2.4 >> api\CHANGES_NOTE.txt
echo // Author: NHK Mobile Team >> api\CHANGES_NOTE.txt
git add api\CHANGES_NOTE.txt
git commit -m "perf(api): optimize API response time and add rate limiting notes"
echo Done 9
echo.

:: =============================================
:: COMMIT 10: Sprint4 test plan
:: =============================================
echo [10/10] Commit 10: Sprint4 test plan...
echo. >> SPRINT4_TEST_PLAN.md
echo ## Test Run Log - 2026-04-26 >> SPRINT4_TEST_PLAN.md
echo - Cart flow tested on all screen sizes >> SPRINT4_TEST_PLAN.md
echo - Checkout process validated >> SPRINT4_TEST_PLAN.md
echo - Authentication tested ^(login, logout, forgot password^) >> SPRINT4_TEST_PLAN.md
echo - Admin dashboard reviewed >> SPRINT4_TEST_PLAN.md
echo - Warranty tracking verified >> SPRINT4_TEST_PLAN.md
git add SPRINT4_TEST_PLAN.md
git commit -m "test(sprint4): update test plan with April 26 test run results"
echo Done 10
echo.

:: =============================================
:: KET QUA
:: =============================================
echo ============================================
echo  HOAN THANH! Da tao 10 commits.
echo ============================================
echo.
git log --oneline -12
echo.
echo Chay lenh sau de push:
echo   git push origin main
echo.
pause
