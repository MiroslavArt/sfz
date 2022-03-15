BX.namespace('sfz.Calendar.HideBooking');

BX.sfz.Calendar.HideBooking = {
    init: function(hidegroup) {
        if(hidegroup==1) {
            console.log(hidegroup)
            this.hidebookingButton();
        }
        
    },
    hidebookingButton: function() {
        
        $(".pagetitle-container").each(function (index, el){
            console.log(index)
            console.log(el)
            $(el).remove()
        });
        //button.each(function (index, el){
        //    console.log($(el))
        //    $(el).css("display", "none");
        //});  
        //console.log(button)
        //button.hide()
    }
}