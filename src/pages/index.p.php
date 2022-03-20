<?php 
echo '
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <div class="row height d-flex justify-content-center">
        <div class="col-md-12">
            <h1 class="index">Shared Phone Book</h1>
            <div class="search"> 
                <i class="fa fa-search"></i> 
                <input type="text" id="search_email" class="form-control" placeholder="Search phonebooks by email"> 
                <button class="btn btn-primary searchEmails">Search</button> 
            </div>
            <br>
            <div id="searchData">

            </div> 
        </div>
    </div>
';
?>

<script> 
$('.searchEmails').on('click', function(e) {
    e.preventDefault(); 

    let email = $('#search_email').val(); 

    $.ajax({
        async: true, 
        url: "<?php echo this::$_PAGE_URL ?>" + "action/show/search",
        type: "post", 
        data: {
            email: email
        }, 
        success: function(data) {
            $("#searchData").html(data); 
        }
    });
});
</script> 