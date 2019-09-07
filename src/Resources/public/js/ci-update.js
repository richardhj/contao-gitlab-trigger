document.addEventListener('DOMContentLoaded', () => {

    let timerId = setInterval(() => checkRows(), 2500);

    function checkRows() {
        let i, td;
        const b = document.querySelectorAll('.ci-status.ci-pending, .ci-status.ci-running');

        if (!b.length) {
            // Stop periodic update if no pipelines idle
            clearInterval(timerId);
        }

        for (i = 0; i < b.length; i++) {
            td = b[i].parentNode;

            updateRow(td);
        }
    }

    function updateRow(td) {
        const
            id = td.querySelector('.ci-id').innerText,
            rt = document.querySelector('input[name=REQUEST_TOKEN]').value,
            xhr = new XMLHttpRequest(),
            data = new FormData();

        if ('tl_file_list' !== td.className) return;

        data.append('action', 'update-ci-label');
        data.append('pipeline', id);
        data.append('REQUEST_TOKEN', rt);

        xhr.onload = function () {
            if (xhr.status === 200) {
                td.innerHTML = xhr.responseText;
            } else {
                t = document.createElement('p');
                t.className = 'error';
                t.innerHTML = 'An error occurred refreshing the state of the pipeline.';
                td.insertBefore(t, td.firstChild);
            }
        };

        xhr.open('POST', window.location.href, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    }

}, false);
