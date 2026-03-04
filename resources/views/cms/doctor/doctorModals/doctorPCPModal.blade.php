<style>
    .selectize-dropdown .description {
        display: block;
        font-size: medium;
        color: #888; /* Light color for additional details */
    }
 
    .selectize-dropdown .Code {
        display: block;
        font-size: smaller;
        color: #888; /* Light color for Code */
    }
 </style>
 
 <form id="pastqueueEditOr" class="form-horizontal" role="form" method="POST" action="" autocomplete="off">
     <input type="hidden" name="_method" value="PUT">
     <input type="hidden" name="_token" value="{{ csrf_token() }}">
 
     <div class="modal-cms-header">
         <div class="">
             <label for="attending" style="color: red">* Select Attending Physician *</label>
             <input type="hidden" name="AttendingName" class="form-control" placeholder="Select a physician">
             <input type="hidden" name="AttendingId" class="form-control" >
             <select name="Attending" class="form-control">
                 <option value="" disabled selected>Select a physician</option>
             </select>
         </div>
     </div>
 </form>
 
 <script>
 $(document).ready(function () {
    // Initialize Selectize for the dropdown
    var select = $('select[name="Attending"]').selectize({
        valueField: 'Code', 
        labelField: 'FullName', 
        searchField: ['FullName', 'Id'], 
        create: false,
        options: @json($Physician),
        load: function (query, callback) {
            if (!query.length) return callback();
            $.ajax({
                url: '/api/physicians',
                type: 'GET',
                dataType: 'json',
                data: { q: query },
                error: function (xhr) {
                    console.error("Failed to fetch data:", xhr.responseText || xhr.statusText);
                    callback();
                },
                success: function (res) {
                    callback(res);
                }
            });
        },
        render: {
            option: function (item, escape) {
                console.log(item); // Debugging
                return (
                    '<div>' +
                        '<span class="name">' + escape(item.FullName) + '</span>' +
                        '<span class="description"><medium>' + escape(item.Description || '') + '</medium></span>' +
                        '<span class="Code"><small>' + escape(item.Code) + '</small></span>' +
                    '</div>'
                );
            }
        },
        onChange: function (value) {
            var selected = this.options[value];
            $('input[name="AttendingName"]').val(selected ? selected.FullName : '');
            $('input[name="AttendingId"]').val(selected ? selected.Id : '');
        }
    });
});
 </script>
 