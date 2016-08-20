$(document).ready(function() {
    $('#example').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
        	url:"http://codeigniter.dev/hmvci/rahendz/index.php/welcome/data_record",
        	type: "post"
        }
    } );
} );