document.addEventListener('DOMContentLoaded', () => {
    let i, td, t;
    const b = document.querySelectorAll('.ci-status.ci-pending, .ci-status.ci-running'),
        rt = document.querySelector('input[name=REQUEST_TOKEN]').value;
    for (i = 0; i < b.length; i++) {
        td = b[i].parentNode;
        if ('tl_file_list' !== td.className) return;

        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    td.innerHTML = xhr.responseText;
                } else {
                    t = document.createElement('p');
                    t.className = 'error';
                    t.innerHTML = 'An error occurred refreshing the state of the pipeline.';
                    td.insertBefore(t, td.firstChild);
                }
            }
        };

        const data = new FormData();
        data.append('action', 'update-ci-label');
        data.append('pipeline', 80752746);
        data.append('REQUEST_TOKEN', rt);

        xhr.open('POST', window.location.href, true);
        xhr.send(data);
    }

}, false);
