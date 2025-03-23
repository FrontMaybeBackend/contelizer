$(document).ready(function() {
    let currentPage = 1;
    let userId = null;

    function loadPosts(page, userId) {
        let queryParams = {
            page: page
        };

        if (userId) {
            queryParams.user_id = userId;
        }

        $.ajax({
            url: '/api/posts',
            type: 'GET',
            data: queryParams,
            success: function(data) {
                displayPosts(data);
                updatePagination(page);
            },
            error: function() {
                $('#posts-container').html('<div class="alert alert-danger">Wystąpił błąd podczas ładowania postów.</div>');
            }
        });
    }

    function displayPosts(posts) {
        const container = $('#posts-container');
        container.empty();

        if (posts.length === 0) {
            container.html('<div class="alert alert-info">Nie znaleziono postów.</div>');
            return;
        }

        posts.forEach(post => {
            container.append(`
                <div class="post-card">
                    <h5>${post.title}</h5>
                    <small>ID Użytkownika: ${post.user_id}</small>
                    <div class="post-body">${post.body}</div>
                    <div class="mt-3">
                        <a href="/posts/${post.id}" class="btn btn-primary btn-sm">Edytuj</a>
                    </div>
                </div>
            `);
        });
    }

    function updatePagination(page) {
        $('#page-info').text(`Strona ${page}`);
        currentPage = page;
    }

    $('#filter-button').click(function() {
        userId = $('#user-id-input').val() || null;
        loadPosts(1, userId);
    });

    $('#prev-page').click(function() {
        if (currentPage > 1) {
            loadPosts(currentPage - 1, userId);
        }
    });

    $('#next-page').click(function() {
        loadPosts(currentPage + 1, userId);
    });

    loadPosts(currentPage, userId);
});
