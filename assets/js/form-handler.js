document.addEventListener('DOMContentLoaded', function() {
    console.log('Custom form script loaded!');

    const contactForm = document.getElementById('contactForm'); // Still get the element
    const successModal = document.getElementById('successModal');
    const modalMessageHeading = document.getElementById('modalMessageHeading');
    const modalMessageText = document.getElementById('modalMessageText');
    const closeButtons = document.querySelectorAll('.close-button, .close-button-alt');

    console.log('Contact form element:', contactForm);

    if (contactForm) {
        // Add a test for the event listener attachment itself
        const testSubmitHandler = function(event) {
            event.preventDefault(); // This is the crucial line
            console.log('--- Submit event fired and default prevented! ---'); // SUPER IMPORTANT LOG

            const formData = new FormData(this);

            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    modalMessageHeading.textContent = 'Success!';
                    modalMessageText.textContent = data.message;
                    contactForm.reset();
                } else {
                    modalMessageHeading.textContent = 'Error!';
                    modalMessageText.textContent = data.message;
                }
                successModal.style.display = 'flex';
            })
            .catch(error => {
                console.error('Error during fetch:', error);
                modalMessageHeading.textContent = 'Error!';
                modalMessageText.textContent = 'A network error or server error occurred. Please try again.';
                successModal.style.display = 'flex';
            });
        };

        // Attach the event listener
        contactForm.addEventListener('submit', testSubmitHandler);
        console.log('Submit event listener ATTACHED to form.'); // Confirm attachment
    } else {
        console.error('ERROR: Contact form with ID "contactForm" not found!');
    }

    // Event listeners for closing the modal (these should still work)
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            successModal.style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target == successModal) {
            successModal.style.display = 'none';
        }
    });
});