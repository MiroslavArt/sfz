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
        this.requestSignals(collectSignals).then(function(response) {
            console.log(response);
            //this.processCollectionResponse(response);
            //this.processKanbanSignals();
        }.bind(this), function(error){
            console.log(error);
        }.bind(this));
    },
    requestSignals: function(signal) {
        return BX.ajax.runAction('sfz:custom.api.signal.getSignal', {
            data: {
                signals: signal
            }
        });
    },
}