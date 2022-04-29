BX.namespace('sfz.Group.Interface');

BX.sfz.Group.Interface = {
    groupid: null,
    init: function(groupid) {
        this.groupid = groupid
        //BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
        //BX.addCustomEvent('Kanban.Grid:onItemDragStop', BX.delegate(this.kanbanHandler, this));
        this.preventSlider();
    },
    preventSlider() {
        console.log(this.groupid)
    }
}