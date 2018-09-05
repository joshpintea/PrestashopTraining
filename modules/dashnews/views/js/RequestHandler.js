
function RequestHandler() {
    this.doRequest = function (paramsObject) {
        var method = paramsObject.method || 'GET';
        var data = paramsObject.data || {};
        var url = paramsObject.url || {};

        return $.ajax(url, {
            method: method,
            data: data,
            headers: {
                'content-type': 'application/json',
            }
        })
    };
}