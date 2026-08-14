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

// ──────────────────────────────────────────────────
// SWEETALERT2 — pengganti alert() & confirm() native
// ──────────────────────────────────────────────────

/**
 * Konfirmasi hapus — auto-bind ke form dengan class .form-delete
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus data?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i>Ya, Hapus',
                cancelButtonText: '<i class="fas fa-times mr-1"></i>Batal',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: true,
                customClass: {
                    popup: 'swal-popup-custom',
                    title: 'swal-title-custom',
                    confirmButton: 'btn btn-danger btn-sm',
                    cancelButton: 'btn btn-secondary btn-sm ml-2'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

/**
 * Konfirmasi generik — panggil manual: swalConfirm({...}).then(...)
 *
 * @param {Object} options
 *   title, text, icon, confirmText, cancelText, confirmColor
 */
function swalConfirm(options) {
    options = options || {};
    return Swal.fire({
        title: options.title || 'Konfirmasi',
        text: options.text || 'Lanjutkan?',
        icon: options.icon || 'question',
        showCancelButton: true,
        confirmButtonColor: options.confirmColor || '#121358',
        cancelButtonColor: '#858796',
        confirmButtonText: options.confirmText || 'Ya',
        cancelButtonText: options.cancelText || 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'swal-popup-custom',
            confirmButton: 'btn btn-primary btn-sm',
            cancelButton: 'btn btn-secondary btn-sm ml-2'
        }
    });
}

/**
 * Alert sukses — pengganti alert('Sukses!')
 */
function swalSuccess(title, text) {
    return Swal.fire({
        title: title || 'Berhasil!',
        text: text || '',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'swal-popup-custom' }
    });
}

/**
 * Alert error
 */
function swalError(title, text) {
    return Swal.fire({
        title: title || 'Gagal!',
        text: text || 'Terjadi kesalahan.',
        icon: 'error',
        confirmButtonColor: '#e74a3b',
        confirmButtonText: 'Tutup',
        customClass: {
            popup: 'swal-popup-custom',
            confirmButton: 'btn btn-danger btn-sm'
        }
    });
}

/**
 * Alert info
 */
function swalInfo(title, text) {
    return Swal.fire({
        title: title || 'Informasi',
        text: text || '',
        icon: 'info',
        timer: 4000,
        showConfirmButton: false,
        customClass: { popup: 'swal-popup-custom' }
    });
}

/**
 * Toast notification (pojok kanan atas)
 */
function swalToast(icon, title) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: function (toast) {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        customClass: { popup: 'swal-toast-custom' }
    });
    return Toast.fire({ icon: icon, title: title });
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

// ──────────────────────────────────────────────────────────────
// MOBILE OFF-CANVAS DRAWER (< 768px)
// sb-admin-2.js otomatis menambah sidebar-toggled/toggled di bawah
// 480px pada SETIAP event resize (show/hide URL bar). Handler kita
// berjalan SETELAH-nya (custom.js di-load setelah sb-admin-2.min.js)
// dan membersihkannya kecuali user memang membuka drawer.
// ──────────────────────────────────────────────────────────────
(function () {
    var isMobile = function () { return window.innerWidth < 768; };

    // 1) Menangkal handler resize sb-admin-2.js: hapus class auto-added.
    //    (dipertahankan bila ada penanda drawer-user-open)
    $(window).on('resize', function () {
        if (isMobile() && !$('body').hasClass('drawer-user-open')) {
            $('body').removeClass('sidebar-toggled');
            $('.sidebar').removeClass('toggled');
        }
    });

    // 2) Ganti handler tombol hamburger (elemen khusus mobile — d-md-none).
    //    .off() melepas handler langsung sb-admin-2.js agar tidak toggle ganda.
    $('#sidebarToggleTop').off('click');
    $('#sidebarToggleTop').on('click', function (e) {
        e.preventDefault();
        var open = !$('body').hasClass('sidebar-toggled');
        $('body').toggleClass('sidebar-toggled', open)
            .toggleClass('drawer-user-open', open);
        $('.sidebar').toggleClass('toggled', open);
        if (open) {
            $('.sidebar .collapse').collapse('hide');   // samakan perilaku SB
        }
    });

    // 3) Klik backdrop → tutup drawer
    $(document).on('click', '.sidebar-backdrop', function () {
        $('body').removeClass('sidebar-toggled drawer-user-open');
        $('.sidebar').removeClass('toggled');
    });

    // 4) Klik link menu sungguhan → tutup drawer; toggle submenu
    //    (data-toggle="collapse") TIDAK boleh menutup drawer,
    //    agar submenu tetap bisa di-expand di dalam drawer.
    $(document).on('click',
        '.sidebar .collapse-item, .sidebar a.nav-link:not([data-toggle="collapse"])',
        function () {
            if (isMobile() && $('body').hasClass('sidebar-toggled')) {
                $('body').removeClass('sidebar-toggled drawer-user-open');
                $('.sidebar').removeClass('toggled');
            }
        });

    // 5) Kembali ke desktop: buang penanda (desktop memakai perilaku
    //    toggle asli SB, termasuk #sidebarToggle bawah).
    $(window).on('resize', function () {
        if (!isMobile()) {
            $('body').removeClass('drawer-user-open');
        }
    });
})();
