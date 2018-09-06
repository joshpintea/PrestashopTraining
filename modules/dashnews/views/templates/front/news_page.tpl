{extends file='layouts/layout-both-columns.tpl'}

{block name="left_column"}
    <div class="content-news">
        <div class="search-box">
            {l s="Search"}: <input type="text" name="search-filter" id="filter-box">
            <a id="reset-news" type="submit">Reset</a>
            <br>

            <div class="categories-container">
                {l s="Categories"}
                <br>
                {foreach from=$categories key=row item=category}
                    <a type='button'
                       class="categorynews"
                       id='{$category.id_categorynews}'>
                        {$category.title}
                    </a>
                    <br>
                {/foreach}
            </div>
        </div>
    </div>
{/block}

{block name="right_column"}
    <div class="right-column col-xs-3 col-sm-8 col-md-8">
        <div id="news-content">
            {foreach from=$news key=id item=n}
                <a href="{$link->getModuleLink('dashnews', 'newsletterpage',['id_news'=>{$n.id_news}])}">
                    <div class='news'>
                        <div class='news-title'><h1>{l s="Title"}</h1>{$n.title}</div>
                        <div class='news-description'><b>{l s="Description"}:</b>{$n.description}</div>
                        <div class='news-image'
                             style="background-image: url('{if $n.image==''}../../modules/dashnews/default.jpg{else}../../../img/dashnews/{$n.image}{/if}')">
                        </div>
                    </div>
                </a>
            {/foreach}
        </div>
    </div>
{/block}

{block name="content"}

{/block}

