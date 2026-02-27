document.addEventListener('DOMContentLoaded', function() {
    const clientStatusEl = document.getElementById('bc-client-status');
    if (!clientStatusEl) {
        return;
    }

    const url = 'https://barcodeapi.org/limiter/';

    fetch(url)
        .then(function(response) {
            if (!response.ok) {
                throw new Error(wpBarcodeApiL10n.networkError + ' ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            let html = '<ul>';
            html += '<li><strong>' + wpBarcodeApiL10n.ip + '</strong> ' + (data.caller || wpBarcodeApiL10n.unknown) + '</li>';
            html += '<li><strong>' + wpBarcodeApiL10n.consumed + '</strong> ' + (data.tokenSpend || 0) + ' / ' + (data.tokenLimit || 0) + '</li>';
            html += '<li><strong>' + wpBarcodeApiL10n.status + '</strong> ' + (data.enforce ? '<span class="status-blocked">' + wpBarcodeApiL10n.blocked + '</span>' : '<span class="status-active">' + wpBarcodeApiL10n.active + '</span>') + '</li>';
            html += '</ul>';
            clientStatusEl.innerHTML = html;
        })
        .catch(function(err) {
            clientStatusEl.innerText = wpBarcodeApiL10n.loadingError + ' ' + err.message;
        });
});
