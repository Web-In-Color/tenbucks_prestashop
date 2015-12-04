{if $show_help}
    <div class="panel">
        <p>
            <button class="btn btn-sm btn-default pull-right right" id="deactivate_help">{l s='Do not show me again.' mod='tenbucks'}</button>
            {if $key}
                {l s='On your first connection, the following key will bey asked:' mod='tenbucks'} <code>{$key|escape:'html':'UTF-8'}</code>
            {else}
                {l s='No key defined in module config,' mod='tenbucks'} <a href="{$generate_uri|escape:'html':'UTF-8'}">{l s='Generate a key' mod='tenbucks' }</a>.
            {/if}
        </p>
    </div>
{/if}
<iframe id="tenbucks_iframe" src="{$iframe_uri|escape:'html':'UTF-8'}"></iframe>
