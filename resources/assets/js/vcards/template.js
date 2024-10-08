listenClick( '.copy-clipboard', function () {
    let vcardId = $(this).data('id');
    let $temp = $('<input>');
    $('body').append($temp);
    $temp.val($('#vcardUrl' + vcardId).text()).select();
    document.execCommand('copy');
    $temp.remove();
    displaySuccessMessage(Lang.get('js.copied_successfully'));
});

listenClick(".vcard-copy-clipboard", function () {
         let vcardId = $(this).data("id");
         var innerText = $('#vcardUrlCopy'+vcardId).text();
         let temp = $("<input>");
         temp.val(innerText);
         $("body").append(temp);
         temp[0].select();
         document.execCommand("copy");
         temp.remove();
         displaySuccessMessage(Lang.get("js.copied_successfully"));
     });
