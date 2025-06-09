BX.namespace('sfz.wf.features');

BX.sfz.wf.features = {
    card: 1,
    init: function() {
        BX.addCustomEvent('onUCFormInit', BX.delegate(this.popupHandler, this));
        /*BX.addCustomEvent('onInitialized', function() {
            console.log('Command');
        });*/
       
    },
    popupHandler: function(popup){
        $('.bizproc-type-control-select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

    }
}

