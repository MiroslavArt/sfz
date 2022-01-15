BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    init: function() {
        BX.addCustomEvent('BX.CRM.EntityEditor:onInit', BX.delegate(this.reacttoChange, this));
    },
    reacttoChange: function(event, data) {
        //console.log(event)
        //console.log(data)
        if(typeof event === 'object') {
            const reg = /COMPANY/
            if(reg.test(data.id)) {
                this.clientid = data.entityId;
                console.log(this.clientid)
                var form = event._formElement

                //var up_names = document.querySelectorAll('[data-cid="UF_CRM_1_1642152336"]');
                //console.log(up_names[0]);
                //var wrap = up_names[0];

                //var select = document.querySelectorAll('[name="UF_CRM_1_1642152336"]');
                var select = document.querySelector('[name="UF_CRM_1_1642152336"]');
                console.log(select)
                if(select !== null) {
                    var options = select.querySelectorAll('option');
                    options.forEach(o => o.remove());
                }
            }
        }
         
        
    }
}

//BX.sfz.Type.RequestsFilterContract.init()

