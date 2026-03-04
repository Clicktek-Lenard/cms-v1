
                <div class="modal-cms-header">
                        <div class="col-menu-15 table-items">
                        </div>
                </div>



<script>
let msgQueue = @json($msgQueue ?? []);

$(document).ready(function(e)
{
    var mless = 300;
    if ($(window).width() < 767) mless = 130;
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
    $html += "<tr>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Descriptions</th>";
	$html += "<th>Status</th>";
	@if (strpos(session('userRole'), '"ldap_role":"[HL7BTN]"') !== false)
		$html += "<th>Resend</th>";
	@endif
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $Pack !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-items').append($html);
		
		if( datas.length <= 10)
			$dom = 'frti';
		else
			$dom = 'frtiS';
			
		var table = $('#QueueListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
			columns			: [
            { "data": "Code", "render": function(data, type, row, meta) { return '<div class="wrap-row">' + data + '</div>'; }, "orderable": false   },
			{ "data": "Description", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "Status", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
			@if (strpos(session('userRole'), '"ldap_role":"[HL7BTN]"') !== false)
			{
				data: null,
				orderable: false,
				className: "text-center",
				render: function(data, type, row) {

					const isDisabled = row.Status === "Waived" || row.Status === "Refused" || row.Status === "Cancelled" || row.ItemGroup === "CLINIC";

					// Check if exists in msgQueue
					let isQueued = msgQueue.some(q =>
						q.ItemGroup === row.ItemGroup &&
						q.AccessionNo === row.AccessionNo &&
						q.ReceivedBU === row.ReceivedBU
					);

					if (isQueued) {
						return `
							<button class="btn btn-sm btn-success btnActionPack locked" 
									data-id="${row.Id}">
								<i class="fa fa-check"></i>
							</button>
						`;
					}

					// Default repeat button
					return `
						<button class="btn btn-sm btn-primary btnActionPack"
							data-id="${row.Id}" ${isDisabled ? "disabled" : ""}>
							<i class="fa fa-repeat"></i>
						</button>
					`;
				}
			}
			@endif
			],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{ targets: 0, "width":"70px" , "orderable": false  },
				{ targets: 1, "width":"120px" },
				{ targets: 2, "width":"80px" },
				@if (strpos(session('userRole'), '"ldap_role":"[HL7BTN]"') !== false)
					{ targets: 3, width: "20px", className: "text-center" },
				@endif
			],
			order			: [ 0, 'desc' ],
			dom:            $dom,
			scrollY: $(window).height() - $('.modal-cms-header').height() - mless,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
		
		
});

$(document).on('click', '.btnActionPack', function () {
    let $btn = $(this);
    let id = $btn.data('id');

    $btn.blur();
	
    if ($btn.hasClass('locked')) return;

    $btn.addClass('locked');
    $btn.html('<i class="fa fa-spinner fa-spin"></i>');

	setTimeout(function() {
		$.ajax({
			url: "{{ route('hl7.resend') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				id: id,
				type: 'Package'
			},
			success: function (response) {

                // ✅ success state for clicked button
                $btn
                    .removeClass('btn-primary')
                    .addClass('btn-success locked')
                    .html('<i class="fa fa-check"></i>');


                let dt = $('#QueueListTable').DataTable();

                $('#QueueListTable tbody tr').each(function () {
                    let rowData = dt.row(this).data();
                    if (!rowData) return;

                    if (
                        rowData.ItemGroup === response.ItemGroup &&
                        rowData.AccessionNo === response.AccessionNo &&
                        rowData.ReceivedBU === response.ReceivedBU
                    ) {
                        $(this).find('.btnActionPack')
                            .removeClass('btn-primary')
                            .addClass('btn-success locked')
                            .html('<i class="fa fa-check"></i>')
                            .blur();
                    }
				});

				let parentDt = $('#TransactionListTable', window.parent.document).DataTable();

                $('#TransactionListTable tbody tr', window.parent.document).each(function () {
                    let parentRowData = parentDt.row(this).data();
                    if (!parentRowData) return;

                    if (
                        parentRowData.ItemGroup === response.ItemGroup &&
                        parentRowData.AccessionNo === response.AccessionNo &&
                        parentRowData.ReceivedBU === response.ReceivedBU
                    ) {
                        $(this).find('.btnActionItem')
                            .removeClass('btn-primary')
                            .addClass('btn-success locked')
                            .html('<i class="fa fa-check"></i>')
                            .blur();
                    }
                });
            },
			error: function() {
				$btn
					.html('<i class="fa fa-repeat"></i>')
					.removeClass('locked'); // allow retry
			}
		});
	}, 1000);
});



</script>
