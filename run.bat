@echo off
setlocal

title PHP FinalExam - Run
color 0A

echo ============================================
echo   KHOI DONG DU AN PHP_FinalExam
echo ============================================
echo.

set "PROJECT_FOLDER=PHP_FinalExam"
set "PROJECT_DIR=%~dp0"
set "ENTRY_PAGE=pages/auth/dangnhap.php"
set "XAMPP_DIR=C:\xampp"
set "XAMPP_PROJECT=%XAMPP_DIR%\htdocs\%PROJECT_FOLDER%"

:: 1) Neu Apache dang chay va project nam trong htdocs thi mo truc tiep localhost
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I "httpd.exe" >NUL
if "%ERRORLEVEL%"=="0" (
    if exist "%XAMPP_PROJECT%" (
        set "URL=http://localhost/%PROJECT_FOLDER%/%ENTRY_PAGE%"
        echo [OK] Apache dang chay. Mo du an qua XAMPP...
        goto OPEN_BROWSER
    )
)

:: 2) Neu khong thi chay built-in server
set "PHP_EXE="
where php >NUL 2>&1
if "%ERRORLEVEL%"=="0" set "PHP_EXE=php"
if not defined PHP_EXE if exist "%XAMPP_DIR%\php\php.exe" set "PHP_EXE=%XAMPP_DIR%\php\php.exe"

if not defined PHP_EXE (
    echo [!!] Khong tim thay PHP tren PATH hoac C:\xampp\php\php.exe.
    echo      Hay cai PHP hoac mo project trong XAMPP htdocs.
    echo.
    pause
    exit /b 1
)

set "PORT=8080"
call :CHECK_PORT %PORT%
if "%PORT_BUSY%"=="1" set "PORT=8081"

echo [..] Dang chay PHP built-in server tai port %PORT%...
set "DOCROOT=%PROJECT_DIR%.."
set "ROUTER=%PROJECT_DIR%router.php"
start "PHP_FinalExam_Server" cmd /k ""%PHP_EXE%" -S localhost:%PORT% -t "%DOCROOT%" "%ROUTER%""
timeout /t 2 /nobreak >NUL

set "URL=http://localhost:%PORT%/%PROJECT_FOLDER%/%ENTRY_PAGE%"
goto OPEN_BROWSER

:OPEN_BROWSER
echo [..] Dang mo trinh duyet: %URL%
start "" "%URL%"
echo.
echo [OK] Hoan tat.
echo     Neu dang dung built-in server, dong cua so 'PHP_FinalExam_Server' de dung.
echo.
pause
exit /b 0

:CHECK_PORT
set "PORT_BUSY=0"
netstat -ano | findstr /R /C:":%1 .*LISTENING" >NUL
if "%ERRORLEVEL%"=="0" set "PORT_BUSY=1"
exit /b 0
