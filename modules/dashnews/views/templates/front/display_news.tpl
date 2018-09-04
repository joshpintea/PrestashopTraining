
{block name='page_content'}
    {foreach from=$news key=id item=n}
        <div class='news'>
            <div class='news-title'><h1>{$n.title}</h1></div>
            <div class='news-description'><b>Description:</b>{$n.description}</div>
            <div class='news-image' style="background-image: url('../../img/dashnews/{if $n.image==''}default.png{else}{$n.image}{/if}')" >
            </div>
        </div>
    {/foreach}
{/block}

