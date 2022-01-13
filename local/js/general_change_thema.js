BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    init: function(usertype) {
        if(usertype==1) {
            $("#user-block").onclick(function (event) {
                event.preventDefault()
            })
        }
        //BX.addCustomEvent('BX.UI.ActionPanel:created', BX.delegate(this.hideHandler, this));
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
        
        //$(function(){
            //onPageLoad();
        //$.holdReady( false );
        //var elem = $("#menu-popup-user-menu")
        //var elemsTotal = elems.length;
        //console.log(elem); 
        //$(".menu-popup-item-text").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
        //    console.log($(el).text());
            
        //});
        //});
    },
    /*detailHandler: function(par1, par2) {
        console.log("detailed")
        console.log(par1)
        console.log(par2)
    }*/
}
$.holdReady( true );
BX.sfz.General.ChangeThema.init();