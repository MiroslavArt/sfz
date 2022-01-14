BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    init: function() {
        BX.addCustomEvent('CRM.EntityModel.Change', BX.delegate(this.reacttoChange, this));
    },
    reacttoChange: function(event) {
        console.log(event)
        if(typeof event === 'object') {
            this.clientid = event._settings.data.REQUISITES[0].enitityID
            console.log(this.clientid); 
        }
        //console.log(BX.Crm.EntityEditorSection)
    }
}

