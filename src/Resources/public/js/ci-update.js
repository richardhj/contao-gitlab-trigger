document.addEventListener('DOMContentLoaded', () => {

    // Check all pipelines being idle
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

    // Refresh state of idle/running pipelines
    function updateRow(td) {
        const
            id = td.querySelector('.ci-id').innerText,
            rt = document.querySelector('input[name=REQUEST_TOKEN]').value,
            xhr = new XMLHttpRequest(),
            data = new FormData();
        let t;

        if ('tl_file_list' !== td.className) return;

        data.append('action', 'update-ci-label');
        data.append('pipeline', id);
        data.append('REQUEST_TOKEN', rt);

        xhr.onload = function () {
            if (200 === xhr.status) {
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

    // Interval for refresh: 2.5s
    let timerId = setInterval(() => checkRows(), 2500);

}, false);
