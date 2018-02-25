RightNow.namespace('Custom.Widgets.warranty.report.otherdealers.GenericReport');
var activePage = 1;
var  flag = 1;
var order = "both";
var pagination = 0;
var field = "";
var per_page;
var imgurl = "/euf/assets/themes/federal/images/sort_both.png";
var keyword = "";
var key = "";
var that;
Custom.Widgets.warranty.report.otherdealers.GenericReport = RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
		 
    	that = this;
    	
    	per_page = this.data.attrs.per_page;
		
    	if (this.data.attrs.name != "claim" ) {
			
				//alert(this.data.js.totalrecords);
    		var optionsObj = new RightNow.Event.EventObject(this, {data:{
	            options : this.data.js.col_header_mapping
	        }});
    		RightNow.Event.fire("keyselection",optionsObj);
    		var options = this.data.js.col_header_mapping;
    		var eventObject = new RightNow.Event.EventObject(this, {data: {
				key: Object.keys(options)[0]
			}});
			RightNow.Event.fire("placeholderkey",eventObject);

    	this.getPaginatedData("",0,this.data.attrs.per_page,1);
			RightNow.Event.subscribe("searchKeyword",this.searchKey,this);
	    	RightNow.Event.subscribe("keywordSearchResponse",this._renderResponseData,this);
			RightNow.Event.subscribe("paginate",this._renderPaginatedData,this);
			//this._renderResponseData('f');
       }
	   else{
		   
	        RightNow.Event.subscribe("searchKeyword",this.searchKey,this);
	   
	   }
	  
       this.displayTotalRecords();
       	
    },
    displayTotalRecords: function() {
		//alert("11");
		
	    document.getElementById("Initialloading").style.display="block";
		//document.getElementById("Paginator").style.display="none";
		
    	$("#pagecount").text(this.data.js.pagerecords);
       	$("#recordcount").text(this.data.js.totalrecords);
		pagination = Math.ceil(this.data.js.totalrecords/this.data.attrs.per_page);
	
		
		var eventObj = new RightNow.Event.EventObject(this, {data:{
	            pages: pagination,
	            activepage: activePage,
	            paginationby : flag
	     }});
		 RightNow.Event.fire("pagination",eventObj);
		
		 
    },
	
    _renderPaginatedData: function(type,args) {  
	//alert("22");
    	activePage = parseInt(args[0].data.page); 
    	flag = 1; 
    	keyword = args[0].data.keyword;
    	var dict = {};
    	dict["key"] = key;
    	dict["response"] = keyword;
    	//this.getPaginatedData(args[0].data.keyword,(parseInt(args[0].data.page)-1)*10,this.data.attrs.per_page,0);
    	this.getPaginatedData(dict,(parseInt(args[0].data.page)-1)*10,this.data.attrs.per_page,0);		
     },
    
    searchKey: function(type,args) {
		//alert("33");
	    flag = 0;
    	this.getPaginatedData(args[0].data,0, this.data.attrs.per_page,0);
    },
    _renderResponseData: function(type,args) {
		//alert("44");
		
		this._renderFilterData(args[0].data.formData);	
	
	},
	 _renderData: function(data) {
		
		// console.log(data);
		  document.getElementById("Initialloading").style.display="none";
		  //document.getElementById("Paginator").style.display="block";

    	this._contentDiv = this.Y.one(this.baseSelector + "_Grid");
    	this.getStatic().templates.dataTable = '<table id="<%= tableID %>" class="yui3-datatable-table" role="grid"> <caption><%= caption %></caption> <thead class="yui3-datatable-columns">  <tr> <% for (var h=0; h < headers.length; h++) { %> <th class="yui3-datatable-header yui3-datatable-sortable-column" style="<%= headers[h].style %>"><%= headers[h].label %></th> <% } %> </tr>  </thead> <tbody class="yui3-datatable-data">  <% for (var r=0; r < rows.length; r++) { %>  <tr role="row" class="yui3-datatable-<%= (r % 2 === 0) ? "even" : "odd" %>"> <% for (var d=0; d < rows[r].length; d++) { %> <td role="gridcell" class="yui3-datatable-cell"><%= rows[r][d] %></td> <% } %> </tr>  <% } %>  </tbody></table>';
		this._contentDiv.set('innerHTML', new EJS({text: this.getStatic().templates.dataTable}).render(data));
		$("th img").on("click",this.sortData);
	},
	sortData: function() {
		 
		if ($(this).attr("alt") == "both" || $(this).attr("alt") == "desc") {
			document.getElementById($(this).attr("id")).setAttribute("alt","asc");
			document.getElementById($(this).attr("id")).setAttribute("src","/euf/assets/themes/federal/images/sort_asc.png");
			order="asc";
			imgurl = "/euf/assets/themes/federal/images/sort_asc.png";
		} else {
			document.getElementById($(this).attr("id")).setAttribute("alt","desc");
			document.getElementById($(this).attr("id")).setAttribute("src","/euf/assets/themes/federal/images/sort_desc.png");
			order = "desc";
			imgurl = "/euf/assets/themes/federal/images/sort_desc.png";
		}
		field = $(this).attr("id");
		activePage = 1;
		var dict = {};
		dict["key"] = key;
		dict["response"] = keyword;
		alert(field);
		//that.getPaginatedData(keyword,0, per_page,0);
		that.getPaginatedData(dict,0, per_page,0);
	},
	
	_renderFilterData: function(data) {
	
		
			var renderData = {
                tableID: this.baseDomID + '_Grid',
                caption: this.data.attrs.label_caption,
                headers: [],
                rows: []
            };
			for (var i = 0; i < this.data.js.headers.length; i++) {
				var header = this.data.js.headers[i].heading;
				if (this.data.js.col_header_mapping[this.data.js.headers[i].heading] == field) {
					header += '<img alt="' + order +'" id="' + this.data.js.col_header_mapping[this.data.js.headers[i].heading] +'" src="/euf/assets/themes/federal/images/sort_' + order +'.png"';
				} else {
					header += '<img alt="both" id="' + this.data.js.col_header_mapping[this.data.js.headers[i].heading] +'" src="/euf/assets/themes/federal/images/sort_both.png"';
				}
				td = {label: header};
                renderData.headers.push(td);
            }
			
			var response = data;
			//alert(response[0]["totalrecords"]);
			var assetid = this.data.js.assets;
			$("#pagecount").text(response.length);
			if (response.length > 0) {
				document.getElementById("noresults").style.display="none";
				document.getElementById("Paginator").style.display="block";
		
				$("#pagecount").show();
				$("#recordcount").show();
				$("#totalrecords").show();
				$("#pagerecords").show();

				//$("#recordcount").text(response[0]["totalrecords"]);
				$("#recordcount").text(this.data.attrs.totalrecords);
				
				//pagination = Math.ceil(parseInt(response[0]["totalrecords"])/this.data.attrs.per_page);
					pagination = Math.ceil(this.data.js.totalrecords/this.data.attrs.per_page);
			} else {
				document.getElementById("noresults").style.display="block";
				document.getElementById("Paginator").style.display="none";
		
				$("#recordcount").hide();
				$("#pagecount").hide();
				$("#totalrecords").hide();
				$("#pagerecords").hide();
				pagination = 0;
			}	
	
	
		pagination = Math.ceil(this.data.js.totalrecords/this.data.attrs.per_page);
			var eventObj = new RightNow.Event.EventObject(this, {data:{
	            pages: pagination,
	            activepage: activePage,
	            paginationby : flag
	        }});
			RightNow.Event.fire("pagination",eventObj);
			var resplen = response.length;
			for (var j = 0; j < resplen; j++) {
				var row = [];
                for (var i = 0; i < this.data.js.headers.length; i++) {
                	if (this.data.js.headers[i].heading == "Serial Number"){
					
                		if(document.URL.indexOf("my_assets")) {
                			row.push("<a href='/app/warranty/asset_details/a_id/" + assetid[j] +"'>" + response[j][this.data.js.headers[i].heading] + "</a>")
                		} else if(document.URL.indexOf("other_dealer_assets")) {
                			row.push("<a href='/app/warranty/other_assets_details/a_id/" + assetid[j] +"'>" + response[j][this.data.js.headers[i].heading] + "</a>")
                		} else {
                			row.push(response[j][this.data.js.headers[i].heading]);
                		}
                	} else {
						
						if (this.data.js.headers[i].heading == "Account Name")
						{
							var acc_name=response[j][this.data.js.headers[i].heading];
							
							strip_html_acc = acc_name.replace(/<\/?[^>]+(>|$)/g, "")
							
							row.push(strip_html_acc);
						}
						else
						{
							row.push(response[j][this.data.js.headers[i].heading]);
						}
						
						           		
                	}
                }
				renderData.rows.push(row);
			}
			var headlen = this.data.js.headers.length;
			if (resplen == 0) {
				var row = [];
				renderData.rows.push(row.push("No Records"));
			}
			
			this._renderData(renderData);

		},

    getPaginatedData: function(formdata,offset, limit, flg) {
	
    	if (flg == 0) {
		 
       		//$.facebox('<img src="/euf/assets/themes/federal/images/reportloading.gif" style="position:relative;left:35%;" alt="Smiley face" style="margin-left: 50px;" height="200" width="200">')
			 document.getElementById("Initialloading").style.display="block";
			  document.getElementById("rn_"+this.instanceID+"_Content").style.display="none";
			 
		}
		
		var data = {};
		data["key"] = formdata.key;
		key = formdata.key;
		data["offset"] = offset;
		data["limit"] = limit;
		data["keyword"] = formdata.response;
		data["url"] = document.URL;
		data["filter_name"] = this.data.attrs.filter_name;
		//data["filter_data"] = this.data.attrs.filter_data + "," + formdata.response;
		data["filter_data"] = this.data.attrs.filter_data;
		data["report_id"] = this.data.attrs.report_id;
		if (order != "both") {
			data["flag"] = order;
		}
		data["field"] = field;
		if (!document.URL.indexOf("/my_assets") && !document.URL.indexOf("other_dealer_assets")) {
		data["field"] = (field.length > 0)?field.replace('$',''):field;
		}

		if (document.URL.indexOf("/transferred_assets")) {
			data["transferredAssets"] = orgID;
		} 
		if (document.URL.indexOf("/my_claims")) {
			data["transferredAssets"] = orgID;
		}
			if (document.URL.indexOf("/asset_details")) {
			data["transferredAssets"] = orgID;
		}
		
		/*if (document.URL.indexOf("/transferred_assets_details/") || document.URL.indexOf("asset_details") || document.URL.indexOf("machine_transfer") || document.URL.indexOf("other_assets_details")) {
			data["assetid"] = assetid; 
		} */
		var fdata = {};
		
		fdata["form"] = JSON.stringify(data);
		fdata["f_tok"] = that.data.js.f_tok;
		that.getDefault_ajax_endpoint(data);
		
		
		/*var xmlhttp= new XMLHttpRequest();
		xmlhttp.open("POST", "/cc/KeywordSearchController/getPaginatedData");
		
		xmlhttp.onreadystatechange = function() {
		    if (this.readyState == 4 && this.status == 200) {
		      //document.getElementById("demo").innerHTML =
		      //this.responseText;
		      console.log(this.responseText);
		    }
		  };
		
		
		xmlhttp.setRequestHeader("Content-Type", "application/json");
		xmlhttp.send(fdata);*/
		
		
		/*$.ajax({
            url:"/cc/KeywordSearchController/getPaginatedData",
            data:fdata, 
            type:'POST',
            dataType:'json'
        }).done(function(response) {
		     
            var eventObj = new RightNow.Event.EventObject(this, {data:{
	            formData : response.result
	        }});
			

            RightNow.Event.fire("keywordSearchResponse",eventObj);
           	
            $("#facebox").hide();
            $("#facebox_overlay").hide();
        }).error(function(response) {
            //alert("got error");
            $("#facebox").hide();
            $("#facebox_overlay").hide();

        });*/
    },

    /**
     * Makes an AJAX request for `default_ajax_endpoint`.
     */
    getDefault_ajax_endpoint: function(fdata) {
		 //alert("88");
        // Make AJAX request:
       // console.log(fdata);
        var eventObj = new RightNow.Event.EventObject(this, {data:{
            w_id: this.data.info.w_id,
            formData : JSON.stringify(fdata)
			
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
		 //alert("99");
        // Handle response
		console.log(response);
		if(response !== undefined)
		{
			result_data=response.result
		}
		
        var eventObj = new RightNow.Event.EventObject(this, {data:{
	            formData : result_data
				
	        }});
			
            RightNow.Event.fire("keywordSearchResponse",eventObj);
            //$("#facebox").hide();
			
			 document.getElementById("Initialloading").style.display="none";
			 document.getElementById("rn_"+this.instanceID+"_Content").style.display="block";
            $("#facebox_overlay").hide();

    },

    /**
     * Renders the `view.ejs` JavaScript template.
     */
    renderView: function() {
		 //alert("1010");
        // JS view:
        var content = new EJS({text: this.getStatic().templates.view}).render({
            // Variables to pass to the view
            // display: this.data.attrs.display
        });
    }
});
