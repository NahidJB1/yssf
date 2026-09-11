$files = Get-ChildItem -Path . -Filter *.html -Recurse | Where-Object { $_.FullName -notmatch '\\\.git\\' -and $_.FullName -notmatch '\\admin_portal\\' }
$files += Get-ChildItem -Path . -Filter *.php -Recurse | Where-Object { $_.FullName -notmatch '\\admin_portal\\' -and $_.FullName -notmatch '\\api\\' }

$fabHtml = "`n  <a href=`"https://wa.me/601119359497`" class=`"fab fab--whatsapp`" target=`"_blank`" rel=`"noopener noreferrer`" aria-label=`"Chat on WhatsApp`">`n    <ion-icon name=`"logo-whatsapp`"></ion-icon>`n  </a>`n</body>"

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # Replace existing old whatsapp numbers
    $content = $content -replace '601139660706', '601119359497'
    $content = $content -replace '\+601139660706', '+601119359497'
    $content = $content -replace '\+60 11-3966 0706', '+60 11-1935 9497'
    
    # Remove existing FAB if it exists
    $content = $content -replace '(?s)<a href="https://wa\.me/\d+" class="fab fab--whatsapp".*?</a>\s*', ''
    
    # Inject FAB before </body>
    if ($content -match '</body>') {
        $content = $content -replace '</body>', $fabHtml
        Set-Content $file.FullName $content
        Write-Host "Processed $($file.Name)"
    }
}
