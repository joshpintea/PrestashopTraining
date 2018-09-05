{extends file='page.tpl'}


{block name='page_content'}
    <div class="search-box">
        <div class="left">
            Search: <input type="text" name="search-filter" id="filter-box">
            <a href="news" type="submit">Reset</a>
            <br>

            <div class="categories-container">
                Categories:<br>
                {foreach from=$categories key=row item=category}
                    <a type = 'button'
                       class="categorynews"
                       id='{$category.id_categorynews}'>
                        {$category.title}
                    </a>
                    <br>
                {/foreach}
            </div>
        </div>


    </div>
    <div class="right" id="news-content">
        {foreach from=$news key=id item=n}
            <a href="{$displayNewsUrl}/{$n.id_news}">
            <div class='news'>
                <div class='news-title'><h1>{$n.title}</h1></div>
                <div class='news-description'><b>Description:</b>{$n.description}</div>
                <div class='news-image'
                     style="background-image: url('{if $n.image==''}../../modules/dashnews/default.jpg{else}../../img/dashnews/{$n.image}{/if}')">
                </div>
            </div>
            </a>
        {/foreach}
    </div>
{/block}