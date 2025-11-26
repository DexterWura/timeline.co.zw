function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
        
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        modal.setAttribute('aria-modal', 'true');
        
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            setTimeout(() => firstFocusable.focus(), 100);
        }
        
        requestAnimationFrame(() => {
            setTimeout(() => {
                modal.classList.add('modal-show');
            }, 10);
        });
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('modal-show');
        modal.setAttribute('aria-hidden', 'true');
        modal.setAttribute('aria-modal', 'false');
        
        const scrollY = document.body.style.top;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal(event.target.id);
    }
}, { passive: true });

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const openModal = document.querySelector('.modal.modal-show');
        if (openModal) {
            closeModal(openModal.id);
        }
    }
}, { passive: true });

document.addEventListener('keydown', function(event) {
    const modal = document.querySelector('.modal.modal-show');
    if (!modal || event.key !== 'Tab') return;
    
    const focusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    if (event.shiftKey) {
        if (document.activeElement === firstElement) {
            event.preventDefault();
            lastElement.focus();
        }
    } else {
        if (document.activeElement === lastElement) {
            event.preventDefault();
            firstElement.focus();
        }
    }
}, { passive: false });

