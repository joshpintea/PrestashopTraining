$filterBox = $("#filter-box");
$newsContent = $("#news-content");

$(".categorynews").click(function () {
    $.ajax({
        type: 'POST',
        url: '../modules/dashnews/ajax.php',
        data: {
            method: 'getAfterIdCategory',
            idCategory: this.id,
            filter: $filterBox.val()
        },
        dataType: 'json',
        success: function (response) {

            renderNews(response.news);
        }
    })
});

$filterBox.keyup(function () {
    $.ajax({
            type: 'POST',
            url: '../modules/dashnews/ajax.php',
            data: {
                method: 'filterNews',
                filter: $filterBox.val()
            },
            dataType: 'json',
            success: function (response) {
                renderNews(response.news);
            }
        }
    )
});

function renderNews(newsArray){
    $newsContent.html('');

    newsArray.forEach(function(newsLetter){
        $a = $('<a></a>').attr('href','display-newsletter/' + newsLetter.id_news);
        $div = $('<div></div>').addClass('news');
        $divTitle = $('<div></div>').addClass('news-title').html("<h1>" + newsLetter.title + "</h1>");
        $divDescription = $('<div></div>').addClass('news-title').html("<b>Description:</b>" + newsLetter.description);
        $divImage = $('<div></div>').addClass('news-image').css('background-image', 'url(' +'../../../../img/dashnews/' + newsLetter.image +')');

        $div.append($divTitle);
        $div.append($divDescription);
        $div.append($divImage);

        $a.append($div);

        $newsContent.append($a);
    })
}

