function formatUangInput(input) {
    // Ambil nilai dari input dan hilangkan karakter selain angka
    let angka = input.value.replace(/\D/g, "");

    // Format ke ribuan dengan titik sebagai pemisah
    let formatted = new Intl.NumberFormat("id-ID").format(angka);

    // Masukkan kembali ke input
    input.value = formatted;
}
