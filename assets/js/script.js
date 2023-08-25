
document.addEventListener('DOMContentLoaded', function () {
    const teamRows = document.querySelectorAll('.team-row');
    teamRows.forEach(row => {
        row.addEventListener('click', function () {
            window.location.href = row.getAttribute('data-url');
        });
    });
});