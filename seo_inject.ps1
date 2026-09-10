$files = Get-ChildItem -Path . -Filter *.html -Recurse | Where-Object { $_.FullName -notmatch '\\\.git\\' -and $_.FullName -notmatch '\\admin_portal\\' }
$files += Get-ChildItem -Path . -Filter *.php -Recurse | Where-Object { $_.FullName -notmatch '\\admin_portal\\' -and $_.FullName -notmatch '\\api\\' }

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    $relPath = Resolve-Path -Relative $file.FullName
    $absoluteUrl = "https://nahidjb1.github.io/yssf/" + ($relPath -replace '^\.\\', '' -replace '\\', '/')

    $title = "YS Study Focus"
    if ($content -match '<title>(.*?)</title>') {
        $title = $matches[1]
    }

    $desc = "YS Study Focus - Your trusted educational consultant for studying in Malaysia. Get expert guidance, fee structures, and admission assistance for top Malaysian universities."
    if ($title -match 'Fee') {
        $desc = "View the latest international fee structure and tuition costs for $title. Get expert admission guidance from YS Study Focus."
    } elseif ($title -match 'Details' -or $title -match 'University' -or $title -match 'College') {
        $desc = "Discover programs, campus life, and admission requirements for $title in Malaysia. Apply easily with YS Study Focus."
    }

    $seoTags = "<meta name=`"description`" content=`"$desc`" />`n"
    $seoTags += "<meta name=`"keywords`" content=`"Study in Malaysia, Malaysian Universities, YS Study Focus, study abroad, international students, Malaysia scholarships, student visa Malaysia, $title`" />`n"
    $seoTags += "<meta property=`"og:title`" content=`"$title`" />`n"
    $seoTags += "<meta property=`"og:description`" content=`"$desc`" />`n"
    $seoTags += "<meta property=`"og:type`" content=`"website`" />`n"
    $seoTags += "<meta property=`"og:url`" content=`"$absoluteUrl`" />`n"
    $seoTags += "<meta property=`"og:image`" content=`"https://nahidjb1.github.io/yssf/assets/images/ys_logo.png`" />`n"
    $seoTags += "<meta name=`"twitter:card`" content=`"summary_large_image`" />`n"
    $seoTags += "<meta name=`"twitter:title`" content=`"$title`" />`n"
    $seoTags += "<meta name=`"twitter:description`" content=`"$desc`" />`n"
    $seoTags += "<meta name=`"twitter:image`" content=`"https://nahidjb1.github.io/yssf/assets/images/ys_logo.png`" />`n"
    $seoTags += "<link rel=`"canonical`" href=`"$absoluteUrl`" />"

    $content = $content -replace '(?s)<meta name="description".*?/>\s*', ''
    $content = $content -replace '(?s)<meta name="keywords".*?/>\s*', ''
    $content = $content -replace '(?s)<meta property="og:.*?/>\s*', ''
    $content = $content -replace '(?s)<meta name="twitter:.*?/>\s*', ''
    $content = $content -replace '(?s)<link rel="canonical".*?/>\s*', ''

    $content = $content -replace '(<title>.*?</title>)', "`$1`n  $seoTags"
    
    Set-Content $file.FullName $content
    Write-Host "Injected SEO into $($file.Name)"
}
