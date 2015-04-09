<!DOCTYPE html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>{$title}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0" />
        <link rel="stylesheet" type="text/css" href="/normalize.css">
        <link rel="stylesheet" type="text/css" href="/style.css">
        {block name='head'}{/block}
    </head>
    <body>

            <nav id='sidebar'>
            {if isset($parent)}
                <nav class='parent'>
                  <a href="{$parent->url_path}">&lsaquo; {$parent->title}</a>
                </nav>
            {/if}

            {if isset($siblings)}
                 <nav class='siblings'>
                 <h4>Sibling Pages</h4>
                 <ul>
                {foreach from=$siblings item=sib}
                    <li>{if isset($sib->self)}
                        {$sib->title}
                        {else}
                        <a href="{$sib->url_path}">{$sib->title}</a>
                    {/if}
                    </li>
                {/foreach}
                </ul>
                </nav>
            {/if}
            {if isset($children)}
                <nav class='children'>
                 <h4>Child Pages</h4>
                 <ul>
                {foreach from=$children item=child}
                    <li><a href="{$child->url_path}">{$child->title}</a></li>
                {/foreach}
                </ul>
                </nav>
            {/if}
           
            </nav>

         <main>
        
        {if isset($title)}      
        <h1>{$title}</h1>
        {/if}
        
        {block name='body'}
        {$html}
        {/block}
        </main>
    </body>
</html>