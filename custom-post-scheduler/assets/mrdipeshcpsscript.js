document.addEventListener('DOMContentLoaded', function() {
    jQuery.ajax({
        url: mrdipesh_cpsscript_vars.ajax_url, 
        type: 'POST',
        data: {
            action: 'overlayExpired',
            page_id: mrdipesh_cpsscript_vars.page_id,            
            nonce: mrdipesh_cpsscript_vars.nonce
        },
        success: function(response) {
            var response = eval(response);
            var status = response.data.status;
            if(status){
                mrdipeshcpsscript_addExpiredOverlay()
            }
        },
        error: function(xhr, status, error) { 
        }
    });
});

function mrdipeshcpsscript_addExpiredOverlay() {
    if (document.body.classList.contains('expired')) {
        var expiredOverlay = document.createElement('div');
        expiredOverlay.classList.add('expired-overlay');
        
        var diagonalText = document.createElement('div');
        diagonalText.classList.add('diagonal-text');
        diagonalText.innerHTML = 'EXPIRED';
        expiredOverlay.appendChild(diagonalText);
        
        document.body.appendChild(expiredOverlay);
    }
} 