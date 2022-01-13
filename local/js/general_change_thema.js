BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    init: function() {
        BX.addCustomEvent('BX.UI.ActionPanel:created', BX.delegate(this.hideHandler, this));
        //BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
        /*switch(type) {
            case 'detail':
                BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
                BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
                break;
            case 'kanban':
                BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
                break;
        }*/
        //console.log(usertype); 
    },
    hideHandler: function() {
        //console.log("window")
        //console.log(ActionPanel)
        //console.log(param1)
        //console.log(param2)
        //console.log("click")
        
        $(function(){
            //onPageLoad();
            var elem = $("#menu-popup-user-menu")
            //var elemsTotal = elems.length;
            console.log(elem); 
            $('span.menu-popup-item-text').each(
                function(item)
                    {
                        console.log(item)
                        //console.log(item.text())
                    }
                );
        });
    },
    /*detailHandler: function(par1, par2) {
        console.log("detailed")
        console.log(par1)
        console.log(par2)
    }*/
}

BX.sfz.General.ChangeThema.init();