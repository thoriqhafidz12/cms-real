$(window).on("load", function () {

           
    $(".loading-page").fadeOut(1000);
    $("body").css("overflow-y", "auto", "overflow-x", "hidden");
});
$(window).on("load", function () {

    $("#Moduleloader-page").fadeOut(30);
   
});
$(".navbar").animate({ top: "0px" }, 200);
$(".navbar-left").animate({ left: "0px" }, 200);
$(".bottom-slide").animate({ bottom: "0px" }, 200);

/**
 * Format input angka dengan sparator ribuan (.)
 * Dipanggil via oninput="autoNumericDot(this, 'targetHiddenId')"
 */
function autoNumericDot(el, targetId) {
    let cursorPos = el.selectionStart;
    let oldLength = el.value.length;

    // Hapus semua karakter selain angka
    let val = el.value.replace(/\D/g, '');

    // Format dengan sparator ribuan (.)
    let formatted = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Koreksi posisi kursor setelah formatting
    let newLength = formatted.length;
    let diff = newLength - oldLength;
    el.value = formatted;
    el.selectionStart = el.selectionEnd = cursorPos + diff;

    // Simpan nilai asli (angka saja) ke hidden input
    document.getElementById(targetId).value = val;
}

// TOOLTIP
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});