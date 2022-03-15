BX.namespace('sfz.Calendar.HideBooking');

BX.sfz.Calendar.HideBooking = {
    init: function(hidegroup) {
        if(hidegroup==1) {
            console.log(hidegroup)
            this.hidebookingButton();
            BX.addCustomEvent('onCalendarAfterBuildViews', BX.delegate(this.hideClickAction, this));
        }
    },
    hidebookingButton: function() {
        $(".pagetitle-container").each(function (index, el){
            if(index==1) {
                $(el).remove()
            }
        });
    }, 
    hideClickAction: function(event, data) {
        //event.sectionManager.sections = []; 
        event.sectionManager.sections.forEach(function(item, i, arr) {
            if(item.CAL_TYPE=="location") {
                arr.splice(i, i); 
            }
        });
        console.log(event)
       
        //var options = node.querySelectorAll('span');
        //console.log(options)
        //$(node).remove()
        //const change = $(node).find('.calendar-views-container');
        //change.each(function (index, el){
        //    console.log(el)
            //$(el).css("display", "none");
        //});  
        //$(".calendar-grid-month-row").each(function (index, el){
        //    console.log(index)
        //    console.log(el) 
            //$(el).off('click');
        //});
    } 
}