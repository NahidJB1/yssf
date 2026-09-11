$detailsFiles = Get-ChildItem pages/universities/*-details.html

foreach ($detailsFile in $detailsFiles) {
    $content = Get-Content $detailsFile.FullName -Raw
    
    $imageUrl = ""
    if ($content -match "background-image:\s*url\('([^']+)'\)") {
        $imageUrl = $Matches[1]
    } elseif ($content -match 'background-image:\s*url\("([^"]+)"\)') {
        $imageUrl = $Matches[1]
    }
    
    if ($imageUrl) {
        $prefix = $detailsFile.Name.Replace('-details.html', '')
        
        # Find the matching fees page
        $feesPage = Get-ChildItem pages/universities/$prefix*.html | Where-Object { $_.Name -notmatch '-details\.html' -and $_.Name -notmatch '\.jpg\.html' } | Select-Object -First 1
        
        # Some prefixes might not match exactly, e.g. "cyberjaya-details.html" -> "university-of-cyberjaya.html"
        if (-not $feesPage -and $prefix -eq 'cyberjaya') {
            $feesPage = Get-Item pages/universities/university-of-cyberjaya.html
        }
        if (-not $feesPage -and $prefix -eq 'lsbf') {
            $feesPage = Get-Item pages/universities/lsbf.html
        }
        
        if ($feesPage) {
            $feesContent = Get-Content $feesPage.FullName -Raw
            
            $newHeader = "<header class=`"hero hero--short`" style=`"background-image: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('$imageUrl'); background-size: cover; background-position: center;`">"
            
            # Replace existing header/section hero
            $feesContent = $feesContent -replace '<header class="hero hero--short"[^>]*>', $newHeader
            $feesContent = $feesContent -replace '<section class="hero hero--short"[^>]*>', $newHeader.Replace('<header', '<section')
            
            Set-Content $feesPage.FullName $feesContent
            Write-Host "Injected $imageUrl into $($feesPage.Name)"
        }
    }
}
