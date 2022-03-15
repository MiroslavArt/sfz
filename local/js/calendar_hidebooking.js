BX.namespace('sfz.Calendar.HideBooking');

BX.sfz.Calendar.HideBooking = {
    init: function(hidegroup) {
        if(hidegroup==1) {
            console.log(hidegroup)
            this.hidebookingButton();
        }
        
    },
    hidebookingButton: function() {
        var button = $('.ui-btn-success');
        button.each(function (index, el){
            $(el).css("display", "none");
        });  
        //console.log(button)
        //button.hide()
    }
}