<a class="btn btn-{$color}" {if isset($href) && $href} href="{$href}" {/if} {if isset($js) && $js} onclick="{$js}" {/if}>
    {if isset($icon) && $icon}
        <i class="material-icons">{$icon}</i> &nbsp;
    {/if}
    <span>{$text}</span>
</a>