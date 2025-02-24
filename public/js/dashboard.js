function formatUang(number) {
    return "Rp. " + number.toLocaleString("id-ID");
}

document.addEventListener("DOMContentLoaded", function () {
    let saldoElement = document.querySelector(".format");
    let saldo = parseFloat(saldoElement.getAttribute("data-saldo")) || 0;

    saldoElement.innerText = formatUang(saldo);
});
