function formatUangInput(input) {
    let value = input.value.replace(/\D/g, ""); // Hapus semua non-angka
    let formatted = new Intl.NumberFormat("id-ID").format(value);

    input.value = formatted; // Tampilkan dengan format
    document.getElementById("jumlah_hidden").value = value; // Simpan nilai asli
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
