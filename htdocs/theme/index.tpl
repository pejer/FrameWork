doctype
html(class="nojs")
    head
        title Henriks little page test
        link(rel="stylesheet",type="text/css",href="/css/dalooks.css")
        style(type="text/css")
            |html.js body{background: #fff;}
            |html.nojs body{background:#f90;}
        script(type="text/javascript") window.document.getElementsByClassName('nojs')[0].className='js';
        script(type="text/javascript",src="/js/app.js")
    body
        #content.className(data-value="something",style="background:#f09") {{variableName}} {{include:menu.tpl}}
            div.something
                ul.className#id
                    li This is
                    li necessary!
                strong Fabulous
                p.text oh yea, motherfucker!
        p and this is the end, sucker!