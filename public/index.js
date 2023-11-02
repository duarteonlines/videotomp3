
var blobUrl= "blob:https://www.youtube.com/fe648804-dca2-43a9-9a91-b5f07186ab28";
var xhr = new XMLHttpRequest;
xhr.responseType = 'blob';

xhr.onload = function () {
    var recoveredBlob = xhr.response;
    var reader = new FileReader;
    reader.readAsDataURL(recoveredBlob);

    reader.onload = function () {
        console.log(reader.result);
    };
};

xhr.open('GET', blobUrl);
xhr.send();