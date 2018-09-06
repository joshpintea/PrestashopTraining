{extends file='page.tpl'}

{block name='page_content'}
    <div class = "news-letter" >
        <div class = 'image' style="background-image:  url('{if $newsLetter.image==''}../../modules/dashnews/default.jpg{else}../../img/dashnews/{$newsLetter.image}{/if}')"></div>

        <div class="title">
            <strong>{l s="Title"}: </strong>{$newsLetter.title}
        </div>

        <div class="description">
            <strong>{l s="Description"}: </strong> {$newsLetter.description}
        </div>

        <div>
            {l s="Available until"}: {$newsLetter.date_to}
        </div>

    </div>
{/block}

