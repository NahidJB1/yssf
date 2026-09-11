$files = Get-ChildItem -Path pages/universities -Filter *.html

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    
    # The content usually starts with <div class="container"> after the hero
    # Let's replace <div class="container"> with <main class="container section"> 
    # but only if it's right after the hero.
    
    $content = $content -replace '</section>\s*<div class="container">', '</section><main class="container section">'
    $content = $content -replace '</header>\s*<div class="section container">', '</header><main class="container section">'
    $content = $content -replace '</header>\s*<div class="container">', '</header><main class="container section">'
    
    # Ensure closing main tag before footer
    $content = $content -replace '</div>\s*<footer', '</main><footer'
    
    Set-Content $file.FullName $content
}
