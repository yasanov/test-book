(function() {
    'use strict';

    let authorsSelectedIds = [];
    let authorsListUrl = '';
    let authorsCurrentPage = 1;
    let authorsTotalPages = 1;

    const loadAuthors = (page = 1, append = false) => {
        $.ajax({
            url: authorsListUrl,
            data: { page },
            dataType: 'json',
            success: (data) => {
                let html = '';

                data.items.forEach((author) => {
                    const checked = authorsSelectedIds.indexOf(author.id) !== -1 ? 'checked' : '';
                    html += `<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="author_ids[]" value="${author.id}" id="author_${author.id}" ${checked}>
                        <label class="form-check-label" for="author_${author.id}">${author.full_name}</label>
                    </div>`;
                });

                if (append) {
                    $('#authors-list').find('.load-more-btn').remove();
                    $('#authors-list').append(html);
                } else {
                    $('#authors-list').html(html);
                }

                authorsCurrentPage = data.pagination.page;
                authorsTotalPages = data.pagination.pageCount;

                if (authorsCurrentPage < authorsTotalPages) {
                    const btnHtml = `<button type="button" class="btn btn-sm btn-link load-more-btn" onclick="loadAuthors(${authorsCurrentPage + 1}, true)">Загрузить еще</button>`;
                    $('#authors-list').append(btnHtml);
                }
            },
            error: () => {
                $('#authors-list').html('<p class="text-danger">Ошибка загрузки авторов</p>');
            }
        });
    };

    window.loadAuthors = loadAuthors;

    $(document).ready(() => {
        const $authorsList = $('#authors-list');
        if ($authorsList.length) {
            authorsSelectedIds = JSON.parse($authorsList.data('selected-ids') || '[]');
            authorsListUrl = $authorsList.data('list-url') || '';

            if (authorsListUrl) {
                loadAuthors(1, false);
            }
        }
    });
})();
