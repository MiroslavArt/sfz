BX.namespace('sfz.Type.HideManagerEdit');

BX.sfz.Type.HideManagerEdit = {
    init: function(mode, ufarr) {
        console.log(ufarr); 
        if(mode=='hidesection') {
            BX.addCustomEvent('BX.UI.EntityConfigurationManager:onInitialise', BX.delegate(this.hidesection, this));
        }
    },
    hidesection: function(par1, par2) {
       console.log(par1);
       console.log(par2);
    }
}