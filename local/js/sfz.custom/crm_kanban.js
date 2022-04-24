BX.namespace('sfz.crm.kanban');

BX.sfz.crm.kanban = {
    kanban: null,
    init: function() {
        BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
        BX.addCustomEvent('Kanban.Grid:onItemDragStop', BX.delegate(this.kanbanHandler, this));
    },
    kanbanHandler: function(grid){
        this.kanban = grid;
        var sum = 0
        var total = 0
        var node
        console.log(grid)
        if(grid.hasOwnProperty('columns')) {
            for(var i in grid.columns) {
                console.log(i)
                //sum = i.data.sum;
                //total = i.total;
                //node = i.layout.info
                //console.log(sum)
                //console.log(total)
                //console.log(node)
                //if(i.data.sum>0) {
    
                //}
            //    if(i>0) {
                    //var localValue = localStorage.getItem(i);
                    //if (localValue == null) {
            //            collectSignals.push(i);
                    //}
    
            //    }
            }
        }

        
        //console.log(grid)
        //var collectSignals = []
        //for(var i in grid.items) {
        //    if(i>0) {
                //var localValue = localStorage.getItem(i);
                //if (localValue == null) {
        //            collectSignals.push(i);
                //}

        //    }
        //}
        //console.log(collectSignals)
        //this.requestSignals(collectSignals).then(function(response) {
        //    console.log(response);
        //    this.processCollectionResponse(response);
            
            //this.processKanbanSignals(response);
        //}.bind(this), function(error){
        //    console.log(error);
        //}.bind(this));
    },
    requestSignals: function(signals) {
        return BX.ajax.runAction('sfz:custom.api.signal.getSignal', {
            data: {
                signals: signals
            }
        });
    },
    processCollectionResponse(response) {
        if(response.hasOwnProperty('status')) {
            console.log("status")
            if(response.status == 'success') {
                this.processKanbanSignals(response);
            }
        }
    },   
    processKanbanSignals(response) {

    }
}