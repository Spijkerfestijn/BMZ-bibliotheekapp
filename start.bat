@echo off
setlocal
title Bibliotheek Bevrijdingsmuseum Zeeland
color 0A

set XAMPP=C:\xampp
set PHP=%XAMPP%\php\php.exe
set PORT=8090

if not exist "%PHP%" (
    echo XAMPP PHP niet gevonden op %PHP%.
    echo Pas het pad in start.bat aan als XAMPP ergens anders staat.
    pause
    exit /b 1
)

echo.
echo  MySQL controleren...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if errorlevel 1 (
    echo  MySQL wordt gestart...
    start "" /B "%XAMPP%\mysql\bin\mysqld.exe" --defaults-file="%XAMPP%\mysql\bin\my.ini" --standalone
    timeout /t 4 /nobreak >nul
) else (
    echo  MySQL draait al.
)

if not exist "%~dp0config.php" (
    echo  Geen config.php gevonden, config.example.php wordt gekopieerd...
    copy "%~dp0config.example.php" "%~dp0config.php" >nul
    echo  Vul de database-gegevens in config.php in en start opnieuw.
    notepad "%~dp0config.php"
)

echo.
echo  Bibliotheek wordt gestart...
echo  De browser opent automatisch op http://localhost:%PORT%
echo.
echo  Sluit dit venster om de server te stoppen.
echo.

start /b "" "%PHP%" -S localhost:%PORT% -t "%~dp0" > "%~dp0server.log" 2>&1
timeout /t 2 /nobreak >nul

for /f "tokens=5" %%p in ('netstat -ano ^| findstr ":%PORT% "') do set SERVER_PID=%%p

start "" "http://localhost:%PORT%"

echo  Server actief op http://localhost:%PORT%
echo  Druk op een toets om de server te stoppen.
pause >nul

if defined SERVER_PID (
    taskkill /f /pid %SERVER_PID% >nul 2>&1
) else (
    taskkill /f /im php.exe >nul 2>&1
)
