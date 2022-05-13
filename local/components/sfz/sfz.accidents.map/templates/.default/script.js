BX.ready(function() {
    //var x = 1111;
    //console.log(x)
    var picture = $('#mapwrapper')

    picture.height(picture.width()*0.89);
    $(window).resize(function(){
        picture.height(picture.width()*0.89);
    }); 
    $( ".sfz-map-cell" ).mouseover(function(e) {
        Appear(e.currentTarget)
    });
    $( ".sfz-map-cell" ).mouseout(function(e) {
        Leave(e.currentTarget)
    });
   

    var camelize = function() {
    var regex = /[\W_]+(.)/g
    var replacer = function (match, submatch) { return submatch.toUpperCase() }
    return function (str) { return str.replace(regex, replacer) }
    }()

    var showData = function (data) {
    data.scale = parseFloat(data.scale.toFixed(4))
    for(var k in data) { $('#'+k).html(data[k]) }
    }

    picture.on('load', function() {
    picture.guillotine({ eventOnChange: 'guillotinechange' })
    picture.guillotine('fit')
    for (var i=0; i<5; i++) { picture.guillotine('zoomIn') }

    // Show controls and data
    //$('.loading').remove()
    //$('.notice, #controls, #data').removeClass('hidden')
    showData( picture.guillotine('getData') )

    // Bind actions
    $('#controls a').click(function(e) {
        e.preventDefault()
        action = camelize(this.id)
        picture.guillotine(action)
    })

    // Update data on change
    picture.on('guillotinechange', function(e, data, action) { showData(data) })
    })

    $('#grid').click(function(){
        if ($(this).is(':checked')){
            $('.sfz-map-background').addClass("sfz-map-network")
        } else {
            $('.sfz-map-background').removeClass("sfz-map-network")
        }
    });   
});

function Leave(Element) {
    //console.log(Element)
    popup.close()
    return true;
}

function Appear(Element) {
    //onsole.log(Element)
    var sluchai = $(Element).find("span")
    var text = ''
    $.each(sluchai,function(index,value){

        // действия, которые будут выполняться для каждого элемента массива
        // index - это текущий индекс элемента массива (число)
        // value - это значение текущего элемента массива
        
        //выведем индекс и значение массива в консоль
        text += '<b>' + 'Год ' + $(value).attr('data-year') + '.Категория: ' + $(value).attr('data-tyazh') + 
            '.С: ' + $(value).attr('data-dolzhn') + '.Тип: ' + $(value).attr('data-type') + '</b><br/>' + $(value).text() + '<br/>'
      
    });
    //(Element.getAttribute("data-id")
    popup = BX.PopupWindowManager.create(Element.getAttribute("data-id"), Element, {
        content: text,
        width: 600,
        darkMode: true,
        autoHide: true,
        closeIcon: false
    });

   popup.show();
   return true;
}

