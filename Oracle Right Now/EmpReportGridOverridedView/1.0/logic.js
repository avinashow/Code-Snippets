RightNow.namespace('Custom.Widgets.input.USTrainee.EmpReportGridOverridedView');
Custom.Widgets.input.USTrainee.EmpReportGridOverridedView = RightNow.Widgets.extend({ 
    constructor: function() {
		console.log(this.data.js.dataArrObj);
		this._data = this.data.js.result;
		keyMap = this._getKeyMappings(this._data);
		RightNow.Event.on("evt_searchreport", this.getSearchText,this);
		RightNow.Event.on("evt_searchEmployeeReport", this.getEmployeeType,this);
    },

	getSearchText: function(type, args) {
		var dataResultArray = this._data;
		var searchKey = args[0].data.searchvalue;
		var renderData = {
			tableID: this.baseDomID + '_Grid',
			caption: this.data.attrs.label_caption,
			headers: [],
			rows: []
		};
		for (var i = 1; i < this.data.js.headers.length; i++) {
			td = {label: this.data.js.headers[i].heading};
			renderData.headers.push(td);
		}
		renderData.rows = [];
		if(searchKey.length == 0) {
			renderData.rows = dataResultArray;
		} else {
			for(var key in keyMap) {
				if(key == searchKey) {
					renderData.rows.push(keyMap[key]);
				}
			}
		}
		this._renderData(renderData);
	},
	_getKeyMappings: function(data) {
		var map = {};
		for(var i = 0; i < data.length; i++) {
			for(var j = 0; j < data[i].length; j++) {
					map[data[i][j]] = data[i];
			}
		}
		return map;
	},
	_renderData: function(data) {
		this._contentDiv = this.Y.one(this.baseSelector+"_Grid");
		this._contentDiv.set('innerHTML', new EJS({text: this.data.js.dataTable}).render(data));
	},

	 getEmployeeType: function(type, args){
		 
				var selectedDropDownValue = args[0].data.dropDownValue;
				var renderData = {
                tableID: this.baseDomID + '_Grid',
                caption: this.data.attrs.label_caption,
                headers: [],
                rows: []
            	}
				var salesMap = {};
				var checkBoxdata = [];
				for(var i=0; i < this._data.length; i++){
						for(var j=0; j < this._data[i].length; j++){
							if(j == 9){
							if(this._data[i][j].includes(selectedDropDownValue)){
								checkBoxdata[i] = this._data[i];		
							}
							}
						}
					}
					
				salesCheckBoxResetArr = checkBoxdata.filter(function(){return true;});
				salesMap = this._getSalesKeyMappings(salesCheckBoxResetArr);	
				
				for (var i = 1; i < this.data.js.headers.length; i++) {
	                td = {label: this.data.js.headers[i].heading};			
	                renderData.headers.push(td);
				}
				
				var saleRows = [];
				if(selectedDropDownValue == "~any~") {
					renderData.rows = this._data;
				} else {
				for(var key in salesMap){
						if(salesMap[key].includes(selectedDropDownValue)){
							saleRows.push(salesMap[key]);
							}
						renderData.rows.push(saleRows[key]);		
						}
				}
				this._renderData(renderData);
		 },
		 
		 _getSalesKeyMappings: function(salesData) {
			 	var salesmap = {};
				for(var i=0;i < salesData.length; i++){
						salesmap[i] = salesData[i];
					}
				return salesmap;
			 }
});