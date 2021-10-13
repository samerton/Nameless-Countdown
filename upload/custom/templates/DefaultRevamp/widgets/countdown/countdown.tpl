<div class="ui fluid card" id="widget-countdown">
    <div class="content">
        {if isset($COUNTDOWN_TITLE)}
            <h4 class="ui header">{$COUNTDOWN_TITLE}</h4>

            <div class="center aligned description">
                {$COUNTDOWN_DESCRIPTION}
            </div>

            <pre id="countdown-value" class="center aligned countdown" data-expires="{$COUNTDOWN_EXPIRES}"></pre>
        {else}
            <div class="ui info message">
                {$NO_COUNTDOWN_AVAILABLE}
            </div>
        {/if}
    </div>
</div>
