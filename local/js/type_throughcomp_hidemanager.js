BX.namespace('sfz.Type.HideManagerEdit');

BX.sfz.Type.HideManagerEdit = {
    init: function(mode, ufarr1, ufarr2) {
        console.log(mode); 
        console.log(ufarr1); 
        console.log(ufarr2); 
        if(mode=='hidesection') {
            console.log("switched")
            BX.addCustomEvent('BX.CRM.EntityEditorSection:onLayout', BX.delegate(this.hidesection, this));
        }
    },
    hidesection: function(par1, par2) {
       console.log('start'); 
       console.log(par1);
       console.log(par2);
    }
}