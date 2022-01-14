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
                console.log(form.parentNode)
                /*obSelect = BX.findChild(BX("body"), {
                    "tag" : "div",
                    "class" : "ui-entity-editor-section-edit"
                    }, 
                    true, true
                );*/
                //var elems = document.getElementsByClassName('enumeration-select')
                //for (let elem of elems) {
                //    var children = elem.childNodes;
                //    for (var i = 0; i < children.length; ++i) {
                //        console.log(children[i])
                //    }
                //}
                var up_names = document.getElementsByName("UF_CRM_1_1642152336");
                console.log(up_names[0])
                if(up_names[0]) {
                    var options = up_names[0].querySelectorAll('option');
                    options.forEach(o => o.remove());
                }
                //let parent = form.closest('.ui-entity-editor-content-block')
                //console.log(parent);
                //var form = event._formElement;
                //console.log(form)
                //var parentform = $(form).parent();
                //var secparentform  =  $(parentform).parent(); 
                //var parentform = event._formElement.parentElement.parentNode
                //var secparentform = $(parentform).parent()
                //console.log(parentform)
                //var core = event.ownerDocument.body
                //var parentform = BX.findChild(core, {"tag" : "select"}, true, true)
                //var parentform = event._formElement.parentElement
                //parentform = parentform.parent();
                //var parentform = $(form).parents('.ui-entity-editor-section-content');
                //var parentform = BX.findParent(form, {"class" : "ui-entity-editor-content-block"}, {"data-cid" : "CLIENT"});
                
                //console.log(secparentform)
            }
        }
         
        //if(typeof event === 'object') {
            //console.log(event._settings.data.REQUISITES[0])
        //    this.clientid = event._settings.data.REQUISITES[0].entityId
        //    console.log(this.clientid); 
        //}
        //let elements = document.querySelectorAll('select');
        //console.log(elements)
        
        //for (let elem of document.body.children) {
        //    if (elem.matches('select')) {
        //      console.log(elem)
        //    }
        //}
        //console.log(BX.Crm.EntityEditorSection)
        //var options = $("select").find(`[name=UF_CRM_1_1642152336]`)
        //console.log(options)
    }
}

//BX.sfz.Type.RequestsFilterContract.init()