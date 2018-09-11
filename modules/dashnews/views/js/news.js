$filterBox = $("#filter-box");
$newsContent = $("#news-content");

requestHandler = new RequestHandler();

localStorage.setItem('action', 'getAll');
$page = 0;
$type = "next";

$(".categorynews").click(function () {
    localStorage.setItem('action', 'getAfterIdCategory');
    localStorage.setItem('idCategory', this.id);
    $page = 0;
    getNews({
            method: 'getAfterIdCategory',
            idCategory: this.id,
            filter: $filterBox.val(),
            page: $page
        }
    );
});

$filterBox.keyup(function () {
    localStorage.setItem('action', 'filterNews');
    localStorage.setItem('filterAfter', $filterBox.val());
    $page = 0;
    getNews({
            method: 'filterNews',
            filter: $filterBox.val(),
            page: $page
        }
    );
});

$("#reset-news").click(function () {
    localStorage.setItem('action', 'getAll');
    $page = 0;
    getNews({
        method: 'getAllNews',
        page: $page
    });
    $filterBox.val('');
});

$("#prev").click(function () {
    $type = "prev";
    handlePagination();
});

$("#next").click(function () {
    $type = "next";
    handlePagination();
});

function handlePagination() {
    switch (localStorage.getItem('action')) {
        case 'getAll':
            getNews({
                    method: "getAllNews",
                    page: $page,
                    type: $type,
                }
            );
            break;

        case 'filterNews':
            getNews({
                    method: "filterNews",
                    page: $page,
                    type: $type,
                    filter: localStorage.getItem('filterAfter')
                }
            );
            break;

        case 'getAfterIdCategory':
            getNews({
                    method: "getAfterIdCategory",
                    page: $page,
                    type: $type,
                    filter: $filterBox.val(),
                    idCategory: localStorage.getItem('idCategory'),
                }
            );
            break;
    }
}

function getNews(data) {
    requestHandler.doRequest({
        url: window.location.origin + '/modules/dashnews/ajax.php',
        data: data
    }).success(function (response) {
        $newsContent.html(response.html);
        $page = response.page;
    }).error(function (error) {
        $newsContent.html("<br><strong>Something goes wrong<strong>");
    })
}



