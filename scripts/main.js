console.log("hello from main.js");

const myName = "sithara sathsarani";
console.log("the script for", myName, "is running.")

const mainHeading = document.querySelector('h1');
const aboutSection = document.querySelector('#about');

console.log(mainHeading);
console.log(aboutSection);

const themeToggleButton = document.querySelector('#theme-toggle');
const bodyElement = document.querySelector('body');


function handleThemeToggle() {
    bodyElement.classList.toggle ('dark-mode');
}
themeToggleButton.addEventListener('click',handleThemeToggle) ;   

// Contact form AJAX submit + client-side UX
(function () {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const statusEl = document.getElementById('formStatus');
    const submitBtn = form.querySelector('button[type="submit"]');

    function setStatus(text, type) {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.classList.remove('success', 'error');
        if (type) statusEl.classList.add(type);
    }

    form.addEventListener('submit', function (ev) {
        ev.preventDefault();
        if (!submitBtn) return;

        // Basic client-side validation
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const message = form.message.value.trim();
        if (!name || !email || !message) {
            setStatus('Please fill in name, email and message.', 'error');
            return;
        }

        submitBtn.disabled = true;
        setStatus('Sending...', null);

        const data = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: data,
            headers: { 'Accept': 'application/json' }
        }).then(async (res) => {
            const ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') !== -1) {
                const json = await res.json();
                if (json.ok) {
                    setStatus('Message sent — thank you!', 'success');
                    form.reset();
                } else {
                    setStatus((json.errors || ['Submission failed']).join(' '), 'error');
                }
            } else {
                // Fallback: if server returns HTML, redirect to it
                const text = await res.text();
                document.open();
                document.write(text);
                document.close();
            }
        }).catch((err) => {
            console.error(err);
            setStatus('Network error. Please try again later.', 'error');
        }).finally(() => { submitBtn.disabled = false; });
    });
})();
