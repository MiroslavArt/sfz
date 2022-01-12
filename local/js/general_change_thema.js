BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    init: function(usertype) {
        BX.addCustomEvent('BX.UI.Viewer.Controller:onSetItems', BX.delegate(this.hideHandler, this));
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
    hideHandler: function(grid) {
        console.log("hide");
        console.log(grid)
    }
}

//BX.sfz.General.ChangeThema.init();