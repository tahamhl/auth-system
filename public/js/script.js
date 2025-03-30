/**
 * Ana JavaScript dosyası
 */

// DOM yüklendikten sonra çalışacak kodlar
document.addEventListener('DOMContentLoaded', function() {
    // Parola göster/gizle fonksiyonu
    setupPasswordToggle();
    
    // Form doğrulama
    setupFormValidation();
    
    // Uyarı mesajlarını otomatik kapat
    setupAlertDismissal();
});

/**
 * Parola göster/gizle düğmesi ekler
 */
function setupPasswordToggle() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(function(field) {
        // Parola alanının içinde bulunduğu form grubunu bul
        const formGroup = field.closest('.form-group');
        
        if (formGroup) {
            // Göster/gizle düğmesini oluştur
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'btn btn-outline-secondary password-toggle';
            toggleButton.innerHTML = '<i class="far fa-eye"></i>';
            toggleButton.title = 'Parolayı göster';
            
            // Input grubunu bulun veya oluşturun
            let inputGroup = field.closest('.input-group');
            
            if (inputGroup) {
                // Mevcut input grubuna düğmeyi ekleyin
                const appendDiv = document.createElement('div');
                appendDiv.className = 'input-group-append';
                appendDiv.appendChild(toggleButton);
                inputGroup.appendChild(appendDiv);
            } else {
                // Yeni bir input grubu oluşturun
                inputGroup = document.createElement('div');
                inputGroup.className = 'input-group';
                
                // Alanı input grubuna taşı
                field.parentNode.insertBefore(inputGroup, field);
                inputGroup.appendChild(field);
                
                // Append div ve düğmeyi ekle
                const appendDiv = document.createElement('div');
                appendDiv.className = 'input-group-append';
                appendDiv.appendChild(toggleButton);
                inputGroup.appendChild(appendDiv);
            }
            
            // Düğme tıklamasını dinle
            toggleButton.addEventListener('click', function() {
                if (field.type === 'password') {
                    field.type = 'text';
                    toggleButton.innerHTML = '<i class="far fa-eye-slash"></i>';
                    toggleButton.title = 'Parolayı gizle';
                } else {
                    field.type = 'password';
                    toggleButton.innerHTML = '<i class="far fa-eye"></i>';
                    toggleButton.title = 'Parolayı göster';
                }
            });
        }
    });
}

/**
 * Form doğrulama ayarları
 */
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Geçersiz alanları vurgula
                const invalidFields = form.querySelectorAll(':invalid');
                invalidFields.forEach(function(field) {
                    field.classList.add('is-invalid');
                    
                    // Hata mesajı ekle
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    
                    if (field.validity.valueMissing) {
                        feedback.textContent = 'Bu alan gereklidir.';
                    } else if (field.validity.typeMismatch && field.type === 'email') {
                        feedback.textContent = 'Lütfen geçerli bir e-posta adresi girin.';
                    } else if (field.validity.tooShort) {
                        feedback.textContent = `En az ${field.minLength} karakter olmalıdır.`;
                    } else if (field.validity.patternMismatch) {
                        feedback.textContent = 'Lütfen istenen formatı kullanın.';
                    }
                    
                    // Hata mesajını ekle
                    const parent = field.parentNode;
                    if (parent.querySelector('.invalid-feedback') === null) {
                        parent.appendChild(feedback);
                    }
                });
            }
            
            form.classList.add('was-validated');
        });
        
        // Alanların geçerliliğini değiştikçe kontrol et
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(function(field) {
            field.addEventListener('input', function() {
                if (field.checkValidity()) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                }
            });
        });
    });
}

/**
 * Uyarı mesajlarını belirli bir süre sonra otomatik olarak kapatır
 */
function setupAlertDismissal() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        // Başarı ve bilgi mesajlarını 5 saniye sonra otomatik kapat
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
            setTimeout(function() {
                const closeButton = alert.querySelector('button.close');
                if (closeButton) {
                    closeButton.click();
                } else {
                    alert.style.display = 'none';
                }
            }, 5000);
        }
    });
} 