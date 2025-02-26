function formatUangInput(input) {
    let angka = input.value.replace(/\D/g, ""); // Hapus karakter selain angka

    // Jika kosong, langsung keluar agar tidak menampilkan "0"
    if (angka === "") {
        input.value = "";
        return;
    }

    // Format angka ke ribuan
    let formatted = new Intl.NumberFormat("id-ID").format(angka);

    // Simpan posisi kursor sebelum mengganti nilai input
    let cursorPos = input.selectionStart;
    let oldLength = input.value.length;

    // Perbarui nilai input dengan format ribuan
    input.value = formatted;

    // Hitung perubahan panjang input setelah format
    let newLength = formatted.length;
    let diff = newLength - oldLength;

    // Sesuaikan posisi kursor agar tetap di tempat yang benar
    input.setSelectionRange(cursorPos + diff, cursorPos + diff);
}

function handleBackspace(event, input) {
    if (event.key === "Backspace") {
        let cursorPos = input.selectionStart;
        if (cursorPos > 0) {
            input.setSelectionRange(cursorPos - 1, cursorPos - 1);
        }
    }
}
function formatUang(number) {
    return "Rp. " + number.toLocaleString("id-ID");
}
