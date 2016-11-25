$(document).ready(function() {
    $('[role="datatable"]').DataTable( {
        processing: true,
        serverSide: true,
        ajax: {
        	url:"http://codeigniter.dev/hmvci/rahendz/index.php/welcome/data_record",
        	type: "POST",
        	data: {'draw':1}
        }
    } );
} );