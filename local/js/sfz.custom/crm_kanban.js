BX.namespace('sfz.crm.kanban');

BX.sfz.crm.kanban = {
    kanban: null,
    init: function() {
        BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
        BX.addCustomEvent('Kanban.Grid:onItemDragStop', BX.delegate(this.kanbanHandler, this));
    },
    kanbanHandler: function(grid){
        this.kanban = grid;
        console.log(grid)
        var collectSignals = []
        for(var i in grid.items) {
            if(i>0) {
                //var localValue = localStorage.getItem(i);
                //if (localValue == null) {
                    collectSignals.push(i);
                //}

            }
        }
        console.log(collectSignals)
    }
}