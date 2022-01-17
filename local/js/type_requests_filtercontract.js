BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    contractuf: null,
    init: function(contuf, clid) {
        this.contractuf = contuf; 
        if(clid != 'na') {
            this.clientid = clid; 
        }
        console.log(this.contractuf)
        console.log(this.clientid)
        BX.addCustomEvent('BX.CRM.EntityEditor:onInit', BX.delegate(this.reacttoChange, this));
        BX.addCustomEvent('BX.UI.EntityEditorField:onLayout', BX.delegate(this.fieldLayoutHandler, this));
    },
    reacttoChange: function(event, data) {
        //console.log(event)
        //console.log(data)
        if(typeof event === 'object') {
            const reg = /COMPANY/
            if(reg.test(data.id)) {
                this.clientid = data.entityId;
                console.log(this.clientid)
                //var form = event._formElement

                //var up_names = document.querySelectorAll('[data-cid="UF_CRM_1_1642152336"]');
                //console.log(up_names[0]);
                //var wrap = up_names[0];

                //var select = document.querySelectorAll('[name="UF_CRM_1_1642152336"]');
                var select = document.querySelector('[name="'+this.contractuf+'"]');
                console.log(select)
                if(select !== null) {
                    var options = select.querySelectorAll('option');

                    options.forEach(function(option, i, arr) {
                        if(option.value==3871) {
                            option.remove()
                        }
                        //console.log(option.value)
                    });
                   // options.forEach(o => o.remove());
                }
            }
        }
    },
    fieldLayoutHandler: function (field) {
        if (typeof field === 'object') {
            if (field.hasOwnProperty('_id')) {
                if(field._id==this.contractuf && field._mode === 1) {
                    var select = field._innerWrapper
                    var options = select.querySelectorAll('option');
                    options.forEach(function(option, i, arr) {
                        if(option.value==3871) {
                            option.remove()
                        }
                        //console.log(option.value)
                    });
                }
            }
        }
    }
}

//BX.sfz.Type.RequestsFilterContract.init()

