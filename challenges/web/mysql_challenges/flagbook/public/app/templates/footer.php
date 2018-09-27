<!-- footer -->
<footer class="container text-center">
    <ul class="nav nav-pills pull-right">
        <li>Flagbook - Made by jfgauron</li>
    </ul>
</footer>
<!-- ./footer -->
<!--
<script src="/assets/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
-->
<script src="/assets/js/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>

function display_errors(errors) {
    errorMsg = "<ul>";
    errors.forEach(function(elem) {
        errorMsg += "<li>" + elem + "</li>"
    });
    errorMsg += "</ul>";
    $('#myModalBody').html(errorMsg);
    $('#myModal').modal('show')
}

function submit_form(url, form, extras, callback) {
    var form = $('#'+form)[0];
    var formData = new FormData(form);
    for (var key in extras) {
        formData.append(key, extras[key]);
    }

    $.ajax({
        url: url,
        data: formData,
        dataType: "json",
        type: 'POST',
        cache: false,
        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
        processData: false, // NEEDED, DON'T OMIT THIS
        success: function(resp) {
            if (resp.status) {
                callback(resp);
            } else {
                display_errors(resp.messages);
            }
        }
    });
}
</script>