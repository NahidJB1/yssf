$files = Get-ChildItem -Path pages/universities -Filter *.html

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # 1. Find the first onclick="downloadPDF(...)"
    $args = "'all'"
    if ($content -match 'onclick="downloadPDF\(([^)]+)\)"') {
        $args = $Matches[1]
    } elseif ($content -match 'downloadPDF\(([^)]+)\)') {
        $args = $Matches[1]
    }
    
    # 2. Remove all existing download buttons
    $content = $content -replace '(?s)<button[^>]*class="[^"]*download-bar[^"]*"[^>]*>.*?</button>\s*', ''
    $content = $content -replace '(?s)<div[^>]*class="[^"]*download-bar[^"]*"[^>]*>.*?</div>\s*', ''
    
    # 3. Add universal button before </main>
    # Check if universal button already exists (idempotency)
    if ($content -notmatch 'class="universal-download"') {
        $universalBtn = "`n  <div class=`"universal-download`">`n    <button class=`"btn btn--primary btn--lg`" onclick=`"downloadPDF($args)`">`n      <i class=`"fas fa-file-pdf`" style=`"margin-right: 0.5rem;`"></i> Download Official PDF`n    </button>`n  </div>`n</main>"
        
        $content = $content -replace '</main>', $universalBtn
    }
    
    Set-Content $file.FullName $content
}
