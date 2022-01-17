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

        BX.addCustomEvent('BX.CRM.EntityEditor:onInit', BX.delegate(this.reacttoChange, this));
        BX.addCustomEvent('BX.UI.EntityEditorField:onLayout', BX.delegate(this.fieldLayoutHandler, this));
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
        //console.log(response);
        if(response.hasOwnProperty('status')) {
            //console.log("status")
            if(response.status == 'success') {

                if(Object.keys(response.data).length>0) {
                    localStorage.setItem('request_cnt', JSON.stringify(response.data));
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
                //console.log(this.clientid)
                var select = document.querySelector('[name="'+this.contractuf+'"]');
                this.requestContracts().then(function(response) {
                    //console.log(response);
                    this.processCollectionResponse(response);
                    var select = document.querySelector('[name="'+this.contractuf+'"]');
                    if(select !== null) {
                        this.processSelectHandler(select, true)

                    }
                }.bind(this), function(error){
                    console.log(error);
                }.bind(this));
            }
        }
    },
    fieldLayoutHandler: function (field) {
        //console.log(field)
        if (typeof field === 'object') {
            if (field.hasOwnProperty('_id')) {
                if(field._id==this.contractuf) {
                    var select = field._innerWrapper
                    //this.processSelectHandler(select)
                    var newselect = select.querySelector('[name="'+this.contractuf+'"]');

                    if(newselect != null) {
                        this.processSelectHandler(newselect)
                    }
                }
            }
        }
    },
    processSelectHandler: function (select, refresh=false) {
        var localValue = JSON.parse(localStorage.getItem('request_cnt'))
        console.log(localValue)
        var options = select.querySelectorAll('option');
        if(!refresh) {
            if(localValue!==null) {
                console.log('object')
                options.forEach(function(option, i, arr) {
                    var optval = option.value
                    if(optval) {
                        if(!localValue.hasOwnProperty(optval)) {
                            option.remove()
                        }
                    }
                })
            } else {
                options.forEach(function(option, i, arr) {
                    var optval = option.value
                    if(optval) {
                        option.remove()
                    }
                })
            }
        } else {
            options.forEach(function(option, i, arr) {
                var optval = option.value
                if(optval) {
                    option.remove()
                }
            })
            if(typeof localValue==='object') {
                for (var key in localValue) {
                    var opt = document.createElement('option');
                    opt.value = key;
                    opt.innerHTML = localValue[key];
                    select.appendChild(opt);
                }
            }
        }
    }
}

//BX.sfz.Type.RequestsFilterContract.init()

