$(document).ready(function () {
    $("#cek_button").on("click", function (event) {
        let namaPelanggan = $("#nama_pelanggan").val();

        const cek = document.getElementById("cek_button");
        cek.style.display = "none";

        if (namaPelanggan.length > 2) {
            $.ajax({
                url: cekPelangganUrl,
                method: "GET",
                data: { nama_pelanggan: namaPelanggan },
                success: function (response) {
                    $("#input_create").show(); // Munculkan form tambahan

                    if (response.ada) {
                        $("#status_pelanggan").text("Pelanggan ditemukan");
                        $("#alamat_pelanggan")
                            .val(response.alamat)
                            .prop("readonly", true);
                    } else {
                        $("#status_pelanggan").text(
                            "Pelanggan belum ditemukan"
                        );
                        $("#alamat_pelanggan").val("").prop("readonly", false);
                    }
                },
            });
        } else {
            alert("Masukkan minimal 3 huruf nama pelanggan.");
        }
    });
});
