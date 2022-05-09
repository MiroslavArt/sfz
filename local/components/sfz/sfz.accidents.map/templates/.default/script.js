function Leave(Element) {
    //console.log(Element)
    popup.close()
    return true;
}

function Appear(Element) {
    console.log(Element)
    popup = BX.PopupWindowManager.create(Element.getAttribute("data-id"), null, {
        content: '<div>xxx</div>',
        darkMode: true,
        autoHide: true,
        closeIcon: true
    });

   popup.show();
   return true;
}