BX.namespace('sfz.Type.HideManagerEdit');

BX.sfz.Type.HideManagerEdit = {
    init: function(mode, ufarr1, ufarr2) {
        if(mode=='hidesection') {
            console.log("switched")
            BX.addCustomEvent('BX.CRM.EntityEditorSection:onLayout', BX.delegate(this.hidesection, this));
        }
    },
    hidesection: function(par1, par2) {
        if (typeof par2 === 'object') {
            if (par2.hasOwnProperty('id')) {
                console.log("here1")
                if(par2.id=='additional') {
                    const node = par1._wrapper
                    console.log(node)
                    $(node).empty();
                }
            }
        }
    }
}