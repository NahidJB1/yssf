$files = Get-ChildItem -Path pages/universities -Filter *.html

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # Remove inline styles that break the hero gradient
    $content = $content -replace '<header class="hero hero--short" style="[^"]*">', '<header class="hero hero--short">'
    
    # Ensure SearchWrapper has relative position
    $content = $content -replace '<div class="search-wrapper"[^>]*>', '<div class="search-wrapper" style="position: relative; margin-bottom: 3rem;">'
    
    Set-Content $file.FullName $content
    Write-Host "Cleaned inline styles in $($file.Name)"
}
