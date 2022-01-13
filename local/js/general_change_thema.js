BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    userid: null,
    init: function(usertype) {
        this.userid = BX.message('USER_ID')
        console.log(this.userid)
        if(usertype!=1) {
            $("#user-block").attr("onClick","BX.sfz.General.ChangeThema.hideHandler()");
            $(".footer-link").each(function (index, el){
                // Для каждого элемента сохраняем значение в personsIdsArray,
                // если значение есть.
                var v  = $(el).attr("onclick");
                console.log(v)
                //if (v) personsIdsArray.push(v);
            });



        }

    },
    hideHandler: function() {
        console.log(this.userid)
        var useridval = this.userid
        var bindElement = BX("user-block");
		BX.addClass(bindElement, "user-block-active");
		BX.PopupMenu.show("user-menu", bindElement, [
                {
                    text : "Моя страница",
                    className : "menu-popup-no-icon",
                    href: '/company/personal/user/' + useridval + '/'
                },			
                { text : "Настройка уведомлений", className : "menu-popup-no-icon", onclick : "BXIM.openSettings({'onlyPanel':'notify'})"},
                { text : "Панель управления", className : "menu-popup-no-icon", href : "/bitrix/admin/"},
                {'text':'Расширения','items':[{'id':'landing_bind','system':true,'text':'Выбрать базу знаний','onclick':'BX.SidePanel.Instance.open(\'/kb/binding/menu/?menuId=top_panel:user_menu\', {allowChangeHistory: false});','sort':'100','sectionCode':'other'},{'id':'landing_create','system':true,'text':'Создать базу знаний','onclick':'BX.SidePanel.Instance.open(\'/kb/binding/menu/?menuId=top_panel:user_menu&create=Y\', {allowChangeHistory: false});','sort':'100','sectionCode':'other'},{'delimiter':true},{'href':'/marketplace/?placement=USER_PROFILE_MENU','text':'Битрикс24.Маркет'}]},
                { text : "Выйти", className : "menu-popup-no-icon", href : "/auth/?logout=yes&sessid=" + BX.bitrix_sessid() + "&backurl=" + encodeURIComponent(B24.getBackUrl()) }
			],
			{
				offsetTop: -9,
				offsetLeft: 40,
				angle: true,
				events: {
					onPopupClose : function() {
						BX.removeClass(this.bindElement, "user-block-active");
					}
				}
        
            });
        
    },
    
}
//$.holdReady( true );
//BX.sfz.General.ChangeThema.init();

