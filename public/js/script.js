(function() {
      const toast = document.getElementById('toast-message');
      const closeBtn = document.getElementById('close-toast-btn');
      
      function dismissToast() {
        if (!toast) return;
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => { 
          toast.remove(); 
        }, 300);
      }

      // Starts the 5000ms countdown immediately when this snippet hits the browser
      const autoDismissTimeout = setTimeout(dismissToast, 5000);

      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          clearTimeout(autoDismissTimeout);
          dismissToast();
        });
      }
    })();

    // Apply theme settings on initial hard refresh
    applyTheme();

    function applyTheme() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    function setupThemeToggle() {
        const toggleButtons = document.querySelectorAll('.theme-toggle-btn');
        if (toggleButtons.length === 0) return;

        toggleButtons.forEach(button => {
            // Clean up any old listeners on elements cached by Livewire
            const cleanButton = button.cloneNode(true);
            button.parentNode.replaceChild(cleanButton, button);
            
            // Attach the click toggle logic
            cleanButton.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            });
        });
    }

    // Remount events cleanly when Livewire changes pages
    document.addEventListener('livewire:navigated', () => {
        applyTheme();
        setupThemeToggle();
    });

    // Fallback for initial page launch
    document.addEventListener('DOMContentLoaded', setupThemeToggle);        