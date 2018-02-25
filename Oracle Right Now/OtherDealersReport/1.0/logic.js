RightNow.namespace('Custom.Widgets.warranty.report.otherdealers.GenericReport');
var activePage = 1;
var  flag = 1;
Custom.Widgets.warranty.report.otherdealers.OtherDealersReport= RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
       	this.getPaginatedData("",0,this.data.attrs.per_page);
       	RightNow.Event.on("searchKeyword",this.searchKey,this);
    	RightNow.Event.on("keywordSearchResponse",this._renderResponseData,this);
		RightNow.Event.on("paginate",this._renderPaginatedData,this);
    },
    
    _renderPaginatedData: function(type,args) {  
    	activePage = parseInt(args[0].data.page); 
    	flag = 1; 
    	this.getPaginatedData($("#rn_CustomSearch_3_Text").val(),(parseInt(args[0].data.page)-1)*10,this.data.attrs.per_page);	
     },
    
    searchKey: function(type,args) {
	    flag = 0;
    	this.getPaginatedData(args[0].data.response,0, this.data.attrs.per_page);
    },
    
	 _renderData: function(data) {
	 		
    		this._contentDiv = this.Y.one(this.baseSelector + "_Grid");
    		this.getStatic().templates.dataTable = '<table id="<%= tableID %>" class="yui3-datatable-table" role="grid"> <caption><%= caption %></caption> <thead class="yui3-datatable-columns">  <tr> <% for (var h=0; h < headers.length; h++) { %> <th class="yui3-datatable-header yui3-datatable-sortable-column" style="<%= headers[h].style %>"><%= headers[h].label %></th> <% } %> </tr>  </thead> <tbody class="yui3-datatable-data">  <% for (var r=0; r < rows.length; r++) { %>  <tr role="row" class="yui3-datatable-<%= (r % 2 === 0) ? "even" : "odd" %>"> <% for (var d=0; d < rows[r].length; d++) { %> <td role="gridcell" class="yui3-datatable-cell"><%= rows[r][d] %></td> <% } %> </tr>  <% } %>  </tbody></table>';
			this._contentDiv.set('innerHTML', new EJS({text: this.getStatic().templates.dataTable}).render(data));
		},
		
		_renderFilterData: function(data) {
			var renderData = {
                tableID: this.baseDomID + '_Grid',
                caption: this.data.attrs.label_caption,
                headers: [],
                rows: []
            };
			
			for (var i = 0; i < this.data.js.headers.length; i++) {
                td = {label: this.data.js.headers[i].heading};
                renderData.headers.push(td);
            }
			
			var response = data;
			var assetid = this.data.js.assets;
			$("#pagecount").text(response.length);
			$("#recordcount").text(response[0]["totalrecords"]);
			pagination = Math.ceil(parseInt(response[0]["totalrecords"])/this.data.attrs.per_page);
			var eventObj = new RightNow.Event.EventObject(this, {data:{
	            pages: pagination,
	            activepage: activePage,
	            paginationby : flag
	        }});
			RightNow.Event.fire("pagination",eventObj);
			for (var j = 0; j < response.length; j++) {
				var row = [];
                for (var i = 0; i < this.data.js.headers.length; i++) {
                	row.push(response[j][this.data.js.headers[i].heading]);
                }
				renderData.rows.push(row);
			}
			this._renderData(renderData);

		},
		_renderResponseData: function(type, args) {
			this._renderFilterData(args[0].data.formData);			
		},

    getPaginatedData: function(formdata,offset, limit) {
    	waitingDialog.show('Loading...');
		    setTimeout(function () {
		      waitingDialog.hide();
		    }, 1000);
		var data = {};
		data["offset"] = offset;
		data["limit"] = limit;
		data["keyword"] = formdata;
		var fdata = {};
		fdata["form"] = JSON.stringify(data);
		fdata["f_tok"] = this.data.js.f_tok;
		$.ajax({
            url:"/cc/KeywordSearchController/getPaginatedData",
            data:fdata, 
            type:'POST',
            dataType:'json'
        }).done(function(response) {
            var eventObj = new RightNow.Event.EventObject(this, {data:{
	            formData : response.result
	        }});
            RightNow.Event.fire("keywordSearchResponse",eventObj);
        }).error(function(response) {
            alert("got error");
        });
    },

    /**
     * Makes an AJAX request for `default_ajax_endpoint`.
     */
    getDefault_ajax_endpoint: function(data) {
        // Make AJAX request:
        var eventObj = new RightNow.Event.EventObject(this, {data:{
            w_id: this.data.info.w_id,
            formData : data
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
    },

    /**
     * Renders the `view.ejs` JavaScript template.
     */
    renderView: function() {
        // JS view:
        var content = new EJS({text: this.getStatic().templates.view}).render({
            // Variables to pass to the view
            // display: this.data.attrs.display
        });
    }
});