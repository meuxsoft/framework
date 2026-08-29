/**
 * PHP 7.3 Static MVC Framework - Client Helpers
 */

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });

    // Image upload preview helper
    const fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    let preview = input.parentNode.querySelector('.image-live-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-live-preview';
                        preview.style.maxHeight = '100px';
                        preview.style.marginTop = '10px';
                        preview.style.borderRadius = '6px';
                        preview.style.border = '1px solid #cbd5e1';
                        input.parentNode.appendChild(preview);
                    }
                    preview.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
