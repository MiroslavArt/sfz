BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    init: function() {
        BX.addCustomEvent('BX.CRM.EntityEditor:onInit', BX.delegate(this.reacttoChange, this));
    },
    reacttoChange: function(event, data) {
        console.log(event)
        console.log(data)
        if(typeof event === 'object') {
            this.clientid = data.entityId;
            console.log(this.clientid)
            var form = event._formElement;
            console.log(form)
            //var parentform = $(form).parents('div.ui-entity-editor-content-block');
            var parentform = BX.findParent(form, {"class" : "ui-entity-editor-content-block"}, {"data-cid" : "CLIENT"});
            console.log(parentform)
        }
         
        //if(typeof event === 'object') {
            //console.log(event._settings.data.REQUISITES[0])
        //    this.clientid = event._settings.data.REQUISITES[0].entityId
        //    console.log(this.clientid); 
        //}
        //let elements = document.querySelectorAll('select');
        //console.log(elements)
        
        //for (let elem of document.body.children) {
        //    if (elem.matches('select')) {
        //      console.log(elem)
        //    }
        //}
        //console.log(BX.Crm.EntityEditorSection)
        //var options = $("select").find(`[name=UF_CRM_1_1642152336]`)
        //console.log(options)
    }
}

BX.sfz.Type.RequestsFilterContract.init()