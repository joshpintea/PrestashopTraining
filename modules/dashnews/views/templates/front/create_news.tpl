{extends file='page.tpl'}

{block name='page_content'}
    <div class='error'>
        {if isset($error)}{$error}{/if}
    </div>
    <div class="content">
        <form action="{$link->getModuleLink('dashnews', 'createnews')|escape:'html'}" method="post"
              enctype="multipart/form-data">
            <div class="col-md-9 title-content">
                Title
                <input type="text" name="title">
            </div>

            <div class="col-md-9 description-content">
                Description
                <input type="text" name="description">
            </div>

            <div class="col-md-9 dates-content">
                Date from
                <input type="date" name="date-from"><br>
                Date to
                <input type="date" name="date-to">
            </div>
            <div class="col-md-9 categories-select">
                <select name="categories[]" multiple>
                    {foreach from=$categories key=id item=category}
                        <option value="{$category.id_categorynews}">{$category.title}</option>
                    {/foreach}
                </select>
            </div>

            <div class="col-md-9 upload-image-content">
                Upload Image

                <input type="file" name="image" id="fileToUpload">
            </div>
            <div class="col-md-9">
                <button name="create-news">Save</button>
            </div>
        </form>
    </div>
{/block}

