BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    clientid: null, 
    contractuf: null,
    init: function(contuf, clid) {
        this.contractuf = contuf; 
        if(clid != 'na') {
            this.clientid = clid; 
        } else {
            this.clientid = null
        }
        console.log(this.contractuf)
        console.log(this.clientid)
        BX.addCustomEvent('BX.CRM.EntityEditor:onInit', BX.delegate(this.reacttoChange, this));
        //BX.addCustomEvent('BX.UI.EntityEditorField:onLayout', BX.delegate(this.fieldLayoutHandler, this));
        if(this.clientid != null) {
            this.requestContracts().then(function(response) {

                this.processCollectionResponse(response);

            }.bind(this), function(error){
                console.log(error);
            }.bind(this));
        } else {
            this.clearstorage();
        }
    },
    requestContracts: function() {
        return BX.ajax.runAction('sfz:custom.api.signal.getContract', {
            data: {
                companyid: this.clientid
            }
        });
    },
    processCollectionResponse: function(response) {
        console.log(response);
        if(response.hasOwnProperty('status')) {
            console.log("status")
            if(response.status == 'success') {
                console.log("success")
                //if(response.data.length) {
                //    console.log("legnth")
                var output = [], item;
                for (var id in response.data) {
                    item = {};
                    item.id = id;
                    item.name = response.data[id];
                    output.push(item);
                }
                console.log(output);
                if(output.length) {
                    localStorage.setItem('request_cnt', JSON.stringify(output));
                } else {
                    this.clearstorage();
                }
            }
        }
        //console.log(localStorage)
    },
    clearstorage: function() {
       localStorage.setItem('request_cnt', null);
    },
    reacttoChange: function(event, data) {
        //console.log(event)
        //console.log(data)
        if(typeof event === 'object') {
            const reg = /COMPANY/
            if(reg.test(data.id)) {
                this.clientid = data.entityId;
                console.log(this.clientid)
                //this.requestContracts().then(function(response) {
                    //console.log(response);
                    //this.processCollectionResponse(response);
                    var select = document.querySelector('[name="'+this.contractuf+'"]');
                    //this.processSelectHandler(select)
                    console.log(select)
                    //if(select !== null) {
                    //    var options = select.querySelectorAll('option');

                    //    options.forEach(function(option, i, arr) {
                    //        if(option.value==3871) {
                    //            option.remove()
                    //        }
                            //console.log(option.value)
                    //    });
                    // options.forEach(o => o.remove());
                    //}
                    //this.processKanbanitemSignals(grid.grid.items);
                //}.bind(this), function(error){
                //    console.log(error);
                //}.bind(this));
                //var form = event._formElement

                //var up_names = document.querySelectorAll('[data-cid="UF_CRM_1_1642152336"]');
                //console.log(up_names[0]);
                //var wrap = up_names[0];

                //var select = document.querySelectorAll('[name="UF_CRM_1_1642152336"]');
                
            }
        }
    },
    fieldLayoutHandler: function (field) {
        console.log(field)
        if (typeof field === 'object') {
            if (field.hasOwnProperty('_id')) {
                if(field._id==this.contractuf) {
                    var select = field._innerWrapper
                    //this.processSelectHandler(select)
                    var newselect = select.querySelector('[name="UF_CRM_1_1642152336"]');
                    console.log(newselect)
                    if(newselect != null) {
                        this.processSelectHandler(newselect)
                    }
                    //var options = select.querySelectorAll('option');
                    //options.forEach(function(option, i, arr) {
                    //    if(option.value==3871) {
                    //        option.remove()
                    //    }
                        //console.log(option.value)
                    //});
                }
            }
        }
    },
    processSelectHandler: function (select) {
        var localValue = localStorage.getItem('request_cnt')
        console.log(localValue)
        console.log(select)    
    }
}

//BX.sfz.Type.RequestsFilterContract.init()

