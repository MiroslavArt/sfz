BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    init: function(usertype) {
        BX.addCustomEvent('BX.UI.ActionPanel:created', BX.delegate(this.hideHandler, this));
        /*switch(type) {
            case 'detail':
                BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
                BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
                break;
            case 'kanban':
                BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
                break;
        }*/
        console.log(usertype); 
    },
    hideHandler: function(ActionPanel) {
        console.log(ActionPanel)
        console.log("click")
        var elem = $("#user-block");
        elem.remove();
        //var elemsTotal = elems.length;
        console.log(elem); 
        $('span.menu-popup-item-text').each(
            function(item)
                {
                    console.log(item)
                    console.log(item.text())
                }
            );
    }
}

//BX.sfz.General.ChangeThema.init();