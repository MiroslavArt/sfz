BX.namespace('sfz.Type.RequestsFilterContract');

BX.sfz.Type.RequestsFilterContract = {
    init: function() {
        BX.addCustomEvent('CRM.EntityModel.Change', BX.delegate(this.reacttoChange, this));
    },
    reacttoChange: function(event) {
        console.log(event)
    }
}

