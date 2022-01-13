BX.namespace('sfz.General.ChangeThema');

BX.sfz.General.ChangeThema = {
    userid: null,
    init: function(usertype) {
        this.userid = BX.message('USER_ID')
        console.log(this.userid)
        if(usertype==1) {
            //console.log("here")
            $("#user-block").attr("onClick","BX.sfz.General.ChangeThema.hideHandler()");
            
            //$("#user-block").click(function (event) {
            //    event.preventDefault()
            //    console.log("click")
            //})
        }
        //BX.addCustomEvent('BX.UI.ActionPanel:created', BX.delegate(this.hideHandler, this));
        //BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
        /*switch(type) {
            case 'detail':
                BX.addCustomEvent('BX.Crm.EntityEditor:onInit', BX.delegate(this.detailHandler, this));
                BX.addCustomEvent('BX.Crm.EntityEditorSection:onLayout', BX.delegate(this.detailHandler, this));
                break;
            case 'kanban':
                BX.addCustomEvent('Kanban.Grid:onRender', BX.delegate(this.kanbanHandler, this));
                break;
        }*/
        //console.log(usertype); 
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
                    href: '/company/personal/user/${useridval}/'
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
        //console.log("window")
        //console.log(ActionPanel)
        //console.log(param1)
        //console.log(param2)
        //console.log("click")
        
        //$(function(){
            //onPageLoad();
        //$.holdReady( false );
        //var elem = $("#menu-popup-user-menu")
        //var elemsTotal = elems.length;
        //console.log(elem); 
        //$(".menu-popup-item-text").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
        //    console.log($(el).text());
            
        //});
        //});
    },
    /*detailHandler: function(par1, par2) {
        console.log("detailed")
        console.log(par1)
        console.log(par2)
    }*/
}
//$.holdReady( true );
//BX.sfz.General.ChangeThema.init();

function showUsercutmenu()
{
    /*var bindElement = BX("user-block");
		BX.addClass(bindElement, "user-block-active");
		BX.PopupMenu.show("user-menu", bindElement, [
			{
				text : "Моя страница",
				className : "menu-popup-no-icon",
				href: "/company/personal/user/35/"
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
		});*/
}