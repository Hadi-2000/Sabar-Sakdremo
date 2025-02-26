function formatUang(number) {
    return "Rp. " + number.toLocaleString("id-ID");
}

document.addEventListener("DOMContentLoaded", function () {
    let saldoElement = document.querySelector(".format");
    // Ambil nilai saldo dari attribut data-saldo, jika kosong, ganti dengan 0
    let saldo = parseFloat(saldoElement.getAttribute("data-saldo")) || 0;

    saldoElement.innerText = formatUang(saldo);
});
