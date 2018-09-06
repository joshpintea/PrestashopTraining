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
        url: window.location.origin + '/modules/dashnews/ajax.php',
        data: data
    }).success(function (response) {
        $newsContent.html(response);
    }).error(function (error) {
        $newsContent.html("<br><strong>Something goes wrong<strong>");
    })
}



