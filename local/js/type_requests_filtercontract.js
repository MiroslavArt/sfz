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
                //console.log(form.parentNode)
                /*obSelect = BX.findChild(BX("body"), {
                    "tag" : "div",
                    "class" : "ui-entity-editor-section-edit"
                    }, 
                    true, true
                );*/
                //"use strict";

                //Array.from(document.getElementsByClassName("enumeration-select")).forEach(function(item) {
                //    console.log(item.id);
                //});

                //NodeList.prototype[Symbol.iterator] = Array.prototype[Symbol.iterator];
                //HTMLCollection.prototype[Symbol.iterator] = Array.prototype[Symbol.iterator];

                //var list = document.getElementsByClassName("ui-entity-editor-content-block");
                //console.log(list[0])
                //for (var item of list) {
                //    console.log(item);

                //}
                                    
                //var elems = document.getElementsByClassName('enumeration-select')
                //console.log(elems)
                //for (let elem of elems) {
                //    var children = elem.childNodes;
                //    for (var i = 0; i < children.length; ++i) {
                //        console.log(children[i])
                //    }
                //}
                //var $value = $("[name='UF_CRM_1_1642152336']");
                //console.log($value.length)
                
                var up_names = document.querySelector('[name="UF_CRM_1_1642152336"]');
                //var up_names = document.getElementsByName("UF_CRM_1_1642152336");
                console.log(up_names)
                console.log(typeof up_names)
                console.log(up_names.remove())
                //console.log(up_names.length())
                //let myArray = Array.from(up_names)
                //console.log(myArray)
                //console.log(up_names.outerText)
                //var up_names = document.querySelectorAll('[data-cid="UF_CRM_1_1642152336"]');
                //console.log(up_names[0]);
                //var wrap = up_names[0];
                //var select = wrap.querySelectorAll('[name="UF_CRM_1_1642152336"]');
                //var select = wrap.getElementsByName("UF_CRM_1_1642152336");
                //console.log(select)
                //var select =  wrap.querySelectorAll('[name="UF_CRM_1_1642152336"]')   
                //console.log(select) 
                //console.log(select[0]);
                //select[0].remove()
                //var options = up_names.querySelectorAll('option');
                //console.log(up_names)
                //up_names.forEach((rate) => {
                //    console.log(rate)
                //});
                
                /*if(up_names[0]) {
                    var options = up_names[0].querySelectorAll('option');
                    options.forEach(o => o.remove());
                }*/
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