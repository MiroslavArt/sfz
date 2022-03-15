BX.namespace('sfz.Calendar.HideBooking');

BX.sfz.Calendar.HideBooking = {
    init: function(hidegroup) {
        if(hidegroup==1) {
            console.log(hidegroup)
            this.hidebookingButton();
            this.hideClickAction(); 
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
        $(".bx-layout-inner-inner-cont").each(function (index, el){
            console.log(index)
            //console.log(index) 
            //$(el).off('click');
        });
    } 
}