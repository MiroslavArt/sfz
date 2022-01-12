BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    init: function(usertype) {
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
    }
}

//BX.sfz.General.ChangeThema.init();