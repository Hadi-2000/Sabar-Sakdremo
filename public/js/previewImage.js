function previewImage() {
    var input = document.getElementById("foto_user");
    var preview = document.getElementById("preview");

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
        };

        reader.readAsDataURL(input.files[0]); // Membaca file sebagai URL
    }
}
