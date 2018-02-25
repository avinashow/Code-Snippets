RightNow.namespace('Custom.Widgets.avinash.CustomGenericFormInput');
Custom.Widgets.avinash.CustomGenericFormInput = RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
		if (this.data.attrs.name == "CO.Assets.status") {
			this.input = this.Y.one("#rn_" + this.instanceID + "_option");
			this.input.on("change",function() {
				if (this.input._node.selectedIndex == 1) {
					var eventObject = new RightNow.Event.EventObject(this, {data: {selected: this.input._node.selectedIndex}});
					RightNow.Event.fire("hidefield",eventObject);
				} else {
					var eventObject = new RightNow.Event.EventObject(this, {data: {selected: this.input._node.selectedIndex}});
					RightNow.Event.fire("showfield",eventObject);

				}
			},this);
		} else if (this.data.attrs.name == "CO.Assets.unit_no") {
			RightNow.Event.on("hidefield",this.hideField,this);
			RightNow.Event.on("showfield",this.showfield,this);			
		}
    },
	
	showfield:function(type,args) {
		document.getElementById("rn_" + this.instanceID).style.display = "block";
	},
	
	hideField:function(type,args) {
		document.getElementById("rn_" + this.instanceID).style.display = "none";
	},
	
    /**
     * Sample widget method.
     */
    methodName: function() {

    },

    /**
     * Makes an AJAX request for `default_ajax_endpoint`.
     */
    getDefault_ajax_endpoint: function() {
        // Make AJAX request:
        var eventObj = new RightNow.Event.EventObject(this, {data:{
            w_id: this.data.info.w_id,
            // Parameters to send
        }});
        RightNow.Ajax.makeRequest(this.data.attrs.default_ajax_endpoint, eventObj.data, {
            successHandler: this.default_ajax_endpointCallback,
            scope:          this,
            data:           eventObj,
            json:           true
        });
    },

    /**
     * Handles the AJAX response for `default_ajax_endpoint`.
     * @param {object} response JSON-parsed response from the server
     * @param {object} originalEventObj `eventObj` from #getDefault_ajax_endpoint
     */
    default_ajax_endpointCallback: function(response, originalEventObj) {
        // Handle response
    }
});