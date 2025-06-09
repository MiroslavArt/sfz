BX.namespace('sfz.crm.pipeline');

BX.sfz.crm.pipeline = {
    colors: null,
    data: null,
    category: null,
    _chart: null,
    subscribed: false,
    componentName: '',
    init: function(params, comp) {
       this.items = params.items
       this.colors = params.colors
       this.category = params.category
       this.componentName = comp
       console.log(comp)
       
       if(this.subscribed==false) {
            console.log("subscribe")
            BX.addCustomEvent("onPullEvent-crm", BX.delegate(this.onPullEvent, this));
            this.subscribed = true
       }
       this.buildPipeline()
    },
    buildPipeline: function(){
        this._chart = AmCharts.makeChart("pipeline",
			{
				"type": "funnel",
				"theme": "none",
                "balloonText": "[[title]]:<b>[[value]]</b>",
				"titleField": "title",
				"valueField": "value",
				"dataProvider": this.items,
                "colors": this.colors,
				"labelPosition": "right",
				"depth3D": 160,
				"angle": 16,
				"outlineAlpha": 2,
				"outlineColor": "#FFFFFF",
				"outlineThickness": 2,
				"startY": -400,
				"marginRight": 240,
				"marginLeft": 10,
				"balloon": { "fixedPosition": true },
                "autoDisplay": true
			}   
		);
		
    },
	onPullEvent: function(command, data) {
        if(command === 'crm_sfz_pipeline_update') {
            if(data.hasOwnProperty('CATEGORY_ID')) {
                if(data.CATEGORY_ID==this.category) {
                    console.log("begin ajax")
                    console.log(this.componentName)
                    BX.ajax.runComponentAction(
                        this.componentName,
                        'chartget',
                        {
                            mode: 'class',
                            data: {}
                        }
                        )
                        .then(function (response) {
                            console.log(response.data)
                            this._chart.dataProvider = response.data
                            this._chart.validateData()
                            //this.items = response.data
                            //this.buildPipeline()
                        }.bind(this))
                        .catch(function (response) {
                            console.log(response)
                        }.bind(this));

                }
            }
        }
    },
}
