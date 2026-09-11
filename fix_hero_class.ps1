$files = Get-ChildItem -Path pages/universities -Filter *.html

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    $content = $content -replace 'class="hero hero--short section"', 'class="hero hero--short"'
    $content = $content -replace 'class="search-wrapper container"', 'class="search-wrapper"'
    
    Set-Content $file.FullName $content
}
