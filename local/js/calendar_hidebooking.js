BX.namespace('sfz.Calendar.HideBooking');

BX.sfz.Calendar.HideBooking = {
    init: function(hidegroup) {
        if(hidegroup==1) {
            console.log(hidegroup)
            this.hidebookingButton();
        }
        
    },
    hidebookingButton: function() {
        var button = $('.ui-btn-success').first();
        console.log(button)
        button.hide()
    }
}