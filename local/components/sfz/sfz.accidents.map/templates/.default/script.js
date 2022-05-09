function Leave(Element) {
    //console.log(Element)
    popup.close()
    return true;
}

function Appear(Element) {
    //onsole.log(Element)
    var sluchai = $(Element).find("span")
    console.log(sluchai)
    popup = BX.PopupWindowManager.create(Element.getAttribute("data-id"), null, {
        content: sluchai,
        darkMode: true,
        autoHide: true,
        closeIcon: false
    });

   popup.show();
   return true;
}