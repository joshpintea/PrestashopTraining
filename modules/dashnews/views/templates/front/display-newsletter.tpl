{extends file='page.tpl'}

{block name='page_content'}
    <div class = "news-letter" >
        <div class = 'image' style="background-image:  url('../../img/dashnews/{if $newsLetter.image==''}default.png{else}{$newsLetter.image}{/if}')"></div>

        <div class="title">
            <strong>Title: </strong>{$newsLetter.title}
        </div>

        <div class="description">
            <strong>Description: </strong> {$newsLetter.description}
        </div>

        <div>
            Available until:  {$newsLetter.date_to}
        </div>

    </div>
{/block}

