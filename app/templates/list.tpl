{extends file='default.tpl'}

{block name='body'} 
    {$html}

    {if isset($children)}
        <nav>
            <br />
        <h2>I am a list template, these are my children:</h2>    
        <ul>
        {foreach from=$children item=child}
        <li><a href="{$child->url_path}">{$child->title}</a></li>
        {/foreach}
        </ul>
        </nav>
    {/if}

{/block}