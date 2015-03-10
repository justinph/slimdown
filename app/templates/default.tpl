<!DOCTYPE html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>{$title}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    </head>
    <body>

        <nav>
            {if isset($parent)}
                 Parent: <a href="{$parent->url_path}">{$parent->title}</a> <br />
            {/if}

            {if isset($siblings)}
                siblings:
                 <ul>
                {foreach from=$siblings item=sib}
                    <li><a href="{$sib->url_path}">{$sib->title}</a></li>
                {/foreach}
                </ul>
            {/if}

            {if isset($children)}
                children:
                 <ul>
                {foreach from=$children item=child}
                    <li><a href="{$child->url_path}">{$child->title}</a></li>
                {/foreach}
                </ul>
            {/if}

        </nav>

        {block name='body'}
        {$html}
        {/block}
    </body>
</html>