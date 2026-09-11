$detailsFiles = Get-ChildItem pages/universities/*-details.html

foreach ($detailsFile in $detailsFiles) {
    $content = Get-Content $detailsFile.FullName -Raw
    
    $imageUrl = ""
    if ($content -match "url\(['`"]?(\.\./\.\./assets/images/[^'`"\)]+)['`"]?\)") {
        $imageUrl = $Matches[1]
    } elseif ($content -match '<img src="(\.\./\.\./assets/images/[^"]+)"[^>]*alt="[^"]*Background"') {
        $imageUrl = $Matches[1]
    }

    if ($imageUrl) {
        $prefix = $detailsFile.Name.Replace('-details.html', '')
        
        $feesPage = Get-ChildItem pages/universities/$prefix*.html | Where-Object { $_.Name -notmatch '-details\.html' -and $_.Name -notmatch '\.jpg\.html' } | Select-Object -First 1
        
        if (-not $feesPage -and $prefix -eq 'cyberjaya') {
            $feesPage = Get-Item pages/universities/university-of-cyberjaya.html
        }
        if (-not $feesPage -and $prefix -eq 'lsbf') {
            $feesPage = Get-Item pages/universities/lsbf.html
        }
        if (-not $feesPage -and $prefix -eq 'mantissa') {
            $feesPage = Get-Item pages/universities/mantissa-college.html
        }
        
        if ($feesPage) {
            $feesContent = Get-Content $feesPage.FullName -Raw
            
            if ($feesContent -notmatch "background-image: linear-gradient") {
                $newHeader = "<header class=`"hero hero--short`" style=`"background-image: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('$imageUrl'); background-size: cover; background-position: center;`">"
                
                $feesContent = $feesContent -replace '<header class="hero hero--short"[^>]*>', $newHeader
                $feesContent = $feesContent -replace '<section class="hero hero--short"[^>]*>', $newHeader.Replace('<header', '<section')
                
                Set-Content $feesPage.FullName $feesContent
                Write-Host "Injected $imageUrl into $($feesPage.Name)"
            }
        }
    }
}
