RightNow.namespace('Custom.Widgets.ui.ProductCategoryTile');
Custom.Widgets.ui.ProductCategoryTile = RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
    	if (this.data.js.categories.row2.length == 0) {
    		$("#toggle-btn").hide();
    		$("#toggle").hide();
    	} else {
    		$("#toggle").show();
    		$("#toggle-btn").show();
    	}
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