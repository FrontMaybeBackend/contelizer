$(document).ready(function() {
    let currentPage = 1;
    let searchQuery = '';

    function loadUsers(page, search) {
        $.ajax({
            url: '/api/users',
            type: 'GET',
            data: {
                page: page,
                search: search
            },
            success: function (data) {
                console.log('Dane API:', data); // Logujemy dane z API
                displayUsers(data);
                updatePagination(page);
            },
            error: function () {
                $('#users-container').html('<div class="alert alert-danger">Wystąpił błąd podczas ładowania użytkowników.</div>');
            }
        });
    }

    function displayUsers(users) {
        const container = $('#users-container');
        container.empty();

        if (users.length === 0) {
            container.html('<div class="alert alert-info">Nie znaleziono użytkowników.</div>');
            return;
        }

        users.forEach(function(user) {
            container.append(`
        <div class="user-card">
            <h5>${user.name}</h5>
            <p>Email: ${user.email}</p>
            <p>Płeć: ${user.gender}</p>
            <p>Status: ${user.status}</p>
            <a href="/users/${user.id}" class="btn btn-primary btn-sm">Edytuj</a>
            <a href="/posts?user_id=${user.id}" class="btn btn-info btn-sm">Posty</a>
        </div>
    `);
        });
    }

    function updatePagination(page) {
        $('#page-info').text(`Strona ${page}`);
        currentPage = page;
    }

    $('#search-button').click(function() {
        searchQuery = $('#search-input').val();
        loadUsers(1, searchQuery);
    });

    $('#prev-page').click(function() {
        if (currentPage > 1) {
            loadUsers(currentPage - 1, searchQuery);
        }
    });

    $('#next-page').click(function() {
        loadUsers(currentPage + 1, searchQuery);
    });

    loadUsers(currentPage, searchQuery);
});
