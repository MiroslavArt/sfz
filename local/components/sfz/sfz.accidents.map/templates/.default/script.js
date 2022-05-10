function Leave(Element) {
    //console.log(Element)
    popup.close()
    return true;
}

function Appear(Element) {
    //onsole.log(Element)
    var sluchai = $(Element).find("span")
    var text = ''
    $.each(sluchai,function(index,value){

        // действия, которые будут выполняться для каждого элемента массива
        // index - это текущий индекс элемента массива (число)
        // value - это значение текущего элемента массива
        
        //выведем индекс и значение массива в консоль
        text += '<b>' + 'В ' + $(value).attr('data-year') + ' Категория: ' + $(value).attr('data-tyazh') + 
            ' С: ' + $(value).attr('data-dolzhn') + '</b><br/>' + $(value).text() + '<br/>'
      
    });
    //(Element.getAttribute("data-id")
    popup = BX.PopupWindowManager.create(Element.getAttribute("data-id"), null, {
        content: text,
        width: 400,
        darkMode: true,
        autoHide: true,
        closeIcon: false
    });

   popup.show();
   return true;
}