$filterBox = $("#filter-box");
$newsContent = $("#news-content");

requestHandler = new RequestHandler();

$(".categorynews").click(function () {
    getNews({
            method: 'getAfterIdCategory',
            idCategory: this.id,
            filter: $filterBox.val()
        }
    );
});

$filterBox.keyup(function () {
    getNews({
            method: 'filterNews',
            filter: $filterBox.val()
        }
    );
});

$("#reset-news").click(function () {
    getNews({method: 'getAllNews'});
    $filterBox.val('');
});


function getNews(data) {
    requestHandler.doRequest({
        url: '../modules/dashnews/ajax.php',
        data: data
    }).success(function (response) {
        renderNews(response.news);
    }).error(function (error) {
        $newsContent.html("<br><strong>Something goes wrong<strong>");
    })
}

function renderNews(newsArray) {
    $newsContent.html('');

    newsArray.forEach(function (newsLetter) {
        $a = $('<a></a>').attr('href', 'display-newsletter/' + newsLetter.id_news);
        $div = $('<div></div>').addClass('news');
        $divTitle = $('<div></div>').addClass('news-title').html("<h1>" + newsLetter.title + "</h1>");
        $divDescription = $('<div></div>').addClass('news-title').html("<b>Description:</b>" + newsLetter.description);
        $divImage = $('<div></div>').addClass('news-image').css('background-image', 'url(' + '../../../../img/dashnews/' + newsLetter.image + ')');

        $div.append($divTitle);
        $div.append($divDescription);
        $div.append($divImage);

        $a.append($div);

        $newsContent.append($a);
    })
}

