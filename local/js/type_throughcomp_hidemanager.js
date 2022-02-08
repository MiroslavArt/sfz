BX.namespace('sfz.Type.HideManagerEdit');

BX.sfz.Type.HideManagerEdit = {
    ufarr: [],
    init: function(mode, ufarr1, ufarr2) {
        this.ufarr = [ufarr1, ufarr2]
        if(mode=='hidesection') {
            BX.addCustomEvent('BX.CRM.EntityEditorSection:onLayout', BX.delegate(this.hidesection, this));
        } else {
            BX.addCustomEvent('BX.CRM.EntityEditorSection:onLayout', BX.delegate(this.hidesectionchange, this));
        }
    },
    hidesection: function(par1, par2) {
        if (typeof par2 === 'object') {
            if (par2.hasOwnProperty('id')) {
                if(par2.id=='additional') {
                    const node = par1._wrapper
                    $(node).empty();
                }
            }
        }
    }, 
    hidesectionchange: function(par1, par2) {
        console.log(par1)
        if (typeof par2 === 'object') {
            if (par2.hasOwnProperty('id')) {
                if(par2.id=='additional') {
                    const node = par1._wrapper
                    const change = $(node).find('.ui-entity-editor-header-actions');
                    const changemanager = $(node).find('.crm-widget-employee-change');
                    change.each(function (index, el){
                        $(el).css("display", "none");
                    });    
                    changemanager.each(function (index, el){
                        $(el).empty();
                    });   
                }
            }
        }
    },
}