function formatUangInput(input) {
    let rawValue = input.value.replace(/\D/g, ""); // Hapus semua non-angka
    let formatted = new Intl.NumberFormat("id-ID").format(rawValue);
    input.value = formatted;

    // Simpan nilai mentah ke input hidden yang sesuai
    if (input.id === "jumlah") {
        document.getElementById("jumlah_hidden").value = rawValue;
    } else if (input.id === "harga_satuan") {
        document.getElementById("harga_satuan_hidden").value = rawValue;
    }
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
