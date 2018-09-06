
{block name='page_content'}
    <h1>{l s="News"}</h1><br>
    {foreach from=$news key=id item=n}
        <div class='news'>
            <div class='news-title'><h1>{$n.title}</h1></div>
            <div class='news-description'><b>{l s="Description"}:</b>{$n.description}</div>
            <div class='news-image' style="background-image: url('{if $n.image==''}../../modules/dashnews/default.jpg{else}../../img/dashnews/{$n.image}{/if}')" >
            </div>
        </div>
    {/foreach}
{/block}

