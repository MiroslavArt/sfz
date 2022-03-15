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
    hideClickAction: function() {
        console.log("hello")
        //$(".calendar-grid-month-row").each(function (index, el){
        //    console.log(index)
        //    console.log(el) 
            //$(el).off('click');
        //});
    } 
}