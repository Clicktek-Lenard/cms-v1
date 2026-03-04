
<form id="deletedTransaction" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    
    <div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
   
    <label class="bold ">Queue No:</label>
    </div>
    <div class="col-sm-10 col-md-4">
            <input type="text" class="form-control" value="{{ $data->QCode }}" readonly>
     
    </div>
    
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Queue Date:</label>
    </div>
    <div class="col-sm-10 col-md-4">
    	<input type="text" class="form-control" value="{{ $data->Date }}" readonly>
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden">
    </div>
</div>

<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Full Name:</label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" class="form-control" value="{{ $data->FullName }}" readonly>
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden">
    </div>
</div>


<div class="modal-cms-header">
    <div class="col-menu-15 table-items">
    </div>
</div>



<script>
    $(document).ready(function(e) {
        
        // var mless = 300;
        // if ($(window).width() < 767) mless = 130;
        
        
        $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
        $html += "<thead>";
        $html += "<tr>";
        $html += "<th></th>";
        $html += "<th>Item Code</th>";
        $html += "<th>Item Desc</th>";
        $html += "<th>Status From</th>";
        $html += "<th>Deleted Reason</th>";
        $html += "<th>Deleted By</th>";
        $html += "<th>Deleted Date</th>";
        $html += "</tr>";
        $html += "</thead><tbody>";
            
        var idata = []
        var idatas = {!! json_encode($payHistory) !!};

        if( typeof(idatas.length) === 'undefined')
		    idata.push(idatas);
	    else
		    idata = idatas;

        var $dom = (idata.length >= 11)?"frtiS":"frti";
    
        $html += "</tbody></table></div>";
        $('.table-items').append($html);
    
        var table = $('#ItemListTable').DataTable({
			data			: idata,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-IdQueue', data.IdQueue); },
			columns			: [
			{ "data": null },
			{ "data": "Code", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "Description", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "Status", "render": function(data,type,row,meta) 
            {   
                        if (data === 210) {
                            return '<div class="wrap-row">Fully Paid</div>';
                        } else if (data == 300) {
                            return '<div class="wrap-row">For Specimen</div>';
                        } else if (data == 201) {
                            return '<div class="wrap-row">For Payment</div>';
                        } 
                        
                    return '<div class="wrap-row">' + data + '</div>';
    
            } },
            { "data": "DeletedReason", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "UpdateBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "UpdateDateTime", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
				{ targets: 1, "width":"7px" },
				{ targets: 2, "width":"150px" },
				{ targets: 3, "width":"80px" },
				{ targets: 4, "width":"100px" },
				{ targets: 5, "width":"100px" },
				{ targets: 6, "width":"100px" }
			],
			order			: [ 1, 'desc' ],

			dom:     $dom,
		    scrollY: $(window).height()-378
        });
        $('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
        
    });
</script>
    

