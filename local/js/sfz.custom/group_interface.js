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
        var diskid = "#group_panel_menu_" + this.groupid + "_files"; 
        //console.log(diskid)
        if ($(diskid).length > 0) {
            $(diskid).attr('data-onclick', "top.location.href = '/workgroups/group/" + this.groupid + "/disk/path/'");
        } 
    }
}