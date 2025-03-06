$(document).ready(function () {
    let pelangganDipilih = false; // Flag untuk memastikan pelanggan dipilih

    // AUTOCOMPLETE saat user mengetik
    $("#nama_pelanggan").on("input", function () {
        let query = $(this).val();
        if (query.length > 2) {
            $.ajax({
                url: cekPelangganAuto, // Menggunakan variabel dari Blade
                type: "GET",
                data: { query: query },
                success: function (data) {
                    let suggestions = data.map(
                        (p) => `
                        <div class="suggestion-item p-2 border-bottom"
                             data-nama="${p.nama}" 
                             data-alamat="${p.alamat}">
                            ${p.nama}
                        </div>
                    `
                    );
                    $("#autocomplete-list").html(suggestions.join(""));
                    $("#autocomplete-list").show();
                },
            });
        } else {
            $("#autocomplete-list").hide();
        }
    });

    // PILIH NAMA dari hasil autocomplete
    $(document).on("click", ".suggestion-item", function () {
        let nama = $(this).data("nama");
        let alamat = $(this).data("alamat");

        $("#nama_pelanggan").val(nama);
        $("#alamat_pelanggan").val(alamat).prop("readonly", true);
        $("#status_pelanggan")
            .text("Pelanggan Terdaftar")
            .removeClass("text-danger")
            .addClass("text-success");
        $("#autocomplete-list").hide();

        pelangganDipilih = true; // Tandai bahwa pelanggan telah dipilih
    });

    // SEMBUNYIKAN autocomplete jika klik di luar
    $(document).click(function (event) {
        if (
            !$(event.target).closest("#nama_pelanggan, #autocomplete-list")
                .length
        ) {
            $("#autocomplete-list").hide();
        }
    });

    // CEK BUTTON jika user tidak memilih dari autocomplete
    $("#cek_button").on("click", function (event) {
        event.preventDefault();
        let namaPelanggan = $("#nama_pelanggan").val();

        if (namaPelanggan.length > 2) {
            $.ajax({
                url: cekPelangganUrl, // Menggunakan var dari Blade
                method: "GET",
                data: { nama_pelanggan: namaPelanggan },
                success: function (response) {
                    if (response.ada) {
                        $("#status_pelanggan")
                            .text("Pelanggan ditemukan")
                            .removeClass("text-danger")
                            .addClass("text-success");
                        $("#alamat_pelanggan")
                            .val(response.alamat)
                            .prop("readonly", true);
                    } else {
                        $("#status_pelanggan")
                            .text("Pelanggan belum ditemukan")
                            .removeClass("text-success")
                            .addClass("text-danger");
                        $("#alamat_pelanggan").val("").prop("readonly", false);
                    }

                    $("#input_create").show(); // Form hanya muncul setelah tombol diklik
                },
            });
        } else {
            alert("Masukkan minimal 3 huruf nama pelanggan.");
        }
    });
});
