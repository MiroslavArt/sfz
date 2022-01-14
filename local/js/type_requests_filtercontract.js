BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    init: function() {
        BX.addCustomEvent('CRM.EntityModel.Change', BX.delegate(this.reacttoChange, this));
    },
    reacttoChange: function(event) {
        //console.log(event)
        if(typeof event === 'object') {
            //console.log(event._settings.data.REQUISITES[0])
            this.clientid = event._settings.data.REQUISITES[0].entityId
            console.log(this.clientid); 
        }
        //let elements = document.querySelectorAll('select');
        
        for (let elem of document.body.children) {
            if (elem.matches('select')) {
              console.log(elem)
            }
        }
        //console.log(BX.Crm.EntityEditorSection)
        //var options = $("select").find(`[name=UF_CRM_1_1642152336]`)
        //console.log(options)
    }
}

BX.sfz.Type.RequestsFilterContract.init()