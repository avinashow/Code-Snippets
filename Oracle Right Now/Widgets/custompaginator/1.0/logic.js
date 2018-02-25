RightNow.namespace('Custom.Widgets.warranty.report.otherdealers.custompaginator');
var first =1;
var last = 10;
var keyword = "";
Custom.Widgets.warranty.report.otherdealers.custompaginator = RightNow.Widgets.extend({ 
    /**
     * Widget constructor.
     */
    constructor: function() {
    	$("#prev").hide();
    	RightNow.Event.subscribe("evt_searchreport",this.setSearchKeword,this);
		 RightNow.Event.subscribe("pagination",this.pages,this);
		 
		 $("#pages").on("click","li",function(){
		 	var eventObj = new RightNow.Event.EventObject(this, {data:{
	            page: $(this).text(),
	            keyword:keyword
	        }});

		 	$("#pages li").each(function(){
		 		$(this).removeClass("active");
		 	});
		 	if (parseInt($(this).text()) == pagination) {
		 		$("#next").hide();
		 	} else {
		 		$("#next").show();
		 	}
		 	if (parseInt($(this).text()) > 1) {
		 		$("#prev").show();
		 	} else {
		 		$("#prev").hide();
		 	}
		 	$(this).addClass("active");
		 	RightNow.Event.fire("paginate",eventObj);
		 });  
		 

		$("#next").on("click",function() {
			$("#prev").show();
			var eventObj = new RightNow.Event.EventObject(this, {data:{
	            page: 0,
	            keyword:keyword
	        }});

			var active = parseInt($("#pages li.active").text());
			if (active == last) {
				first = active;
				last = active + 9;
				if (last > pagination) {
					last = pagination;
				}
				$("#pages").empty();
				if (first != last) {
					for(var i = first; i <= last ; i++) {
			    		var li = $("<li id=" + i +"></li>");
			    		if (i == active) {
			    			li = $("<li class='active' id=" + i +"></li>");
			    		}
			    		$("<a>" + i + "</a>").appendTo(li);
			    		li.appendTo("#pages");
			    	}
				}
			} else {
				$("#pages li").each(function(){
			 		$(this).removeClass("active");
			 	});
			 	active += 1;
			 	$("#"+active).addClass("active");
			 	if (active == pagination) {
			 		$("#next").hide();
			 	}
			 	eventObj.data.page = active;
			 	RightNow.Event.fire("paginate",eventObj);
			 }
		});
		
		$("#prev").on("click",function() {
			var eventObj = new RightNow.Event.EventObject(this, {data:{
	            page: 0,
	            keyword:keyword
	        }});

			var active = parseInt($("#pages li.active").text());
			$("#next").show();
			if (active == first) {
				first = active - 10 + 1;
				last = active;
				$("#pages").empty();
				if (first >= 1) {
					for(var i = first; i <= last ; i++) {
			    		var li = $("<li id=" + i +"></li>");
			    		if (i == active) {
			    			li = $("<li class='active' id=" + i +"></li>");
			    		}
			    		$("<a>" + i + "</a>").appendTo(li);
			    		li.appendTo("#pages");
			    	}
		    	}

			} else {
				$("#pages li").each(function(){
			 		$(this).removeClass("active");
			 	});
			 	active = active - 1;
			 	if (active == 1) {
			 		$("#prev").hide();
			 	}
			 	$("#"+active).addClass("active");
			 	eventObj.data.page = active;
			 	RightNow.Event.fire("paginate",eventObj);
			}

		});
	},
	
	setSearchKeword: function(type,args) {
		document.getElementById("rn_"+this.instanceID).style.display="none";
		keyword = args[0].data.response;
	},
	
    pages: function(type,args) {
		
		document.getElementById("rn_"+this.instanceID).style.display="block";
    	//console.log(args[0].data);
		if (last > args[0].data.pages) {
			last = args[0].data.pages;
		}
		$("#pages").empty();
		if (args[0].data.paginationby == 1) {
			for(var i = first; i <= last ; i++) {
	    		var li = $("<li id=" + i +"></li>");
	    		if (i == parseInt(args[0].data.activepage)) {
	    			li = $("<li class='active' id=" + i +"></li>");
	    		}
	    		$("<a>" + i + "</a>").appendTo(li);
	    		li.appendTo("#pages");
	    	}
    	} else {
    		var pg = 10;
    		if (args[0].data.pages < pg) {
    			pg = args[0].data.pages;
    		}
			
    		//console.log(pg);
    		last = pg;
    		for(var i = 1; i <= pg; i++) {
	    		var li = $("<li id=" + i +"></li>");
	    		if (i == 1) {
	    			li = $("<li class='active' id=" + i +"></li>");
	    		}
	    		$("<a>" + i + "</a>").appendTo(li);
	    		li.appendTo("#pages");
	    	}
	    	first = 1;
			$("#next").show();
    	}
    	if (first == 1 && args[0].data.paginationby == 0) {
			$("#prev").hide();
			$("#next").show();
		}
		if (last == first || args[0].data.pages == 0) {
			$("#prev").hide();
			$("#next").hide();
		}
    }
});