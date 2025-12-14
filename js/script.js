document.addEventListener('DOMContentLoaded', function() {
    
    const hargaElements = document.querySelectorAll('.harga');
    hargaElements.forEach(el => {
        const text = el.textContent;
        if (text.includes('Rp')) return;
        
        const angka = parseInt(text.replace(/\D/g, ''));
        if (!isNaN(angka)) {
            el.textContent = 'Rp ' + angka.toLocaleString('id-ID');
        }
    });
    
   
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm(this.getAttribute('data-confirm') || 'Apakah Anda yakin?')) {
                e.preventDefault();
            }
        });
    });
    
    
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.transition = 'opacity 0.5s';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    });
    
    
    document.getElementById('currentYear')?.textContent = new Date().getFullYear();
});


function validateForm(formId) {
    const form = document.getElementById(formId);
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#e74c3c';
            isValid = false;
            
            input.addEventListener('input', function() {
                this.style.borderColor = '#ddd';
            });
        }
    });
    
    if (!isValid) {
        alert('Harap isi semua field yang wajib diisi!');
        return false;
    }
    
    return true;
}