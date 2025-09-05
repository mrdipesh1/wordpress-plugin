document.addEventListener('DOMContentLoaded', function() {
    console.log("URL: "+ mrdipeshcpsscript_vars.ajax_url) 
    console.log("page_id: "+ mrdipeshcpsscript_vars.page_id)
    jQuery.ajax({
        url: mrdipeshcpsscript_vars.ajax_url, 
        type: 'POST',
        data: {
            action: 'overlayExpired',
            page_id: mrdipeshcpsscript_vars.page_id
        },
        success: function(response) {
            var response = eval(response);
            console.log("response: "+ response) 
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