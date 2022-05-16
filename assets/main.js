$(document).ready(() => {
  let newsCount = 0;
  $.ajax({
    type: "GET",
    url: requestURL,
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    success: function (resp, textStatus, jqXHR) {
      if (jqXHR.status === 200) {
        // console.log(resp.message);
        resp.message.forEach((item) => {
          // console.log(item.title);
          $("#app").append(
            `<div class="item">
                <h3 class="item-title">${item.title}</h3>
                <div class="item-details">
                    <div class="item-detail external-link"><h5>Externí odkaz</h5><p>${(item.external_link === item.internal_link)? "---" : item.external_link}</p></div>
                    <div class="item-detail internal-link"><h5>Interní odkaz</h5><p>${item.internal_link}</p></div>
                    <div class="item-detail created-at"><h5>Datum a čas přidání</h5><p>${item.date_time}</p></div>
                </div>
            </div>`
          );
        });

        $(".site-title").show();
        setInterval(() => {
          $("#spinner-container").css("display", "none");
        }, 200);
      }
    },
  });
});
