$(document).ready(() => {
  let newsCount = 0;
  $.ajax({
    type: "GET",
    url: requestURL,
    contentType: "text/html; charset=utf-8",
    dataType: "html",
    success: function (resp, textStatus, jqXHR) {
      if (jqXHR.status === 200) {
        let data = {};
        let counter = 0;
        $(resp).find("tr").each(function(index, item){
          if($(item).hasClass("athing")){
            // console.log($(item).find(".titlelink").text());

            data[counter] = {"title": $(item).find(".titlelink").text()};
            data[counter] = {...data[counter],"external_link": $(item).find(".titlelink").attr("href")};
          }else{
            data[counter] = {...data[counter], "internal_link": $(item).find(".age > a").attr("href"),
            };
            data[counter] = {...data[counter], "date_time": $(item).find(".age > a").text()};
            counter++;
          }
        });

        counter = 0;

        while (counter < 100) {
          $("#app").append(
            `<div class="item">
                <h3 class="item-title">${data[counter].title}</h3>
                <div class="item-details">
                    <div class="item-detail external-link"><h5>Externí odkaz</h5><p>${(data[counter].external_link === data[counter].internal_link)? "---" : data[counter].external_link}</p></div>
                    <div class="item-detail internal-link"><h5>Interní odkaz</h5><p>${data[counter].internal_link}</p></div>
                    <div class="item-detail created-at"><h5>Datum a čas přidání</h5><p>${data[counter].date_time}</p></div>
                </div>
            </div>`
          );
          counter++;
        }

        $(".site-title").show();
          setInterval(() => {
            $("#spinner-container").css("display", "none");
          }, 200);
      }
    },
  });
});
