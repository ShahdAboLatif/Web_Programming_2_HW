
// AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addStudentForm');
    if (!form) return; 

    const ajaxMessage = document.getElementById('ajax-message');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); 

        // browser check for empty fields
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();

        if (!name || !email || !phone || !address) {
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>Please fill in all required text fields.</div>";
            return;
        }

        // form data for AJAX transmission
        const formData = new FormData(form);

        // Send form Data via fetch API 
        fetch('add_student.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                ajaxMessage.innerHTML = `<div class='alert alert-success'>${data.message} <a href='view_students.php'>View Students</a></div>`;
                form.reset(); 
            } else {
                ajaxMessage.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ajaxMessage.innerHTML = "<div class='alert alert-danger'>An unexpected error occurred. Please try again.</div>";
        });
    });
});