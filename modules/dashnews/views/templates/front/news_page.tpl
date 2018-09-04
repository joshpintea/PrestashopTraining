{extends file='page.tpl'}


{block name='page_content'}
    <div class="search-box">
        <div class="left">
            Search: <input type="text" name="search-filter">
            <a href="news" type="submit">Reset</a>
            <br>

            <div class="categories-container">
                Categories:<br>
                {foreach from=$categories key=row item=category}
                    <a href="{$newsUrl}?id_categorynews={$category.id_categorynews}"
                       class="{if $category.id_categorynews == $idCategory}red{else}blue{/if}">{$category.title}</a>
                    <br>
                {/foreach}
            </div>
        </div>


    </div>
    <div class="right">
        {foreach from=$news key=id item=n}
            <a href="{$displayNewsUrl}/{$n.id_news}">
            <div class='news'>
                <div class='news-title'><h1>{$n.title}</h1></div>
                <div class='news-description'><b>Description:</b>{$n.description}</div>
                <div class='news-image'
                     style="background-image: url('../../img/dashnews/{if $n.image==''}default.png{else}{$n.image}{/if}')">
                </div>
            </div>
            </a>
        {/foreach}
    </div>
{/block}