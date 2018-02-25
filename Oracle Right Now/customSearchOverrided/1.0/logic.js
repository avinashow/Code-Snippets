RightNow.namespace('Custom.Widgets.warranty.report.otherdealers.customSearchOverrided');
Custom.Widgets.warranty.report.otherdealers.customSearchOverrided = RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
		console.log(this);				
		this._searchButton = this.Y.one(this.baseSelector + "_Button");
		this._searchButton.on("click",this._getData,this);
    },

    /**
     * Sample widget method.
     */
    _getData: function() {
		
		RightNow.Ajax.makeRequest('/cc/AjaxCustom/checkSessionTimeOut', {},  {				   
					successHandler: function(response) {
					
					   
					    //alert(response);
						if(response.c_id=='0')
						{ 
						   location.reload();
						   halt=true;
						}
						
						else
						{
						   var data = {};
		var formData = $("#keywordsearch").serializeArray();
		console.log(formData);
		if (this.data["attrs"]["name"] != "other_dealer_assets") {
			var org = {};
			org["name"] = "org_id";
			org["value"] = orgID;
			formData.push(org);
		}
		
		/*var transferAssets = {};
		transferAssets["name"] = "transferredAssets";
		transferAssets["value"] = orgName;
		formData.push(transferAssets);  */
		
		data["form"] = JSON.stringify(formData);
		data["f_tok"] = this.data.js.f_tok;
       	
        
        var eventObject = new RightNow.Event.EventObject(this, {data: {
            response:'',
            key:''
        }});   
		if (this.data.attrs.name == "other_dealer_assets" || this.data.attrs.name == "contactrequest") {
			eventObject.data.key = formData[0]["value"];
			eventObject.data.response = formData[1]["value"];
			RightNow.Event.fire("searchKeyword",eventObject);
			RightNow.Event.fire("evt_searchreport", eventObject);
		} else {
        $.ajax({
            url:"/cc/KeywordSearchController/getAssets",
            data:data, 
            type:'POST',
            dataType:'json'
        }).done(function(response) {
			
        	console.log(response);
            eventObject.data = response;
            RightNow.Event.fire("keywordSearchResponse",eventObject);
        }).error(function(response) {
            alert("got error");
        });
        }
						}
	
					},
					scope: this,
					json: true,
					type: "POST",
					}); 
		
		
		
		
		
    }
});