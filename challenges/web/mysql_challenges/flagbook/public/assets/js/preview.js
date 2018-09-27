function readURL(input) {
	alert(1);

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#file_preview').attr('src', e.target.result);
      $('#file_preview').attr('display', "default");
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#post_file").change(function() {
  readURL(this);
});